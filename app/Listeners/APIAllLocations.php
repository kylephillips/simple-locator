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
		$this->formatInfoWindows();
		return $this->locations;
	}

	/**
	* Apply Infowindow Formatting to locations
	*/
	private function formatInfoWindows()
	{
		foreach ($this->locations as $key => $location) :
			$infowindow = '<div data-result="' . $key . '"><h4>'. $location->title . '</h4><p><a href="' . $location->permalink . '" data-location-id="'. $location->id .'">' . __('View Location', 'simple-locator') . '</a></p></div>';
			$infowindow = apply_filters('simple_locator_infowindow', $infowindow, $location, $key);
			$this->locations[$key]->infowindow = $infowindow;
		endforeach;
	}
}