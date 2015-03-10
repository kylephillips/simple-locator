<?php namespace SimpleLocator\Import;
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
	* Post Data
	*/
	private $post_data;

	/**
	* Import Status
	*/
	private $import_status;
	
	public function __construct($column_data, $transient)
	{
		$this->column_data = $column_data;
		$this->transient = $transient;
		$this->setPostData();
		$this->importPost();
		return $this->import_status;
	}

	/**
	* Set the Post Data based on column map
	*/
	private function setPostData()
	{
		$fields = array();
		foreach($this->transient['columns'] as $key => $value){
			if ( $value == "" ) continue;
			array_push($fields, $key);
		}
		foreach($fields as $field){
			if ( $field == 'website' ){
				$this->post_data[$field] = esc_url($this->column_data[intval($this->transient['columns'][$field])]);
			} else {
				$this->post_data[$field] = sanitize_text_field($this->column_data[intval($this->transient['columns'][$field])]);
			}
		}
	}

	/**
	* Import WP Post
	*/
	private function importPost()
	{
		$post = array();
		$post['post_type'] = 'location';
		$post['post_status'] = $this->transient['import_status'];
		if ( isset($this->post_data['title']) ) $post['post_title'] = $this->post_data['title'];
		if ( isset($this->post_data['content']) ) $post['post_content'] = $this->post_data['content'];
		$post_id = wp_insert_post($post);
		if ( $post = 0 ) {
			$this->import_status = false;
			return;
		}
		$this->addMeta($post_id);
	}

	/**
	* Add Custom Meta Fields
	*/
	private function addMeta($post_id)
	{
		if ( isset($this->post_data['address']) ) add_post_meta($post_id, 'wpsl_address', $this->post_data['address']);
		if ( isset($this->post_data['city']) ) add_post_meta($post_id, 'wpsl_city', $this->post_data['city']);
		if ( isset($this->post_data['state']) ) add_post_meta($post_id, 'wpsl_state', $this->post_data['state']);
		if ( isset($this->post_data['zip']) ) add_post_meta($post_id, 'wpsl_zip', $this->post_data['zip']);
		if ( isset($this->post_data['phone']) ) add_post_meta($post_id, 'wpsl_phone', $this->post_data['phone']);
		if ( isset($this->post_data['website']) ) add_post_meta($post_id, 'wpsl_website', $this->post_data['website']);
		if ( isset($this->post_data['additional']) ) add_post_meta($post_id, 'wpsl_additionalinfo', $this->post_data['additional']);
		$this->import_status = true;
	}

}