<?php
namespace SimpleLocator\WPData;

use SimpleLocator\Listeners\APILocationSearch;

/**
* Register the WP API Endpoints for use in the Applications
*/ 
class RegisterEndpoints 
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
	}

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
}