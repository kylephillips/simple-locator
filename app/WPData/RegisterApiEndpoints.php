<?php
namespace SimpleLocator\WPData;

use SimpleLocator\Listeners\APILocationSearch;
use SimpleLocator\Listeners\APIAllLocations;

/**
* Register the WP API Endpoints for use in the Application
*/ 
class RegisterApiEndpoints 
{
	public function __construct()
	{
		add_action( 'rest_api_init', [$this, 'registerRoutes']);
	}

	public function registerRoutes()
	{
		register_rest_route( 'simplelocator/v2', '/search/', [
			'methods'  => 'GET',
			'callback' => [$this, 'searchLocations'],
		]);
		register_rest_route( 'simplelocator/v2', '/locations/', [
			'methods'  => 'GET',
			'callback' => [$this, 'getLocations'],
		]);
	}

	/**
	* Get locations from a search request
	*/
	public function searchLocations(\WP_REST_Request $request)
	{
		$search = new APILocationSearch($request->get_query_params());
		try {
			$results = $search->getResults();
			return $results;
		} catch ( \Exception $e ){
			return [
				'status' => 'error',
				'message' => $e->getMessage()
			];
		}
	}

	/**
	* Get all locations
	*/
	public function getLocations(\WP_REST_Request $request)
	{
		$locations = new APIAllLocations($request->get_query_params());
		return $locations->getLocations();
	}
}