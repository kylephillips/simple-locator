<?php 
namespace SimpleLocator\WPData;

use SimpleLocator\WPData\Fields\FormFields;

/**
* Custom Meta Fields for Location Post Type
*/
class MetaFields 
{
	/**
	* Meta Data
	*/
	private $meta;

	/**
	* Fields
	*/
	public $fields;

	/**
	* Form Fields Output
	*/
	private $form_fields;

	function __construct()
	{
		$this->form_fields = new FormFields;
		add_action( 'admin_init', [$this, 'setFields']);
		add_action( 'add_meta_boxes', [$this, 'metaBox']);
		add_action( 'save_post', [$this, 'savePost']);
	}

	/**
	* Set the Fields for use in custom meta
	*/
	public function setFields()
	{
		$this->fields = $this->form_fields->order();
		$this->fields[] = 'custom_geo';
		$this->fields[] = 'latitude';
		$this->fields[] = 'longitude';
		$this->fields[] = 'custom_geo';
	}

	/**
	* Register the Meta Box
	*/
	public function metaBox() 
	{
    	add_meta_box( 
    		'wpsl-meta-box', 
    		apply_filters('simple_locator_meta_fields_title', __('Location Information', 'simple-locator')), 
    		[$this, 'displayMeta'], 
    		$this->getPostType(), 
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
		include( \SimpleLocator\Helpers::view('post-meta/location-meta') );
	}

	/**
	* Set the Field Data
	*/
	private function setData($post)
	{
		foreach ( $this->fields as $key => $field )
		{
			$this->meta[$field] = get_post_meta( $post->ID, 'wpsl_' . $field, true );
		}
	}

	/**
	* Save the custom post meta
	*/
	public function savePost( $post_id ) 
	{
		if ( get_post_type($post_id) == $this->getPostType() ) :
			if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return $post_id;
			if( !isset( $_POST['wpsl_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['wpsl_meta_box_nonce'], 'my_wpsl_meta_box_nonce' ) ) return $post_id;
			
			// Save Custom Fields
			foreach ( $this->fields as $key => $field )
			{
				$fieldName = 'wpsl_' . $field;
				if ( isset($_POST[$fieldName]) && $_POST[$fieldName] !== "" ) 
					update_post_meta( $post_id, $fieldName, esc_attr( $_POST[$fieldName] ) );
				if ( isset($_POST[$fieldName]) && $_POST[$fieldName] == "" )
					delete_post_meta( $post_id, $fieldName );
			}
		endif;
	} 

	/**
	* Get the Location Post Type
	*/
	private function getPostType()
	{
		$posttype = get_option('wpsl_post_type');
		$hide_meta = get_option('wpsl_hide_default_fields');
		if ( $hide_meta == 'true' ) return ' ';
		return ( $posttype !== "" ) ? $posttype : 'location';
	}
}