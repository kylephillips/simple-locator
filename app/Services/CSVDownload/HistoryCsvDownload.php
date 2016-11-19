<?php
namespace SimpleLocator\Services\CSVDownload;

use SimpleLocator\Repositories\SearchHistoryRepository;

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
	}

	private function getResults()
	{
		var_dump($_POST);
		$this->results = $this->search_history_repo->setSearchHistory('POST');
		var_dump($this->results); die();
	}
}