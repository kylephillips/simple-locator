<?php
namespace SimpleLocator\Listeners;

use SimpleLocator\Listeners\AJAXListenerBase;
use SimpleLocator\Services\LocationSearch\LocationSearchValidator;
use SimpleLocator\Services\LocationSearch\LocationSearch;
use SimpleLocator\Services\LocationSearch\JsonResponseFactory;
use SimpleLocator\Services\LocationSearch\SaveSearch;

/**
* Perform a location search based on an API request
*/
class APILocationSearch
{
	/**
	* The Request Array
	*/
	private $request;

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

	/**
	* Search Store
	*/
	private $search_store;

	public function __construct($request)
	{
		$this->request = $request;
		$this->location_search = new LocationSearch;
		$this->search_validator = new LocationSearchValidator;
		$this->response_factory = new JsonResponseFactory;
		$this->search_store = new SaveSearch;
	}

	/**
	* Perform the Search and get the results
	*/
	public function getResults()
	{
		$this->validate();
		$this->location_search->search($this->request);
		$response = $this->response_factory->build(
			$this->location_search->getResults(), 
			$this->location_search->getResultCount(),
			$this->location_search->getTotalResultCount(),
			$this->request
		);
		$this->saveSearch();
		return $response;
	}

	/**
	* Validate the Form Data
	*/
	private function validate()
	{
		try {
			$this->search_validator->validate($this->request);
		} catch ( \Exception $e ){
			throw new \Exception($e->getMessage());
		}
	}

	/**
	* Store the Search
	*/
	private function saveSearch()
	{
		if ( !get_option('wpsl_save_searches') ) return;
		if ( isset($this->request['allow_empty_address']) ) return;
		$this->search_store->save($this->request);
	}
}