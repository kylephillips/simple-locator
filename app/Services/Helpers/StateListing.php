<?php
namespace SimpleLocator\Services\Helpers;

use SimpleLocator\Services\Helpers\States\Us;

/**
* Returns a listing of states by country
*/
class StateListing
{
	/**
	* Get a listing of states
	* Defaults to US
	*/
	public function getStates($request)
	{
		if ( !isset($request['country']) || $request['country'] == '' || $request['country'] == 'us' ) return (new Us)->states();
		
		// First try the country code
		$country_from_code = $this->countryCode($request['country'], null);
		if ( $country_from_code ) {
			$class = 'SimpleLocator\Services\Helpers\States\\' . ucfirst($country_from_code);
			if ( class_exists($class) ) return (new $class)->states();
		}

		// Then try the name
		$country_from_name = $this->countryCode(null, $request['country']);
		if ( $country_from_name ){
			$class = 'SimpleLocator\Services\Helpers\States\\' . ucfirst($country_from_name);
			if ( class_exists($class) ) return (new $class)->states();
		}

		throw new \Exception(__('Country not found', 'simple-locator'));
	}

	/**
	* Loop through country codes (allows searching by code or name)
	*/
	public function countryCode($code = null, $name = null)
	{
		if ( !$code && !$name ) return null;
		$countries = [
			'us' => 'United States',
			'mx' => 'Mexico',
			'ca' => 'Canada'
		];
		foreach ( $countries as $country_code => $country_name ){
			if ( $code && $country_code == strtolower($code) ) return $country_code;
			if ( $name && strtolower($country_name) == strtolower($name) ) return $country_code;
		}
		return false;
	}
}