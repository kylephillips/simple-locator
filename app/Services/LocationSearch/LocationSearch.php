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
	* Total Results
	* @var int
	*/
	private $result_count;

	/**
	* JSON Response
	* @var array
	*/
	private $response;

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
	* Sanitize and set the user-submitted data
	*/
	private function setData()
	{
		$this->data = array(
			'distance' => sanitize_text_field($_POST['distance']),
			'latitude' => sanitize_text_field($_POST['latitude']),
			'longitude' => sanitize_text_field($_POST['longitude']),
			'unit' => sanitize_text_field($_POST['unit']),
		);
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
		$statement = "";
		foreach($this->results_fields as $key=>$field){
			$fieldname = $field;
			$statement .= "$fieldname.meta_value AS $fieldname,\n";
		}
		return $statement;
	}

	/**
	* Set the Join statement for field vars in sql using fields chosen in settings
	*/
	private function sqlFieldJoins()
	{
		$statement = "";
		foreach($this->results_fields as $key=>$field){
			$fieldname = $field;
			$statement .= "\nLEFT JOIN " . $this->query_data['meta_table'] . " AS $fieldname
			ON p.ID = $fieldname.post_id AND $fieldname.meta_key = " . "'" . $fieldname . "'" . "\n";
		}
		return $statement;
	}

	/**
	* Set the SQL Limit
	*/
	private function sqlLimit()
	{
		$limit = $this->settings_repo->resultsLimit();
		if ( $limit == -1 ) return;
		if ( is_numeric(intval($limit)) ) return "LIMIT " . intval($limit);
	}

	/**
	* Set the Query
	*/
	private function setQuery()
	{
		$sql = "
			SELECT 
			p.post_title AS title,
			p.ID AS id," .
			$this->sqlFieldVars() . "
			lat.meta_value AS latitude,
			lng.meta_value AS longitude,
			( " . $this->query_data['diameter'] . " * acos( cos( radians(@origlat) ) * cos( radians( lat.meta_value ) ) 
			* cos( radians( lng.meta_value ) - radians(@origlng) ) + sin( radians(@origlat) ) * sin(radians(lat.meta_value)) ) )
			AS distance
			FROM " . $this->query_data['post_table'] . " AS p
			LEFT JOIN " . $this->query_data['meta_table'] . " AS lat
			ON p.ID = lat.post_id AND lat.meta_key = '" . $this->query_data['lat_field'] . "'
			LEFT JOIN " . $this->query_data['meta_table'] . " AS lng
			ON p.ID = lng.post_id AND lng.meta_key = '" . $this->query_data['lng_field'] . "'" . 
			$this->sqlFieldJoins() . "		
			WHERE lat.meta_value
				BETWEEN @origlat - (@distance / @dist_unit)
				AND @origlat + (@distance / @dist_unit)
			AND lng.meta_value
				BETWEEN @origlng - (@distance / (@dist_unit * cos(radians(@origlat))))
				AND @origlng + (@distance / (@dist_unit * cos(radians(@origlat))))
			AND `post_type` = '" . $this->query_data['post_type'] . "'
			AND `post_status` = 'publish'
			HAVING distance < @distance
			ORDER BY distance\n" . 
			$this->sqlLimit() . ";";
		$this->sql = $sql;
	}

	/**
	* Lookup location data
	*/
	private function runQuery()
	{
		global $wpdb;

		// Set the SQL Vars
		$wpdb->query("SET @origlat = " . $this->query_data['userlat'] . ";");
		$wpdb->query("SET @origlng = " . $this->query_data['userlong'] . ";");
		$wpdb->query("SET @distance = " . $this->query_data['distance'] . ";");
		$wpdb->query("SET @dist_unit = " . $this->query_data['distance_unit'] . ";");
		
		// Run the Query
		$results = $wpdb->get_results($this->sql);
		$this->result_count = count($results);
		$this->setResults($results);
	}

	/**
	* Prepare Results
	*/
	private function setResults($results)
	{
		$i = 0;
		foreach ( $results as $result ) {
			$location = $this->result_presenter->present($result, $i);
			$this->results[] = $location;
			$i++;
		}
	}

	/**
	* Get Result Count
	*/
	public function getResultCount()
	{
		return $this->result_count;
	}

	/**
	* Get Results
	*/
	public function getResults()
	{
		return $this->results;
	}
}