<?php namespace SimpleLocator\Repositories;

/**
* Settings Repo
*/
class SettingsRepository {

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
	}

	/**
	* Get an array of fields to display
	*/
	public function resultsFieldsArray()
	{
		$option = get_option('wpsl_results_fields');
		return $option['fields'];
	}

	/**
	* Results Limit
	*/
	public function resultsOption($selected = 'limit')
	{
		$option = get_option('wpsl_results_fields');
		if ( isset($option[$selected]) ) return $option[$selected];
		return;
	}

}