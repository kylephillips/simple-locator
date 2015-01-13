<?php namespace SimpleLocator\Migrations;

/**
* Default Plugin Options
*/
class DefaultOptions {

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
		if ( !get_option('wpsl_posttype_labels') ){
			update_option('wpsl_posttype_labels', array(
				'name' => 'location',
				'label' => __('Locations', 'wpsimplelocator'),
				'singular'=> __('Location', 'wpsimplelocator'),
				'add_new_item'=> __('Add Location', 'wpsimplelocator'),
				'edit_item' => __('Edit Location', 'wpsimplelocator'),
				'view_item' => __('View Location', 'wpsimplelocator'),
				'slug' => __('location', 'wpsimplelocator'),
				'menu_position' => 6
			));
		}
	}

}