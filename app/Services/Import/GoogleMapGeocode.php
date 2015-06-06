<?php namespace SimpleLocator\Services\Import;

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
	* Google Response Status
	*/
	private $response_status;

	/**
	*  Geocode the address
	*/
	public function geocode($address)
	{
		$client = new Client();
		$query = '?address=' . $address;
		$apikey = get_option('wpsl_google_api_key');
		
		$response = $client->get('https://maps.googleapis.com/maps/api/geocode/json' . $query);
		$json = $response->json();
		$this->response_status = $json['status'];

		// Testing Query Limit Error
		// if ( $address == '4156 W.E. Heck Court Baton Rouge LA 70816' ){
		// 	$this->response_status = 'OVER_QUERY_LIMIT';
		// }

		if ( $this->response_status == 'OK' ){
			$this->coordinates = array(
				'lat' => $json['results'][0]['geometry']['location']['lat'],
				'lng' => $json['results'][0]['geometry']['location']['lng']
			);
			return true;
		}
		$this->error = __('Google Maps Error', 'wpsimplelocator') . ': ' . $this->response_status;
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

	/**
	* Response Status Getter
	*/
	public function getStatus()
	{
		return $this->response_status;
	}

}