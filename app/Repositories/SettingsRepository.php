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

}