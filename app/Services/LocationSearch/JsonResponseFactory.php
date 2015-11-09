<?php 

namespace SimpleLocator\Services\LocationSearch;

/**
* Build the JSON Response Array for a Location Search
*/
class JsonResponseFactory 
{

	/**
	* Response Data
	*/
	private $data;

	/**
	* Set the Response Data
	*/
	private function setData()
	{
		$taxonomies = ( isset($_POST['taxonomies']) ) ? $_POST['taxonomies'] : null;
		$this->data = array(
			'address' => sanitize_text_field($_POST['address']),
			'formatted_address' => sanitize_text_field($_POST['formatted_address']),
			'distance' => sanitize_text_field($_POST['distance']),
			'latitude' => sanitize_text_field($_POST['latitude']),
			'longitude' => sanitize_text_field($_POST['longitude']),
			'unit' => sanitize_text_field($_POST['unit']),
			'geolocation' => sanitize_text_field($_POST['geolocation']),
			'taxonomies' => $taxonomies,
			'allow_empty_address' => $_POST['allow_empty_address']
		);
	}

	/**
	* Build the Response Array
	* @return array
	*/
	public function build($results, $results_count)
	{
		$this->setData();
		return array(
			'status' => 'success', 
			'distance'=> $this->data['distance'],
			'latitude' => $this->data['latitude'],
			'longitude' => $this->data['longitude'],
			'unit' => $this->data['unit'],
			'formatted_address' => $this->data['formatted_address'],
			'results' => $results,
			'result_count' => $results_count,
			'using_geolocation' => $this->data['geolocation'],
			'taxonomies' => $this->data['taxonomies'],
			'allow_empty_address' => $this->data['allow_empty_address']
		);
	}

}