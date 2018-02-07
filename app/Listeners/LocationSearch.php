<?php
namespace SimpleLocator\Listeners;

use SimpleLocator\Services\LocationSearch\LocationSearch as Search;
use SimpleLocator\Services\LocationSearch\LocationSearchValidator;
use SimpleLocator\Services\LocationSearch\SaveSearch;

/**
* Display Search Results for Non-Ajax Forms
*/
class LocationSearch
{
	/**
	* Request
	*/ 
	private $request;

	/**
	* Location Search Object
	* @var SimpleLocator\Services\LocationSearch\LocationSearch
	*/
	private $location_search;

	/**
	* Validator
	* @var SimpleLocator\Services\LocationSearch\LocationSearchValidator
	*/
	private $validator;

	/**
	* Search Storing Class
	* @var SimpleLocator\Services\LocationSearch\SaveSearch
	*/
	private $save_search;

	/**
	* Search Data
	*/
	private $search_data;

	public function __construct()
	{
		if ( !isset($_POST['simple_locator_results']) && !isset($_GET['simple_locator_results']) ) return;
		$this->location_search = new Search;
		$this->validator = new LocationSearchValidator;
		$this->save_search = new SaveSearch;
		add_action('init', [$this, 'initialize']);
		add_action('wp_head', [$this, 'addScriptData']);
		add_filter('the_content', [$this, 'displayResults']);
		
	}

	public function initialize()
	{
		$this->setRequest();
		$this->search();		
	}

	public function addScriptData()
	{
		global $post;
		if ( $this->request['results_page'] != $post->ID ) return;
		$scripts = '<script>var simple_locator_results = [';
		foreach($this->search_data['results'] as $key => $result){
			$scripts .= '{id : ' . $result['id'] . ', title : "' . $result['title'] . '", lat : ' . $result['latitude'] . ', lng : ' . $result['longitude'] . ', infowindow : "' . str_replace('"', '\"', $result['infowindow']) . '"}';
			if ( ($key + 1) > count($this->search_data['results']) ) $scripts .= ',';
		}
		$scripts .= ']; console.log(simple_locator_results);</script>';
		echo $scripts;
	}

	public function displayResults($content)
	{
		global $post;
		if ( $this->request['results_page'] != $post->ID ) return $content;
		include(\SimpleLocator\Helpers::view('search-results'));
		$content .= apply_filters('simple_locator_results_non_ajax', $output, $this->request);
		return $content;
	}

	/**
	* Set the request data
	*/
	private function setRequest()
	{
		$temp_request = ( isset($_GET['method']) ) ? $_GET : $_POST;
		$this->request = [];
		$this->request['page'] = ( isset($temp_request['page']) ) ? intval($temp_request['page']) : 0;
		$this->request['per_page'] = ( isset($temp_request['per_page']) ) ? intval($temp_request['per_page']) : get_option('posts_per_page');
		$this->request['address'] = ( isset($temp_request['address']) ) ? sanitize_text_field($temp_request['address']) : null;
		$this->request['formatted_address'] = ( isset($temp_request['formatted_location']) ) ? sanitize_text_field($temp_request['formatted_location']) : null;
		$this->request['distance'] = ( isset($temp_request['distance']) ) ? intval($temp_request['distance']) : null;
		$this->request['latitude'] = ( isset($temp_request['latitude']) ) ? $temp_request['latitude'] : null;
		$this->request['longitude'] = ( isset($temp_request['longitude']) ) ? $temp_request['longitude'] : null;
		$this->request['unit'] = ( isset($temp_request['unit']) ) ? $temp_request['unit'] : 'miles';
		$this->request['geolocation'] = ( isset($temp_request['geolocation']) && $temp_request['geolocation'] == 'true' ) ? true : false;
		$this->request['search_page'] = ( isset($temp_request['search_page']) ) ? intval($temp_request['search_page']) : null;
		$this->request['results_page'] = ( isset($temp_request['results_page']) ) ? intval($temp_request['results_page']) : null;
		$this->request['allow_empty_address'] = ( isset($temp_request['allow_empty_address']) && $temp_request['allow_empty_address'] == 'true' ) ? true : false;
		// 'taxonomies' => $taxonomies,
	}

	/**
	* Perform the search
	*/
	private function search()
	{
		$this->location_search->search($this->request);
		$this->search_data['results'] = $this->location_search->getResults();
		$this->search_data['total_results'] = $this->location_search->getTotalResultCount();
	}
}