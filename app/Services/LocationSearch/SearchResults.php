<?php
namespace SimpleLocator\Services\LocationSearch;

/**
* Display Search Results for Non-Ajax Forms
*/
class SearchResults
{
	public function __construct()
	{
		add_filter('the_content', [$this, 'displayResults']);
	}

	public function displayResults($content)
	{
		if ( !isset($_GET['simple_locator_results']) ) return $content;
		var_dump($_GET);
		return $content;
	}
}