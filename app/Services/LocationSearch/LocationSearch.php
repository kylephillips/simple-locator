<?php 
namespace SimpleLocator\Services\LocationSearch;

use SimpleLocator\Repositories\SettingsRepository;
use SimpleLocator\Services\LocationSearch\LocationResultPresenter;
use SimpleLocator\Helpers;

/**
* Search Locations
*/
class LocationSearch 
{
	/**
	* The request
	* @var array
	*/
	private $request;

	/**
	* Form Data
	* @var array
	*/
	private $data;

	/**
	* Settings Repository
	* @var SimpleLocator\Repositories\SettingsRepository
	*/
	private $settings_repo;

	/**
	* Result Presenter
	* @var SimpleLocator\Services\LocationSearch\LocationResultPresenter
	*/
	private $result_presenter;

	/**
	* Results Fields from Settings
	* @var array
	*/
	private $results_fields;

	/**
	* Query Data
	* @var array
	*/
	private $query_data;

	/**
	* Query - the SQL
	*/
	private $sql;

	/**
	* Query Results
	* @var array
	*/
	private $results;

	/**
	* Total Results (with limit)
	* @var int
	*/
	private $result_count;

	/**
	* Total Results (without limit)
	* @var int
	*/
	private $total_results;

	/**
	* Address Provided
	* @var boolean
	*/
	private $address;

	public function __construct()
	{
		$this->settings_repo = new SettingsRepository;
		$this->result_presenter = new LocationResultPresenter;
	}

	/**
	* Perform the Search
	*/
	public function search($request = null)
	{
		$this->request = ( $request ) ? $request : $_POST;
		$this->setResultsFields();
		$this->setData();
		$this->setAddress();
		$this->setQueryData();
		$this->setQuery();
		$this->runQuery();
	}

	/**
	* Set the results fields
	*/
	private function setResultsFields()
	{
		$this->results_fields = $this->settings_repo->getResultsFieldArray();
	}

	/**
	* Was an address provided
	*/
	private function setAddress()
	{
		$this->address = ( isset($this->request['latitude']) && $this->request['latitude'] != "" && !$this->data['orderby']) ? true : false;
	}

	/**
	 * Sanitize and validate user-submitted data
	 * Ensures all input is safe and within expected ranges/types
	 */
	private function setData()
	{
		// Sanitize and validate unit
		$unit = isset($this->request['unit']) ? sanitize_text_field($this->request['unit']) : get_option('wpsl_measurement_unit');
		$unit = in_array($unit, ['miles', 'kilometers']) ? $unit : 'miles';

		// Sanitize and validate pagination parameters
		$per_page = isset($this->request['per_page']) ? absint($this->request['per_page']) : null;
		$page = isset($this->request['page']) ? absint($this->request['page']) : 1;
		$offset = ($page - 1) * $per_page;
		if ($offset < 0) $offset = 0;

		// Sanitize and validate distance
		$distance = isset($this->request['distance']) ? absint($this->request['distance']) : null;
		if ($distance !== null && $distance < 0) $distance = 0;

		// Sanitize and validate coordinates
		$latitude = isset($this->request['latitude']) ? floatval($this->request['latitude']) : null;
		$longitude = isset($this->request['longitude']) ? floatval($this->request['longitude']) : null;

		// Validate coordinate ranges
		if ($latitude !== null && ($latitude < -90 || $latitude > 90)) {
			$latitude = null;
		}
		if ($longitude !== null && ($longitude < -180 || $longitude > 180)) {
			$longitude = null;
		}

		// Sanitize and validate order parameters
		$orderby = isset($this->request['orderby']) ? sanitize_sql_orderby($this->request['orderby']) : null;
		$order = isset($this->request['order']) ? sanitize_sql_orderby($this->request['order']) : 'DESC';
		$order = in_array(strtoupper($order), ['ASC', 'DESC']) ? strtoupper($order) : 'DESC';

		$this->data = [
			'unit' => $unit,
			'offset' => $offset,
			'limit' => $per_page,
			'distance' => $distance,
			'latitude' => $latitude,
			'longitude' => $longitude,
			'orderby' => $orderby,
			'order' => $order
		];

		// Set taxonomy filters if provided
		if (isset($this->request['taxfilter'])) {
			$this->setTaxonomies();
		}
	}

