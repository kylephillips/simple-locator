<?php namespace SimpleLocator\Import;

use SimpleLocator\Import\GoogleMapGeocode;

/**
* Import a Single Row/Post
*/
class ImportRow {

	/**
	* Array of column mappings from transient
	*/
	private $column_data;

	/**
	* Column Map
	*/
	private $transient;

	/**
	* Geo Meta Fields
	* @var array
	*/
	private $geo_fields;

	/**
	* Geocode Coordinates
	*/
	private $coordinates;

	/**
	* Import Status
	*/
	private $import_status;

	/**
	* Geocoder Class
	*/
	private $geocoder;
	
	public function __construct($column_data, $transient, $geo_fields)
	{
		$this->geocoder = new GoogleMapGeocode;
		$this->column_data = $column_data;
		$this->transient = $transient;
		$this->geo_fields = $geo_fields;
		$this->setAddress();
		$this->geocode();
		return $this->import_status;
	}

	/**
	* Set the Address to be Geocoded
	*/
	private function setAddress()
	{
		$address = '';
		foreach( $this->transient['columns'] as $field ){
			if ( $field->type == 'address' ) $address .= $this->column_data[$field->csv_column] . ' ';
			if ( $field->type == 'city' ) $address .= $this->column_data[$field->csv_column] . ' ';
			if ( $field->type == 'state' ) $address .= $this->column_data[$field->csv_column] . ' ';
			if ( $field->type == 'zip' ) $address .= $this->column_data[$field->csv_column];
			if ( $field->type == 'full_address' ) $address = $this->column_data[$field->csv_column];
		}
		$this->address = $address;
	}

	/**
	* Geocode the Address
	*/
	private function geocode()
	{
		if ( $this->geocoder->geocode($this->address) ){
			$coordinates = $this->geocoder->getCoordinates();
			$this->coordinates['latitude'] = $coordinates['lat'];
			$this->coordinates['longitude'] = $coordinates['lng'];
			return $this->importPost();
		} else {
			return wp_send_json(array('status'=>'apierror', 'message'=>$this->geocoder->getError()));
			die();
		}
	}

	/**
	* Import WP Post
	*/
	private function importPost()
	{
		$post = array();
		$post['post_type'] = $this->transient['post_type'];
		$post['post_status'] = $this->transient['import_status'];
		foreach ( $this->transient['columns'] as $field ){
			$column_value = ( isset($this->column_data[$field->csv_column]) ) ? $this->column_data[$field->csv_column] : "";
			if ( $field->field == 'title' && $column_value !== "" ) $post['post_title'] = $column_value;
			if ( $field->field == 'content' && $column_value !== "" ) $post['post_content'] = $column_value;
		}
		$post_id = wp_insert_post($post);
		if ( $post = 0 ) {
			$this->import_status = false;
			return;
		}
		$this->addMeta($post_id);
		$this->addGeocodeField($post_id);
		$this->import_status = true;
	}

	/**
	* Add Custom Meta Fields
	*/
	private function addMeta($post_id)
	{
		$exclude_fields = array('title', 'content');
		foreach ( $this->transient['columns'] as $field ){
			if ( in_array($field->field, $exclude_fields) ) continue;
			$column_value = ( isset($this->column_data[$field->csv_column]) ) ? $this->column_data[$field->csv_column] : "";
			if ( $column_value !== "" ) add_post_meta($post_id, $field->field, $column_value);
		}
	}

	/**
	* Add Geocode Fields
	*/
	private function addGeocodeField($post_id)
	{
		if ( isset($this->coordinates['latitude']) ) add_post_meta($post_id, $this->geo_fields['lat'], $this->coordinates['latitude']);
		if ( isset($this->coordinates['longitude']) ) add_post_meta($post_id, $this->geo_fields['lng'], $this->coordinates['longitude']);
	}

}