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
		
		if ( isset($_GET['q']) && $_GET['q'] !== '' ) :
			$q = sanitize_text_field($_GET['q']);
			$query .= " WHERE (search_term LIKE '%$q%') OR (search_term_formatted LIKE '%$q%');";
		endif;

		if ( isset($_GET['date_start']) && $_GET['date_start'] !== '' ) :
			$date_start = intval($_GET['date_start']);
			$query .= ( !isset($q) ) ? ' WHERE' : ' AND WHERE';
			$query .= ' unix_timestamp(time) >= ' . $date_start;
		endif;

		if ( isset($_GET['date_end']) && $_GET['date_end'] !== '' ) :
			$date_end = intval($_GET['date_end']);
			$query .= ( !isset($q) && !isset($date_start) ) ? ' WHERE' : ' AND WHERE';
			$query .= ' unix_timestamp(time) <= ' . $date_end;
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