	/**
	 * Sanitize and validate taxonomy filters
	 * Ensures taxonomy names and term IDs are safe
	 */
	private function setTaxonomies()
	{
		if (!isset($this->request['taxfilter']) || !is_array($this->request['taxfilter'])) {
			return;
		}

		$terms = [];
		foreach ($this->request['taxfilter'] as $taxonomy => $term_ids) {
			// Sanitize taxonomy name
			$taxonomy = sanitize_key($taxonomy);
			// Ensure term IDs are integers
			if (is_array($term_ids)) {
				$term_ids = array_map('absint', $term_ids);
				$term_ids = array_filter($term_ids); // Remove empty values
				if (!empty($term_ids)) {
					$terms[$taxonomy] = $term_ids;
				}
			}
		}
		$this->data['taxonomies'] = $terms;
	}

	/**
	* Set Query Data
	*/
	private function setQueryData()
	{
		global $wpdb;
		$table_prefix = $wpdb->prefix;
		$this->query_data['post_table'] = $table_prefix . 'posts';
		$this->query_data['meta_table'] = $table_prefix . 'postmeta';
		$this->query_data['term_relationship_table'] = $table_prefix . 'term_relationships';
		$this->query_data['distance'] = $this->data['distance'];
		$this->query_data['userlat'] = $this->data['latitude'];
		$this->query_data['userlong'] = $this->data['longitude'];
		$this->query_data['post_type'] = $this->settings_repo->getPostType();
		$this->query_data['lat_field'] = get_option('wpsl_lat_field');
		$this->query_data['lng_field'] = get_option('wpsl_lng_field');
		$this->query_data['diameter'] = ( $this->data['unit'] == "miles" ) ? 3959 : 6371;
		$this->query_data['distance_unit'] = ( $this->data['unit'] == "miles" ) ? 69 : 111.045;		
	}

	/**
	* Set the Field Variables for the SQL using fields chosen in settings
	*/
	private function sqlFieldVars()
	{
		$sql = "";
		foreach($this->results_fields as $key=>$field){
			$fieldname = $field;
			$sql .= ",$fieldname.meta_value AS $fieldname\n";
		}
		return apply_filters('simple_locator_sql_select', $sql, $this->request);
	}

	/**
	* Set the Join statement for field vars in sql using fields chosen in settings
	*/
	private function sqlFieldJoins()
	{
		$sql = "";
		foreach($this->results_fields as $key=>$field){
			$fieldname = $field;
			$sql .= "\nLEFT JOIN " . $this->query_data['meta_table'] . " AS $fieldname
			ON p.ID = $fieldname.post_id AND $fieldname.meta_key = " . "'" . $fieldname . "'" . "\n";
		}
		return apply_filters('simple_locator_sql_join', $sql, $this->request);
	}

	/**
	* Add Taxonomy Joins to limit by taxonomy if available
	*/
	private function taxonomyJoins()
	{
		if ( !isset($this->data['taxonomies']) ) return;
		$sql = "";
		foreach ( $this->data['taxonomies'] as $taxonomy_name => $ids ){
			if ( is_array($ids) && $ids[0] !== '' ){
				$sql .= "\nJOIN " . $this->query_data['term_relationship_table'] . " AS `$taxonomy_name` ON `$taxonomy_name`.object_id = p.ID AND `$taxonomy_name`.term_taxonomy_id IN (" . implode(',', $ids) . ")\n";
			}
		}
		return $sql;
	}

