<?php 

namespace SimpleLocator\Services\Import;

use GuzzleHttp\Client;
use SimpleLocator\Services\Import\Exceptions\GoogleQueryLimitException;
use SimpleLocator\Services\Import\Exceptions\GoogleAPIException;
use SimpleLocator\Services\Import\Exceptions\GoogleRequestDeniedException;

/**
* Geocode an address
*/
class GoogleMapGeocode 
{

	/**
	* Coordinates
	* @var array
	*/
	private $coordinates;

	/**
	* Error
	* @var string
	*/
	private $error;

	/**
	*  Geocode the address
	*/
	public function geocode($address)
	{
		$apikey = get_option('wpsl_google_geocode_api_key');
		$client = new Client();		
		$response = $client->get('https://maps.googleapis.com/maps/api/geocode/json', [
			'query' => [
				'address' => $address,
				'key' => $apikey
			]
		]);
		$json = $response->json();
		$response_status = $json['status'];

		//if ( $address == '5395 Sugarloaf Pkwy Lawrenceville GA 30043' ) $response_status = 'OVER_QUERY_LIMIT';

		if ( $response_status == 'OVER_QUERY_LIMIT' ){
			throw new GoogleQueryLimitException(__('Your API limit has been reached. Try again in 24 hours.', 'wpsimplelocator'));
		}

		if ( $response_status == 'REQUEST_DENIED' ){
			throw new GoogleRequestDeniedException(__('Google Maps Error', 'wpsimplelocator') . ': ' . $json['error_message']);
		}

		if ( $response_status !== 'OK' ) {
			throw new GoogleAPIException(__('Google Maps Error', 'wpsimplelocator') . ': ' . $response_status);
			return false;
		}

		$this->coordinates = array(
			'lat' => $json['results'][0]['geometry']['location']['lat'],
			'lng' => $json['results'][0]['geometry']['location']['lng']
		);
		return true;
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