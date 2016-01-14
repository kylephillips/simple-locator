<?php 

namespace SimpleLocator\Repositories;

/**
* Settings Repo
*/
class SettingsRepository 
{

	/**
	* Get Geo Button Options
	* @param $return string 
	*/
	public function showGeoButton($return = 'enabled')
	{
		$option = get_option('wpsl_geo_button');
		if ( $return == 'enabled' ){
			return ( !isset($option['enabled']) || $option['enabled'] == "" ) ? 'false' : 'true';
		}
		return ( !isset($option['text']) || $option['text'] == "" ) ? __('Use my location', 'wpsimplelocator') : $option['text'];
	}

	/**
	* Output the Google Maps API
	* @return boolean
	*/
	public function outputGMaps()
	{
		$option = get_option('wpsl_gmaps_api');
		return ( $option == 'true' ) ? true : false;
	}

	/**
	* Output the Google Maps API in the Admin
	* @return boolean
	*/
	public function outputGMapsAdmin()
	{
		$option = get_option('wpsl_gmaps_api_admin');
		return ( $option == 'true' ) ? true : false;
	}

	/**
	* Show a default map?
	* @return boolean
	*/
	public function showDefaultMap()
	{
		$option = get_option('wpsl_default_map');
		return ( isset($option['show']) && $option['show'] == "true" ) ? true : false;
	}

	/**
	* Default map coordinates & zoom
	* @return string
	* @param string - what field to return
	*/
	public function defaultMap($return = 'latitude')
	{
		$option = get_option('wpsl_default_map');
		if ( $return == 'latitude' ) return $option['latitude'];
		if ( $return == 'longitude' ) return $option['longitude'];
		if ( $return == 'zoom' ) return $option['zoom'];
		if ( $return == 'user_location' ){
			return ( isset($option['user_location']) && $option['user_location'] == 'true' ) ? 'true' : 'false';	
		} 
	}

	/**
	* Unit of Measurement
	*/
	public function measurementUnit()
	{
		$option = get_option('wpsl_measurement_unit');
		return ( $option == 'miles' || $option == 'Miles' ) ? 'miles' : 'kilometers';
	}

	/**
	* Results Limit
	*/
	public function resultsLimit()
	{
		$option = get_option('wpsl_results_fields_formatted');
		return ( isset($option['limit']) ) ? $option['limit'] : -1;
	}

	/**
	* Get the results fields from the formatted option
	* @return array
	*/
	public function getResultsFieldArray()
	{
		$exclude = array('post_title','distance','post_excerpt','post_permalink','show_on_map','post_thumbnail' );
		$resultoutput = get_option('wpsl_results_fields_formatted');
		$resultoutput = $resultoutput['output'];
		preg_match_all("/\[([^\]]*)\]/", $resultoutput, $matches);
		return array_diff(array_unique($matches[1]), $exclude);
	}

	/**
	* Get results field formatting option
	*/
	public function resultsFormatting()
	{
		$resultoutput = get_option('wpsl_results_fields_formatted');
		return $resultoutput['output'];
	}

	/**
	* Get the Location Post Type
	* @since 1.1.0
	* @return string
	*/
	public function getLocationPostType()
	{
		return get_option('wpsl_post_type');
	}

	/**
	* Get the distance unit
	* @since 1.1.0
	* @return string
	*/
	public function getDistanceUnit()
	{
		$unit = get_option('wpsl_measurement_unit');
		if ( $unit == "" || $unit == 'miles' ) return 'miles';
		return 'kilometers';
	}

	/**
	* Get the localized distance unit
	* @since 1.1.0
	* @return string
	*/
	public function getDistanceUnitLocalized()
	{
		$unit = get_option('wpsl_measurement_unit');
		if ( $unit == "" || $unit == 'miles' ) return __('Miles', 'wpsimplelocator');
		return __('Kilometers', 'wpsimplelocator');
	}

	/**
	* Get Geocode Field
	*/
	public function getGeoField($field = 'lat')
	{
		return ( $field == 'lat' ) ? get_option('wpsl_lat_field') : get_option('wpsl_lng_field');
	}

	/**
	* Is Autocomplete enabled?
	* @return boolean
	*/
	public function autocomplete()
	{
		$option = get_option('wpsl_enable_autocomplete');
		return ( $option == 'true' ) ? true : false;
	}

	/**
	* Are custom map options being used?
	*/
	public function customMapOptions()
	{
		$option = get_option('wpsl_custom_map_options');
		return ( $option && $option == '1' ) ? true : false;
	}

	/**
	* Get JS Map options
	*/ 
	public function mapOptions()
	{
		$option = get_option('wpsl_map_options');
		if ( $option ) return $option;
		include( dirname(dirname(__FILE__)) . '/Migrations/map_options/map-options.php' );
		return $default;
	}

	/**
	* Is JS debugging enabled?
	*/
	public function jsDebug()
	{
		$option = get_option('wpsl_js_debug');
		if ( $option && $option == 'true' ) return true;
		return false;
	}

}