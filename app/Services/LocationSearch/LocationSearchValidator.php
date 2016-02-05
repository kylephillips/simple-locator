<?php 

namespace SimpleLocator\Services\LocationSearch;

class LocationSearchValidator 
{

	public function validate()
	{
		// Unit
		if ( ($_POST['unit'] !== 'miles') && ($_POST['unit'] !== 'kilometers') ){
			throw new \Exception(__('Invalid unit.', 'wpsimplelocator'));
		}

		if ( isset($_POST['allow_empty_address']) && $_POST['allow_empty_address'] == 'true' ) return;

		// Latitude & Longitude
		if ( !is_numeric($_POST['latitude']) || !is_numeric($_POST['longitude']) ) {
			throw new \Exception(__('The address could not be located at this time.', 'wpsimplelocator'));
		}

		// Distance
		if ( !ctype_digit($_POST['distance']) ) {
			throw new \Exception(__('Please enter a valid distance.', 'wpsimplelocator'));
		}
	}
}