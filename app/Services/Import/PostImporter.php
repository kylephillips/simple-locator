<?php 

namespace SimpleLocator\Services\Import;

use SimpleLocator\Services\Import\GoogleMapGeocode;
use SimpleLocator\Repositories\PostRepository;

/**
* Import a Single Row/Post
*/
class PostImporter 
{

	/**
	* Array of column mappings from transient
	*/
	private $post_data;

	/**
	* Transient
	*/
	private $transient;

	/**
	* Geocode Coordinates
	*/
	private $coordinates;

	/**
	* Import Status
	*/
	private $import_status = true;

	/**
	* Geocoder Class
	*/
	public $geocoder;

	/**
	* Newly Created Post ID
	*/
	private $post_id;

	/**
	* Post Repository
	*/
	private $post_repo;
	

	public function __construct()
	{
		$this->geocoder = new GoogleMapGeocode;
		$this->post_repo = new PostRepository;
	}

	/**
	* Import a Post
	*/
	public function import($post_data, $transient)
	{
		$this->post_data = $post_data;
		$this->transient = $transient;
		$this->setAddress();
		$this->geocode();
		if ( $this->post_id ) return $this->post_id;
		return false;
	}

	/**
	* Set the Address to be Geocoded
	*/
	private function setAddress()
	{
		$address = '';
		foreach( $this->transient['columns'] as $field ){
			if ( $field->type == 'address' ) $address .= $this->post_data[$field->csv_column] . ' ';
			if ( $field->type == 'city' ) $address .= $this->post_data[$field->csv_column] . ' ';
			if ( $field->type == 'state' ) $address .= $this->post_data[$field->csv_column] . ' ';
			if ( $field->type == 'zip' ) $address .= $this->post_data[$field->csv_column];
			if ( $field->type == 'full_address' ) $address = $this->post_data[$field->csv_column];
		}
		$this->address = $address;
	}

	/**
	* Geocode the Address
	*/
	private function geocode()
	{
		try {
			$this->geocoder->geocode($this->address);
			$coordinates = $this->geocoder->getCoordinates();
			$this->coordinates['latitude'] = $coordinates['lat'];
			$this->coordinates['longitude'] = $coordinates['lng'];
			$this->importPost();
		} catch ( \SimpleLocator\Services\Import\Exceptions\GoogleQueryLimitException $e ) {
			$this->updateLastRowImported();
			throw new \Exception($e->getMessage());
		} catch ( \SimpleLocator\Services\Import\Exceptions\GoogleRequestDeniedException $e ) {
			$this->updateLastRowImported();
			throw new \Exception($e->getMessage());
		} catch ( \SimpleLocator\Services\Import\Exceptions\GoogleAPIException $e ) {
			$this->failedRow($e->getMessage());
			$this->importPost();
		}		
	}

	/**
	* Import WP Post
	*/
	private function importPost()
	{
		$this->import_status = true;
		$post = array();
		$post['post_type'] = $this->transient['post_type'];
		$post['post_status'] = $this->transient['import_status'];
		foreach ( $this->transient['columns'] as $field ){
			$column_value = ( isset($this->post_data[$field->csv_column]) ) ? $this->post_data[$field->csv_column] : "";
			if ( $field->field == 'title' && $column_value !== "" ) $post['post_title'] = sanitize_text_field($column_value);
			if ( $field->field == 'content' && $column_value !== "" ) $post['post_content'] = sanitize_text_field($column_value);
		}
		if ( !isset($post['post_title']) ){
			$this->failedRow(__('Missing Title', 'wpsimplelocator'));
			return false;
		}
		if ( $this->post_repo->postExists($post['post_title']) ){
			$this->failedRow(__('Location Exists', 'wpsimplelocator'));
			return false;
		}
		$this->post_id = wp_insert_post($post);
		if ( !$this->post_id ) {
			$this->failedRow(__('WordPress Import Error', 'wpsimplelocator'));
			return false;
		}
		$this->addMeta();
		$this->addGeocodeField();
	}

	/**
	* Add Custom Meta Fields
	*/
	private function addMeta()
	{
		$exclude_fields = array('title', 'content');
		foreach ( $this->transient['columns'] as $field ){
			if ( in_array($field->field, $exclude_fields) ) continue;
			$column_value = ( isset($this->post_data[$field->csv_column]) ) ? sanitize_text_field($this->post_data[$field->csv_column]) : "";
			if ( $field->type == 'website' ) $column_value = esc_url($column_value);
			if ( $column_value !== "" ) add_post_meta($this->post_id, $field->field, $column_value);
		}
	}

	/**
	* Add Geocode Fields
	*/
	private function addGeocodeField()
	{
		if ( isset($this->coordinates['latitude']) ) add_post_meta($this->post_id, $this->transient['lat'], $this->coordinates['latitude']);
		if ( isset($this->coordinates['longitude']) ) add_post_meta($this->post_id, $this->transient['lng'], $this->coordinates['longitude']);
	}

	/**
	* Update failed row
	* @param string $error
	*/
	private function failedRow($error)
	{
		$this->import_status = false;
		$transient = get_transient('wpsl_import_file'); // Calling manually for multiple errors
		$row_error = array(
			'row' => $this->post_data['record_number'],
			'error' => $error
		);
		$transient['error_rows'][] = $row_error;
		set_transient('wpsl_import_file', $transient, 1 * YEAR_IN_SECONDS);
	}

	/**
	* Update the last row imported in case of API Failure
	*/
	private function updateLastRowImported()
	{
		$transient = get_transient('wpsl_import_file'); // Calling manually for multiple errors
		$transient['last_imported'] = $this->post_data['record_number'] - 1;
		$transient['last_import_date'] = date_i18n( 'j F Y: H:i', time() );
		set_transient('wpsl_import_file', $transient, 1 * YEAR_IN_SECONDS);
	}

}