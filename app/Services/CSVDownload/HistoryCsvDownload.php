<?php
namespace SimpleLocator\Services\CSVDownload;

use SimpleLocator\Repositories\SearchHistoryRepository;
use League\Csv\Writer;

class HistoryCsvDownload
{
	/**
	* Search History Repo
	*/
	private $search_history_repo;

	/**
	* Results
	*/
	private $results;

	public function __construct()
	{
		$this->search_history_repo = new SearchHistoryRepository;
		$this->getResults();
		$this->generateCsv();
	}

	/**
	* Get the Search History based on post params
	*/
	private function getResults()
	{
		$this->search_history_repo->setSearch('POST', false);
		$this->results = $this->search_history_repo->getResults();
	}

	/**
	* Generate the CSV
	*/
	private function generateCsv()
	{
		$csv = Writer::createFromFileObject(new \SplTempFileObject());
		
		// Header Row
		$csv->insertOne([
			__('Time', 'wpsimplelocator'), 
			__('User IP', 'wpsimplelocator'), 
			__('Search Latitude', 'wpsimplelocator'), 
			__('Search Longitude', 'wpsimplelocator'), 
			__('Search Term', 'wpsimplelocator'), 
			__('Search Term Formatted', 'wpsimplelocator'), 
			__('Distance', 'wpsimplelocator')
		]);

		foreach ( $this->results as $result ){
			$csv->insertOne([
				$result->time, 
				$result->user_ip, 
				$result->search_lat, 
				$result->search_lng, 
				$result->search_term, 
				$result->search_term_formatted, 
				$result->distance
			]);
		}

		$filename = __('location-search-history', 'wpsimplelocator') . '.csv';
		$csv->output($filename);
	}
}