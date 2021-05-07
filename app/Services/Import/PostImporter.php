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
	* Do the Geocode?
	*/
	private $do_geocode = true;

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
		$this->importPost();
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
		if ( !$this->do_geocode || $this->transient['skip_geocode'] ) return;
		try {
			$this->geocoder->geocode($this->address);
			$coordinates = $this->geocoder->getCoordinates();
			$this->coordinates['latitude'] = $coordinates['lat'];
			$this->coordinates['longitude'] = $coordinates['lng'];
			$this->addGeocodeField();
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
		
		$duplicate_handling = $this->transient['duplicate_handling'];
		$existing_id = $this->post_repo->postExists($this->post_data, $this->transient);
		$post = [];
		if ( $existing_id && $duplicate_handling == 'skip' ){
			$this->failedRow(__('Location Exists, Skipping Import', 'simple-locator'));
			return false;
		}
		if ( $existing_id && $duplicate_handling == 'update' ){
			$this->checkExistingAddress($existing_id[0]->ID);
			$post['ID'] = $existing_id[0]->ID;
		}

		$post['post_type'] = $this->transient['post_type'];
		$post['post_status'] = $this->transient['import_status'];
		foreach ( $this->transient['columns'] as $field ){
			$column_value = ( isset($this->post_data[$field->csv_column]) ) ? $this->post_data[$field->csv_column] : "";
			if ( $field->field == 'title' && $column_value !== "" ) $post['post_title'] = sanitize_text_field($column_value);
			if ( $field->field == 'content' && $column_value !== "" ) $post['post_content'] = sanitize_text_field($column_value);
			if ( $field->field == 'excerpt' && $column_value !== "" ) $post['post_excerpt'] = sanitize_text_field($column_value);
			if ( $field->field == 'status' && $column_value !== "" ) $post['post_status'] = sanitize_text_field($column_value);
			if ( $field->field == 'publish_date' && $column_value !== "") $post['post_date'] = $this->date($column_value);
			if ( $field->field == 'publish_date_gmt' && $column_value !== "" ) $post['publish_date_gmt'] = $this->date($column_value);
			if ( $field->field == 'modified_date' && $column_value !== "" ) $post['post_modified'] = $this->date($column_value);
			if ( $field->field == 'modified_date_gmt' && $column_value !== "" ) $post['post_modified_gmt'] = $this->date($column_value);
			if ( $field->field == 'slug' && $column_value !== "" ) $post['post_name'] = sanitize_text_field($column_value);
		}
		if ( !isset($post['post_title']) ){
			$this->failedRow(__('Missing Title', 'simple-locator'));
			return false;
		}
		$this->post_id = wp_insert_post($post);
		if ( !$this->post_id ) {
			$this->failedRow(__('WordPress Import Error', 'simple-locator'));
			return false;
		}
		$this->addMeta();
		$this->addTerms();
		do_action('simple_locator_post_imported', $this->post_id, $post);
	}

	/**
	* Format a time for inserting date
	*/
	private function date($value)
	{
		return date('Y-m-d H:i:s', strtotime($value));
	}

	/**
	* Do we need to geocode the updated post?
	*/
	private function checkExistingAddress($post_id)
	{
		if ( $this->transient['skip_geocode'] ){
			$this->do_geocode = false;
			return;
		}
		$meta = get_post_meta($post_id);
		$address = '';
		if ( isset($meta['wpsl_address'][0]) ) $address .= $meta['wpsl_address'][0] . ' ';
		if ( isset($meta['wpsl_city'][0]) ) $address .= $meta['wpsl_city'][0] . ' ';
		if ( isset($meta['wpsl_state'][0]) ) $address .= $meta['wpsl_state'][0] . ' ';
		if ( isset($meta['wpsl_zip'][0]) ) $address .= $meta['wpsl_zip'][0];
		if ( $address == $this->address ) $this->do_geocode = false;

	}

	/**
	* Add Custom Meta Fields
	*/
	private function addMeta()
	{
		foreach ( $this->transient['columns'] as $field ){
			if ( $field->field_type == 'post_field' || $field->field_type == 'taxonomy' ) continue;
			$column_value = ( isset($this->post_data[$field->csv_column]) ) ? $this->post_data[$field->csv_column] : "";
			if ( $field->type == 'website' ) $column_value = esc_url($column_value);
			$column_value = apply_filters('simple_locator_import_custom_field', $column_value, $field->field);
			if ( $column_value !== "" ) update_post_meta($this->post_id, $field->field, $column_value);
		}
	}

	/**
	* Add Taxonomy Terms
	*/
	private function addTerms()
	{
		$separator = $this->transient['taxonomy_separator'];
		$separator = ( $separator == 'comma' ) ? ',' : '|';
		foreach ( $this->transient['columns'] as $field ){ 
			if ( $field->field_type !== 'taxonomy' ) continue;
			$terms = ( isset($this->post_data[$field->csv_column]) ) ? explode($separator, $this->post_data[$field->csv_column]) : [];
			$taxonomy = str_replace('taxonomy_', '', $field->field);
			foreach ( $terms as $term ){
				wp_set_object_terms($this->post_id, $term, $taxonomy, true);
			}
		}
	}

	/**
	* Add Geocode Fields
	*/
	private function addGeocodeField()
	{
		if ( isset($this->coordinates['latitude']) ) update_post_meta($this->post_id, $this->transient['lat'], $this->coordinates['latitude']);
		if ( isset($this->coordinates['longitude']) ) update_post_meta($this->post_id, $this->transient['lng'], $this->coordinates['longitude']);
	}

	/**
	* Update failed row
	* @param string $error
	*/
	private function failedRow($error)
	{
		$this->import_status = false;
		$transient = get_transient('wpsl_import_file'); // Calling manually for multiple errors
		$row_error = [
			'row' => $this->post_data['record_number'],
			'error' => $error
		];
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