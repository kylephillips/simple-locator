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
	public function setSearchHistory($request_type = 'GET')
	{
		$request = array();
		if ( $request_type == 'GET' ){
			if ( isset($_GET['q']) && $_GET['q'] !== '' ) $request['q'] = $_GET['q'];
			if ( isset($_GET['date_start']) && $_GET['date_start'] !== '' ) $request['date_start'] = $_GET['date_start'];
			if ( isset($_GET['date_end']) && $_GET['date_end'] !== '' ) $request['date_end'] = $_GET['date_end'];
		}
		if ( $request_type == 'POST' ){
			if ( isset($_POST['q']) && $_POST['q'] !== '' ) $request['q'] = $_POST['q'];
			if ( isset($_POST['date_start']) && $_POST['date_start'] !== '' ) $request['date_start'] = $_POST['date_start'];
			if ( isset($_POST['date_end']) && $_POST['date_end'] !== '' ) $request['date_end'] = $_POST['date_end'];
		}
		

		$table = $this->wpdb->prefix . 'simple_locator_history';
		$query = "SELECT * FROM $table";
		
		if ( isset($request['q']) ) :
			$q = sanitize_text_field($request['q']);
			$query .= " WHERE (search_term LIKE '%$q%') OR (search_term_formatted LIKE '%$q%')";
		endif;

		if ( isset($request['date_start']) ) :
			$date_start = strtotime('+1 day', $request['date_start']);
			$query .= ( !isset($q) ) ? ' WHERE' : ' AND ';
			$query .= ' unix_timestamp(time) >= ' . $date_start;
		endif;

		if ( isset($request['date_end']) ) :
			$date_end = strtotime('+1 day', $request['date_end']);
			$query .= ( !isset($q) && !isset($date_start) ) ? ' WHERE' : ' AND';
			$query .= ' unix_timestamp(time) <= ' . $date_end;
		endif;

		$query .= ';';

		$results = $this->wpdb->get_results($query);
		$this->results = $results;

		return $this->results;
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