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
		if ( !get_option('wpsl_results_fields_formatted') ){
			update_option('wpsl_results_fields_formatted', array(
				'output' => "<strong><a href='[post_permalink]'>[post_title]</a></strong>\r\n<em>" . __('Distance', 'simple-locator') . ": [distance]</em>\r\n[wpsl_address]\r\n[wpsl_city], [wpsl_state] [wpsl_zip]\r\n[wpsl_phone]\r\n<a href='[wpsl_website]'>[wpsl_website]</a>\r\n[show_on_map]",
				'limit' => -1
			));
		}
		if ( !get_option('wpsl_results_fields_formatted_default') ){
			update_option('wpsl_results_fields_formatted_default', array(
				'output' => "<strong><a href='[post_permalink]'>[post_title]</a></strong>\r\n[wpsl_address]\r\n[wpsl_city], [wpsl_state] [wpsl_zip]\r\n[wpsl_phone]\r\n<a href='[wpsl_website]'>[wpsl_website]</a>\r\n[show_on_map]",
				'limit' => -1
			));
		}
	}
}