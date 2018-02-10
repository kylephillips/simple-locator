<?php
namespace SimpleLocator\Listeners;

use \SimpleLocator\Repositories\PostRepository;

/**
* Gets array of all locations
*/
class APIAllLocations
{
	/**
	* All Locations
	*/
	private $locations;

	/**
	* Request
	*/
	private $request;

	/**
	* Post Repository
	*/
	private $post_repo;

	public function __construct($request)
	{
		$this->post_repo = new PostRepository;
		$this->request = $request;
	}

	/**
	* Get all locations
	*/
	public function getLocations()
	{
		$limit = ( isset($this->request['limit']) ) ? intval($this->request['limit']) : -1;
		$this->locations = $this->post_repo->allLocations($limit);
		return $this->locations;
	}
}