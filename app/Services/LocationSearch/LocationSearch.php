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
	public function search()
	{
		$this->setResultsFields();
		$this->setAddress();
		$this->setData();
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
		$this->address = ( $_POST['latitude'] != "") ? true : false;
	}

	/**
	* Sanitize and set the user-submitted data
	*/
	private function setData()
	{
		$this->data = array(
			'distance' => sanitize_text_field($_POST['distance']),
			'latitude' => sanitize_text_field($_POST['latitude']),
			'longitude' => sanitize_text_field($_POST['longitude']),
			'unit' => sanitize_text_field($_POST['unit']),
			'offset' => ( isset($_POST['page']) ) ? sanitize_text_field(intval($_POST['page'])) : null,
			'limit' => ( isset($_POST['limit']) ) ? sanitize_text_field(intval($_POST['limit'])) : null
		);
		if ( isset($_POST['taxonomies']) ) $this->setTaxonomies();
	}

	/**
	* Set Taxonomy Filters
	*/
	private function setTaxonomies()
	{
		$terms = $_POST['taxonomies'];
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
		$this->query_data['post_type'] = get_option('wpsl_post_type');
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
		return apply_filters('simple_locator_sql_select', $sql);
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
		return apply_filters('simple_locator_sql_join', $sql);
	}

	/**
	* Add Taxonomy Joins to limit by taxonomy if available
	*/
	private function taxonomyJoins()
	{
		if ( !isset($this->data['taxonomies']) ) return;
		$sql = "";
		foreach ( $this->data['taxonomies'] as $taxonomy_name => $ids ){
			if ( is_array($ids) ){
				$sql .= "\nJOIN " . $this->query_data['term_relationship_table'] . " AS $taxonomy_name ON $taxonomy_name.object_id = p.ID AND $taxonomy_name.term_taxonomy_id IN (" . implode(',', $ids) . ")\n";
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
			$limit .= $this->data['limit'] + 1;
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
				AND @origlng + (@distance / (@dist_unit * cos(radians(@origlat))))";
		}
		$sql .= "
			AND `post_type` = '" . $this->query_data['post_type'] . "'
			AND `post_status` = 'publish'";
		return apply_filters('simple_locator_sql_where', $sql);
	}

	/**
	* Set the Query
	*/
	private function setQuery()
	{
		$sql = "
			SELECT DISTINCT p.post_title AS title, p.ID AS id" .
			$this->sqlFieldVars() . 
			$this->distanceVars() . "
			FROM " . $this->query_data['post_table'] . " AS p 
			LEFT JOIN " . $this->query_data['meta_table'] . " AS lat
				ON p.ID = lat.post_id AND lat.meta_key = '" . $this->query_data['lat_field'] . "'
			LEFT JOIN " . $this->query_data['meta_table'] . " AS lng
				ON p.ID = lng.post_id AND lng.meta_key = '" . $this->query_data['lng_field'] . "'";
			$sql .= $this->sqlFieldJoins();
			$sql .= $this->taxonomyJoins();
			$sql .= $this->sqlWhere();
			if ( $this->address ) $sql .= "\nHAVING distance < @distance\nORDER BY distance\n";
			$sql .= $this->sqlLimit() . ";";
		$this->sql = $sql;
	}

	/**
	* Lookup location data
	*/
	private function runQuery()
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
		
		// Run the Query
		$results = $wpdb->get_results($this->sql);
		$this->result_count = count($results);
		$this->setResults($results);
		if ( $this->data['limit'] ) $this->setTotalResults();
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
		$sql = "
			SELECT DISTINCT p.ID";
			$this->distanceVars();
			$sql .= "\nFROM " . $this->query_data['post_table'] . " AS p";
			$sql .= $this->sqlWhere();
			if ( $this->address ) {
				$sql .= "\nHAVING distance < @distance\n";
			}

		// Set the SQL Vars
		if ( $this->address ){
			$wpdb->query("SET SQL_BIG_SELECTS=1");
			$wpdb->query("SET @origlat = " . $this->query_data['userlat'] . ";");
			$wpdb->query("SET @origlng = " . $this->query_data['userlong'] . ";");
			$wpdb->query("SET @distance = " . $this->query_data['distance'] . ";");
			$wpdb->query("SET @dist_unit = " . $this->query_data['distance_unit'] . ";");
		}
		
		$results = $wpdb->get_results($sql);
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