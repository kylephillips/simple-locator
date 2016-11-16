<?php 

namespace SimpleLocator\Listeners;

use SimpleLocator\Listeners\AJAXListenerBase;
use SimpleLocator\Services\LocationSearch\LocationSearchValidator;
use SimpleLocator\Services\LocationSearch\LocationSearch;
use SimpleLocator\Services\LocationSearch\JsonResponseFactory;
use SimpleLocator\Services\LocationSearch\StoreSearch;

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

	/**
	* Search Store
	*/
	private $search_store;

	public function __construct()
	{
		$this->location_search = new LocationSearch;
		$this->search_validator = new LocationSearchValidator;
		$this->response_factory = new JsonResponseFactory;
		$this->search_store = new StoreSearch;
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
		$this->storeSearch();
		return $this->respond($response);
	}

	/**
	* Store the Search
	*/
	private function storeSearch()
	{
		if ( !get_option('wpsl_save_searches') ) return;
		$this->search_store->save();
	}

}