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
	}

}