	/**
	* Add the distance variables
	*/
	private function distanceVars()
	{
		$sql = ",lat.meta_value AS latitude,
			lng.meta_value AS longitude";
		if ( !$this->address ) return $sql;
		$sql .= "\n,( " . $this->query_data['diameter'] . " * acos( cos( radians(@origlat) ) * cos( radians( lat.meta_value ) ) 
			* cos( radians( lng.meta_value ) - radians(@origlng) ) + sin( radians(@origlat) ) * sin(radians(lat.meta_value)) ) )
			AS distance\n";
		return $sql;
	}

	/**
	* Set the SQL Limit
	*/
	private function sqlLimit()
	{
		if ( $this->data['limit'] ) {
			$limit = "LIMIT ";
			if ( $this->data['offset'] ) $limit .= $this->data['offset'] . ',';
			$limit .= $this->data['limit'];
			return $limit;
		}
		$limit = $this->settings_repo->resultsLimit();
		if ( $limit == "-1" || $limit == -1) return;
		if ( is_numeric(intval($limit)) ) return "\nLIMIT " . intval($limit);
	}

	/**
	* SQL Where Constraints
	*/
	private function sqlWhere()
	{
		$sql = "";
		if ( $this->address ){
			$sql .= "
			WHERE lat.meta_value
				BETWEEN @origlat - (@distance / @dist_unit)
				AND @origlat + (@distance / @dist_unit)
			AND lng.meta_value
				BETWEEN @origlng - (@distance / (@dist_unit * cos(radians(@origlat))))
				AND @origlng + (@distance / (@dist_unit * cos(radians(@origlat)))) AND";
		} else {
			$sql .= ' WHERE ';
		}
		$sql .= "
			`post_type` = '" . $this->query_data['post_type'] . "'
			AND `post_status` = 'publish'";
		return apply_filters('simple_locator_sql_where', $sql, $this->request);
	}

	/**
	* SQL Order Constraints
	*/
	private function sqlOrderby()
	{
		if ( !$this->data['orderby'] || $this->data['orderby'] == '' ) return;
		$sql = "";
		$orderby = 'p.' . $this->data['orderby'];
		$order = strtoupper($this->data['order']);
		$sql .= "\nORDER BY $orderby $order\n";
		return apply_filters('simple_locator_sql_orderby', $sql, $this->request);
	}

	/**
	 * Build the SQL query using prepared statements and safe values
	 * Prevents SQL injection and ensures only safe data is used
	 */
	private function setQuery($include_limit = true)
	{
		global $wpdb;
		// Prepare the base query with placeholders for meta fields
		$query = "
			SELECT DISTINCT p.post_title AS title, p.ID AS id" .
			$this->sqlFieldVars() . 
			$this->distanceVars() . "
			FROM " . $wpdb->posts . " AS p 
			LEFT JOIN " . $wpdb->postmeta . " AS lat
				ON p.ID = lat.post_id AND lat.meta_key = %s
			LEFT JOIN " . $wpdb->postmeta . " AS lng
				ON p.ID = lng.post_id AND lng.meta_key = %s";
		$query .= $this->sqlFieldJoins();
		$query .= $this->taxonomyJoins();
		$query .= $this->sqlWhere();
		// Prepare the query parameters
		$params = [
			$this->query_data['lat_field'],
			$this->query_data['lng_field']
		];
		if ($this->address) {
			$query .= "\nHAVING distance < %d\nORDER BY distance\n";
			$params[] = $this->query_data['distance'];
		} else {
			$query .= $this->sqlOrderby();
		}
		if ($include_limit) {
			$query .= $this->sqlLimit();
		}
		$query .= ";";
		// Use $wpdb->prepare to safely inject parameters
		$this->sql = $wpdb->prepare($query, $params);
	}

	/**
	 * Run the SQL query and set results
	 * Uses safe SQL variables and prepared statements
	 */
	private function runQuery()
	{
		global $wpdb;
		// Set the SQL Vars for distance calculation if needed
		if ($this->address) {
			$wpdb->query("SET SQL_BIG_SELECTS=1");
			$wpdb->query($wpdb->prepare("SET @origlat = %f;", $this->query_data['userlat']));
			$wpdb->query($wpdb->prepare("SET @origlng = %f;", $this->query_data['userlong']));
			$wpdb->query($wpdb->prepare("SET @distance = %d;", $this->query_data['distance']));
			$wpdb->query($wpdb->prepare("SET @dist_unit = %f;", $this->query_data['distance_unit']));
		}
		// Run the Query
		$results = $wpdb->get_results($this->sql);
		$this->result_count = count($results);
		$this->setResults($results);
		$this->setTotalResults();
	}

	/**
	* Prepare Results
	*/
	private function setResults($results)
	{
		foreach ( $results as $key => $result ) {
			$location = $this->result_presenter->present($result, $key);
			$this->results[] = $location;
		}
	}

	/**
	* Get Total Number of results without pagination
	*/
	private function setTotalResults()
	{
		global $wpdb;

		// Set the SQL Vars
		if ( $this->address ){
			$wpdb->query("SET SQL_BIG_SELECTS=1");
			$wpdb->query("SET @origlat = " . $this->query_data['userlat'] . ";");
			$wpdb->query("SET @origlng = " . $this->query_data['userlong'] . ";");
			$wpdb->query("SET @distance = " . $this->query_data['distance'] . ";");
			$wpdb->query("SET @dist_unit = " . $this->query_data['distance_unit'] . ";");
		}
		
		$this->setQuery(false);
		$results = $wpdb->get_results($this->sql);
		$this->total_results = count($results);
	}

	/**
	* Get Result Count (limit)
	*/
	public function getResultCount()
	{
		return $this->result_count;
	}

	/**
	* Get Result Count (limit)
	*/
	public function getTotalResultCount()
	{
		return $this->total_results;
	}

	/**
	* Get Results
	*/
	public function getResults()
	{
		return $this->results;
	}
}