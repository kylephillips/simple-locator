<?php
namespace SimpleLocator\Repositories;

class SearchHistoryRepository
{
	/**
	* WPDB 
	*/
	private $wpdb;

	/**
	* Search DB Rows
	*/
	private $results;

	public function __construct()
	{
		global $wpdb;
		$this->wpdb = $wpdb;
		$this->setSearchHistory();
	}

	/**
	* Fetch the search history
	*/
	private function setSearchHistory()
	{
		$table = $this->wpdb->prefix . 'simple_locator_history';
		$query = "SELECT * FROM $table";
		if ( isset($_GET['q']) ) :
			$q = sanitize_text_field($_GET['q']);
			$query .= " WHERE (search_term LIKE '%$q%') OR (search_term_formatted LIKE '%$q%');";
		endif;
		$results = $this->wpdb->get_results($query);
		$this->results = $results;
	}

	/**
	* Get the total number of searches
	*/
	public function getTotalCount()
	{
		return count($this->results);
	}

	/**
	* Get all the searches
	*/
	public function getAllSearches()
	{
		return $this->results;
	}
}