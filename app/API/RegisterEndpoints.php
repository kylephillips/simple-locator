<?php
namespace SimpleLocator\API;

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
		$params = $request->get_query_params();
		return $params;
	}
}