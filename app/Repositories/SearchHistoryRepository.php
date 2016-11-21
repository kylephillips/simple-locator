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
	}

	/**
	* Fetch the search history
	*/
	public function setSearch($request_type = 'GET', $paginated = true)
	{
		$request = array();
		$offset = null;
		$per_page = ( isset($_GET['per_page']) ) ? intval($_GET['per_page']) : 20;

		if ( isset($_GET['p']) && $paginated ){
			$page = intval($_GET['p']);
			$offset = $per_page * ( $page - 1 );
		}

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

		$query .= ' ORDER BY time DESC';

		if ( $paginated ) :
			if ( $offset ) $query .= ' LIMIT ' . $offset . ',' . $per_page;
			if ( !$offset ) $query .= ' LIMIT ' . $per_page;
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
	public function getResults()
	{
		return $this->results;
	}

	/**
	* Get the total number of pages
	*/
	public function totalPages()
	{
		$this->setSearch('GET', false);
		$total_results = $this->getTotalCount();
		
		$query = $_GET;
		$per_page = ( isset($query['per_page']) && $query['per_page'] !== '' ) ? intval($query['per_page']) : 20;

		$total_pages = ceil($total_results / $per_page);
		return $total_pages;
	}

	/**
	* Pagination
	*/
	public function pagination($prev = null, $next = null, $link_class = null)
	{
		if ( !isset($prev) ) $prev = __('Previous', 'wpsimplelocator');
		if ( !isset($next) ) $next = __('Next', 'wpsimplelocator');
		if ( !isset($link_class) ) $link_class = 'button';

		$query = $_GET;
		$first_page = true;

		$current_page = ( isset($query['p']) && $query['p'] !== '' ) ? intval($query['p']) : 1;
		
		if ( $current_page > 1 ){
			$first_page = false;
			$query['p'] = intval($current_page) - 1;
			$prev_page_query = http_build_query($query);
		}

		$query['p'] = intval($current_page) + 1;
		$next_page_query = http_build_query($query);

		$per_page = ( isset($query['per_page']) && $query['per_page'] !== '' ) ? intval($query['per_page']) : 0;
		$total_results = $this->getTotalCount();
		$out = '<ul class="pagination-list">';
			if ( !$first_page ) $out .= '<li><a href="' . $_SERVER['PHP_SELF'] . '?' . $prev_page_query . '" class="' . $link_class . '">' . $prev . '</a></li>';
			if ( $current_page < $this->totalPages() ) $out .= '<li><a href="' . $_SERVER['PHP_SELF'] . '?' . $next_page_query . '" class="' . $link_class . '">' . $next . '</a></li>';
		$out .= '</ul>';
		return $out;
	}
}