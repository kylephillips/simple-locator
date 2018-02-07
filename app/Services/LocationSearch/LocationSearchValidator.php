<?php 
namespace SimpleLocator\Services\LocationSearch;

class LocationSearchValidator 
{
	public function validate($request = null)
	{
		if ( !$request ) $request = $_POST;

		// Unit
		if ( (!isset($request['unit'])) || ($request['unit'] !== 'miles') && ($request['unit'] !== 'kilometers') ){
			throw new \Exception(__('Please provide a radius measurement unit (miles or kilometers).', 'wpsimplelocator'));
		}

		if ( isset($request['allow_empty_address']) && $request['allow_empty_address'] == 'true' ) return;

		// Latitude & Longitude
		if ( !isset($request['latitude']) || !is_numeric($request['latitude']) || !isset($request['longitude']) || !is_numeric($request['longitude']) ) {
			throw new \Exception(__('Please provide a valid latitude and longitude.', 'wpsimplelocator'));
		}

		// Distance
		if ( !isset($request['distance']) || !ctype_digit($request['distance']) ) {
			throw new \Exception(__('Please enter a valid distance.', 'wpsimplelocator'));
		}
	}
}