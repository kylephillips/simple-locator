<?php namespace SimpleLocator\Forms;

use SimpleLocator\Repositories\SettingsRepository;
use SimpleLocator\Helpers;

/**
* Front-end form handler for simple locator lookup
* @return JSON Response
*/
class MapHandler {

	/**
	* Form Data
	* @var array
	*/
	private $data;

	/**
	* Validator
	*/
	private $validator;

	/**
	* Settings Repository
	* @var object
	*/
	private $settings_repo;

	/**
	* Results Fields from Settings
	* @var array
	*/
	private $results_fields;

	/**
	* SQL Limit
	*/
	private $limit;

	/**
	* Before Formatting
	*/
	private $before_formatting;

	/**
	* After Formatting
	*/
	private $after_formatting;

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
	private $total_results;

	/**
	* JSON Response
	* @var array
	*/
	private $response;


	public function __construct()
	{
		$this->settings_repo = new SettingsRepository;
		$this->validator = new Validation;
		$this->setResultsFields();
		$this->setData();
		$this->validateData();
		$this->setQueryData();
		$this->setQuery();
		$this->runQuery();
		$this->sendResponse();
	}


	/**
	* Set the results fields
	*/
	private function setResultsFields()
	{
		$this->results_fields = $this->settings_repo->resultsFieldsArray();
		$this->limit = $this->settings_repo->resultsOption();
		$this->before_formatting = $this->settings_repo->resultsOption('before_item');
		$this->after_formatting = $this->settings_repo->resultsOption('after_item');
	}


	/**
	* Sanitize and set the user-submitted data
	*/
	private function setData()
	{
		$this->data = array(
			'nonce' => sanitize_text_field($_POST['locatorNonce']),
			'address' => sanitize_text_field($_POST['address']),
			'formatted_address' => sanitize_text_field($_POST['formatted_address']),
			'distance' => sanitize_text_field($_POST['distance']),
			'latitude' => sanitize_text_field($_POST['latitude']),
			'longitude' => sanitize_text_field($_POST['longitude']),
			'unit' => sanitize_text_field($_POST['unit']),
			'geolocation' => sanitize_text_field($_POST['geolocation'])
		);
	}


	/**
	* Validate Data
	*/
	private function validateData()
	{
		return ( $this->validator->validates($this->data) ) ? true : false;
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
			$fieldname = $field['field'];
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
			$fieldname = $field['field'];
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
		if ( $this->limit == -1 ) return;
		if ( is_numeric(intval($this->limit)) ) return "LIMIT " . intval($this->limit);
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
		$this->total_results = count($results);
		$this->setResults($results);
	}


	/**
	* Prepare Results
	*/
	private function setResults($results)
	{
		foreach ( $results as $result ) :
			$location = array(
				'title' => $result->title,
				'permalink' => get_permalink($result->id),
				'latitude' => $result->latitude,
				'longitude' => $result->longitude,
				'output'=> $this->formatOutput($result)
			);
			if ( $this->settings_repo->resultsOption('show_distance') == 'true' ) $location['distance'] = round($result->distance, 2);
			$this->results[] = $location;
		endforeach;
	}

	/**
	* Format the output
	*/
	private function formatOutput($result)
	{
		$output = "";
		$output .= $this->before_formatting;
		foreach($this->results_fields as $field){
			$rfield = $field['field'];
			$found = $result->$rfield;
			if ( !$found ) continue;

			if ( isset($field['before']) ) $output .= $field['before'];			
			if ( $field['type'] == 'url' ) $output .= '<a href="' . Helpers::checkURL($found) . '">';
			$output .= $found;
			if ( $field['type'] == 'url' ) $output .= '</a>';			
			if ( isset($field['after']) ) $output .= $field['after'];
		}
		$output .= $this->after_formatting;
		return $output;
	}


	/**
	* Send the Response
	*/
	private function sendResponse()
	{
		return wp_send_json(array(
			'status' => 'success', 
			'distance'=> $this->data['distance'],
			'latitude' => $this->data['latitude'],
			'longitude' => $this->data['longitude'],
			'unit' => $this->data['unit'],
			'formatted_address' => $this->data['formatted_address'],
			'results' => $this->results,
			'result_count' => $this->total_results,
			'using_geolocation' => $this->data['geolocation']
		));
	}

}


