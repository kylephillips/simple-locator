<?php
namespace SimpleLocator\WPData;

use SimpleLocator\Listeners\APILocationSearch;
use SimpleLocator\Listeners\APIAllLocations;

/**
* Register the WP API Endpoints for use in the Applications
*/ 
class RegisterApiEndpoints 
{
	public function __construct()
	{
		add_action( 'rest_api_init', [$this, 'registerRoutes']);
	}

	public function registerRoutes()
	{
		register_rest_route( 'simplelocator/v2', '/locations/', [
			'methods'  => 'GET',
			'callback' => [$this, 'getLocations'],
		]);
		register_rest_route( 'simplelocator/v2', '/all-locations/', [
			'methods'  => 'GET',
			'callback' => [$this, 'getAllLocations'],
		]);
	}

	/**
	* Get locations from a search request
	*/
	public function getLocations(\WP_REST_Request $request)
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
	public function getAllLocations(\WP_REST_Request $request)
	{
		$locations = new APIAllLocations($request->get_query_params());
		return $locations->getLocations();
	}
}