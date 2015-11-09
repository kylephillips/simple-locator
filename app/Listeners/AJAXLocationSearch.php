<?php 

namespace SimpleLocator\Listeners;

use SimpleLocator\Listeners\AJAXListenerBase;
use SimpleLocator\Services\LocationSearch\LocationSearchValidator;
use SimpleLocator\Services\LocationSearch\LocationSearch;
use SimpleLocator\Services\LocationSearch\JsonResponseFactory;

/**
* Front-end form handler for simple locator lookup
* @return JSON Response
*/
class AJAXLocationSearch extends AJAXListenerBase 
{

	/**
	* Location Search Service
	*/
	private $location_search;

	/**
	* Location Search Validator
	*/
	private $search_validator;

	/**
	* Response Factory
	*/
	private $response_factory;

	public function __construct()
	{
		$this->location_search = new LocationSearch;
		$this->search_validator = new LocationSearchValidator;
		$this->response_factory = new JsonResponseFactory;
		parent::__construct();
		$this->validate();
		$this->performSearch();
	}

	/**
	* Validate the Form Data
	*/
	private function validate()
	{
		try {
			$this->search_validator->validate();
		} catch ( \Exception $e ){
			return $this->error($e->getMessage());
		}
	}

	/**
	* Perform the Search
	*/
	private function performSearch()
	{
		$this->location_search->search();
		$response = $this->response_factory->build(
			$this->location_search->getResults(), 
			$this->location_search->getResultCount()
		);
		return $this->respond($response);
	}

}