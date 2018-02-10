<?php 
namespace SimpleLocator\Migrations;

/**
* Required Plugin Options Checked on init
*/
class RequiredOptions 
{
	public function __construct()
	{
		$this->setOptions();
	}

	public function setOptions()
	{
		if ( !get_option('wpsl_post_type') ) update_option('wpsl_post_type', 'location');
		if ( !get_option('wpsl_field_type') ) update_option('wpsl_field_type', 'wpsl');
		if ( !get_option('wpsl_lat_field') ) update_option('wpsl_lat_field', 'wpsl_latitude');
		if ( !get_option('wpsl_lng_field') ) update_option('wpsl_lng_field', 'wpsl_longitude');
		if ( !get_option('wpsl_posttype_labels') ){
			update_option('wpsl_posttype_labels', array(
				'name' => 'location',
				'label' => __('Locations', 'simple-locator'),
				'singular'=> __('Location', 'simple-locator'),
				'add_new_item'=> __('Add Location', 'simple-locator'),
				'edit_item' => __('Edit Location', 'simple-locator'),
				'view_item' => __('View Location', 'simple-locator'),
				'slug' => __('location', 'simple-locator'),
				'menu_icon' => 'dashicons-post-status',
				'menu_position' => 6
			));
		}
	}
}