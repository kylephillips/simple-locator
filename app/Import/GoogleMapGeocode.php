<?php namespace SimpleLocator\Import;

use GuzzleHttp\Client;

/**
* Geocode an address
*/
class GoogleMapGeocode {

	/**
	* Coordinates
	* @var array
	*/
	private $coordinates;

	/**
	* Error
	*/
	private $error;

	/**
	*  Geocode the address
	*/
	public function geocode($address)
	{
		$client = new Client();
		$query = '?address=5395+Sugarloaf+Pkwy+Lawrenceville+GA';
		$apikey = get_option('wpsl_google_api_key');
		//if ( $apikey !== "" ) $query .= '&key=' . $apikey;
		$response = $client->get('https://maps.googleapis.com/maps/api/geocode/json' . $query);
		$json = $response->json();
		if ( $json['status'] == 'OK' ){
			$this->coordinates = array(
				'lat' => $json['results'][0]['geometry']['location']['lat'],
				'lng' => $json['results'][0]['geometry']['location']['lng']
			);
			return true;
		}
		$this->error = __('Google Maps Error:', 'wpsimplelocator') . $json['status'];
		return false;
	}

	/**
	* Coordinates Getter
	*/
	public function getCoordinates()
	{
		return $this->coordinates;
	}

	/**
	* Error getter
	*/
	public function getError()
	{
		return $this->error;
	}

}