<?php 
namespace SimpleLocator\Services\Import\Listeners;

use SimpleLocator\Services\Import\GoogleMapGeocode;

class ApiTest
{
	/**
	* Geocoder
	*/
	private $geocoder;

	public function __construct()
	{
		$this->geocoder = new GoogleMapGeocode;
		$this->runTest();
		return wp_send_json(['status' => 'testing', 'message' => 'testing']);
	}

	private function runTest()
	{
		$address = '1600+Amphitheatre+Parkway,+Mountain+View,+CA';
		try {
			$this->geocoder->geocode($address);
			$this->respond('success', __('Google Maps Geocoder test successful using the provided API key.', 'simple-locator'));
		} catch ( \Exception $e ){
			$this->respond('error', __('Google response: ', 'simple-locator') . $e->getMessage());
		}
	}

	private function respond($status, $message)
	{
		return wp_send_json(['status' => $status, 'message' => $message]);
	}
}