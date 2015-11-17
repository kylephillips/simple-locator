<?php 

namespace SimpleLocator\Migrations;

/**
* Default Plugin Options
*/
class DefaultOptions 
{

	public function __construct()
	{
		$this->setOptions();
	}

	public function setOptions()
	{
		if ( !get_option('wpsl_post_type') ){
			update_option('wpsl_post_type', 'location');
		}
		if ( !get_option('wpsl_field_type') ){
			update_option('wpsl_field_type', 'wpsl');
		}
		if ( !get_option('wpsl_lat_field') ){
			update_option('wpsl_lat_field', 'wpsl_latitude');
		}
		if ( !get_option('wpsl_lng_field') ){
			update_option('wpsl_lng_field', 'wpsl_longitude');
		}
		if ( !get_option('wpsl_output_css') ){
			update_option('wpsl_output_css', 'true');
		}
		if ( !get_option('wpsl_map_pin') ){
			update_option('wpsl_map_pin', '');
		} 
		if ( !get_option('wpsl_map_styles_type') ){
			update_option('wpsl_map_styles_type', 'none');
		}
		if ( !get_option('wpsl_map_styles_choice') ){
			update_option('wpsl_map_styles_choice', '');
		}
		if ( !get_option('wpsl_singular_data') ){
			update_option('wpsl_singular_data', 'true');
		}
		if ( !get_option('wpsl_show_hidden') ){
			update_option('wpsl_show_hidden', 'false');
		}
		if ( !get_option('wpsl_gmaps_api') ){
			update_option('wpsl_gmaps_api', 'true');
		}
		if ( !get_option('wpsl_gmaps_api_admin') ){
			update_option('wpsl_gmaps_api_admin', 'true');
		}
		if ( !get_option('wpsl_measurement_unit') ){
			update_option('wpsl_measurement_unit', 'miles');
		}
		if ( !get_option('wpsl_geo_button') ){
			update_option('wpsl_geo_button', array(
				'enabled' => '',
				'text'=> __('Use my location', 'wpsimplelocator')
			));
		}
		if ( !get_option('wpsl_posttype_labels') ){
			update_option('wpsl_posttype_labels', array(
				'name' => 'location',
				'label' => __('Locations', 'wpsimplelocator'),
				'singular'=> __('Location', 'wpsimplelocator'),
				'add_new_item'=> __('Add Location', 'wpsimplelocator'),
				'edit_item' => __('Edit Location', 'wpsimplelocator'),
				'view_item' => __('View Location', 'wpsimplelocator'),
				'slug' => __('location', 'wpsimplelocator'),
				'menu_icon' => 'dashicons-post-status',
				'menu_position' => 6
			));
		}
		if ( !get_option('wpsl_default_map') ){
			update_option('wpsl_default_map', array(
				'show' => 'false',
				'latitude' => '33.786637',
				'longitude' => '-84.383160',
				'zoom' => '15',
				'user_location' => 'false'
			));
		}
		if ( !get_option('wpsl_results_fields_formatted') ){
			update_option('wpsl_results_fields_formatted', array(
				'output' => "<strong><a href='[post_permalink]'>[post_title]</a></strong>\r\n<em>" . __('Distance', 'wpsimplelocator') . ": [distance]</em>\r\n[wpsl_address]\r\n[wpsl_city], [wpsl_state] [wpsl_zip]\r\n[wpsl_phone]\r\n<a href='[wpsl_website]'>[wpsl_website]</a>\r\n[show_on_map]",
				'limit' => -1
			));
		}
	}

}