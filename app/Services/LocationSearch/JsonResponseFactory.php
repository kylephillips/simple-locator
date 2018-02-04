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
	* Request
	*/
	private $request;

	/**
	* Set the Response Data
	*/
	private function setData()
	{
		$taxonomies = ( isset($this->request['taxonomies']) ) ? $this->request['taxonomies'] : null;
		$this->data = array(
			'address' => sanitize_text_field($this->request['address']),
			'formatted_address' => sanitize_text_field($this->request['formatted_address']),
			'distance' => sanitize_text_field($this->request['distance']),
			'latitude' => sanitize_text_field($this->request['latitude']),
			'longitude' => sanitize_text_field($this->request['longitude']),
			'unit' => sanitize_text_field($this->request['unit']),
			'geolocation' => sanitize_text_field($this->request['geolocation']),
			'taxonomies' => $taxonomies,
			'allow_empty_address' => $this->request['allow_empty_address']
		);
	}

	/**
	* Build the Response Array
	* @return array
	*/
	public function build($results, $results_count, $request = null)
	{
		$this->request = ( $request ) ? $request : $_POST;
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
			'geolocation' => $this->data['geolocation'],
			'taxonomies' => $this->data['taxonomies'],
			'allow_empty_address' => $this->data['allow_empty_address']
		);
	}

}