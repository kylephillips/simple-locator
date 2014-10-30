<?php
/**
* Custom Meta Fields for Location Post Type
*/
class WPSL_MetaFields {

	/**
	* Meta Data
	*/
	private $meta;

	/**
	* Fields
	*/
	public $fields;


	function __construct()
	{
		$this->setFields();
		add_action( 'add_meta_boxes', array( $this, 'metaBox' ));
		add_action( 'save_post_location', array($this, 'savePost' ));
	}


	/**
	* Set the Fields for use in custom meta
	*/
	private function setFields()
	{
		$this->fields = array(
			'latitude' => 'wpsl_latitude',
			'longitude' => 'wpsl_longitude',
			'address' => 'wpsl_address',
			'city' => 'wpsl_city',
			'state' => 'wpsl_state',
			'zip' => 'wpsl_zip',
			'phone' => 'wpsl_phone',
			'website' => 'wpsl_website',
			'additionalinfo' => 'wpsl_additionalinfo'
		);
	}


	/**
	* Register the Meta Box
	*/
	public function metaBox() 
	{
    	add_meta_box( 
    		'wpsl-meta-box', 
    		'Location Information', 
    		array($this, 'displayMeta'), 
    		'location', 
    		'normal', 
    		'high' 
    	);
	}


	/**
	* Meta Boxes for Output
	*/
	public function displayMeta($post)
	{
		$this->setData($post);
		wp_nonce_field( 'my_wpsl_meta_box_nonce', 'wpsl_meta_box_nonce' );
		include( dirname(dirname(__FILE__)) . '/views/location-meta.php' );
	}


	/**
	* Set the Field Data
	*/
	private function setData($post)
	{
		foreach ( $this->fields as $key=>$field )
		{
			$this->meta[$key] = get_post_meta( $post->ID, $field, true );
		}
	}


	/**
	* Save the custom post meta
	*/
	public function savePost( $post_id ) 
	{
		if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return $post_id;
		if( !isset( $_POST['wpsl_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['wpsl_meta_box_nonce'], 'my_wpsl_meta_box_nonce' ) ) return $post_id;

		// Save Custom Fields
		foreach ( $this->fields as $key => $field )
		{
			if ( isset( $_POST[$field] ) ) update_post_meta( $post_id, $field, esc_attr( $_POST[$field] ) );
		}
	} 

}