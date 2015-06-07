<?php namespace SimpleLocator\Listeners;

use SimpleLocator\Listeners\AJAXListenerBase;
use SimpleLocator\Services\LocationSearch\LocationSearch;

/**
* Front-end form handler for simple locator lookup
* @return JSON Response
*/
class AJAXLocationSearch extends AJAXListenerBase {

	/**
	* Location Search Service
	*/
	private $location_search;

	public function __construct()
	{
		$this->location_search = new LocationSearch;
		parent::__construct();
		$this->performSearch();
	}

	/**
	* Perform the Search
	*/
	private function performSearch()
	{
		try {
			$this->location_search->search();
			return $this->respond($this->location_search->getResults());
		} catch ( \Exception $e ){
			return $this->error($e->getMessage());
		}
	}

}


