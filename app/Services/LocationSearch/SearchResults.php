<?php
namespace SimpleLocator\Services\LocationSearch;

/**
* Display Search Results for Non-Ajax Forms
*/
class SearchResults
{
	/**
	* Request
	*/ 
	private $request;

	public function __construct()
	{
		add_filter('the_content', [$this, 'displayResults']);
	}

	public function displayResults($content)
	{
		if ( !isset($_GET['simple_locator_results']) ) return $content;
		var_dump($_GET);
		$this->setRequest();
		return $content;
	}

	private function setRequest()
	{
		$this->request = [];
		$this->request['page'] = ( isset($_GET['page']) ) ? intval($_GET['page']) : 1;
		$this->request['per_page'] = ( isset($_GET['per_page']) ) ? intval($_GET['per_page']) : get_option('posts_per_page');
	}
}