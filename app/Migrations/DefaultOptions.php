<?php 
namespace SimpleLocator\Migrations;

/**
* Default Plugin Options set during install
*/
class DefaultOptions 
{
	public function __construct()
	{
		$this->setOptions();
	}

	public function setOptions()
	{
		if ( !get_option('wpsl_output_css') ) update_option('wpsl_output_css', 'true');
		if ( !get_option('wpsl_map_pin') ) update_option('wpsl_map_pin', '');
		if ( !get_option('wpsl_map_styles_type') ) update_option('wpsl_map_styles_type', 'none');
		if ( !get_option('wpsl_map_styles_choice') ) update_option('wpsl_map_styles_choice', '');
		if ( !get_option('wpsl_singular_data') ) update_option('wpsl_singular_data', 'true');
		if ( !get_option('wpsl_show_hidden') ) update_option('wpsl_show_hidden', 'false');
		if ( !get_option('wpsl_gmaps_api') ) update_option('wpsl_gmaps_api', 'true');
		if ( !get_option('wpsl_gmaps_api_admin') ) update_option('wpsl_gmaps_api_admin', 'true');
		if ( !get_option('wpsl_measurement_unit') ) update_option('wpsl_measurement_unit', 'miles');
		if ( !get_option('wpsl_geo_button') ){
			update_option('wpsl_geo_button', array(
				'enabled' => '',
				'text'=> __('Use my location', 'simple-locator')
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
	}
}