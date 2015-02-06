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
	* @return string
	*/
	public function getLocationPostType()
	{
		return get_option('wpsl_post_type');
	}

}