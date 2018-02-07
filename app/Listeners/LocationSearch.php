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
		wp_enqueue_script('google-maps');
		wp_enqueue_script('simple-locator');

		global $post;
		if ( $this->request['resultspage'] != $post->ID ) return;
		$scripts = '<script>var simple_locator_results = [';
		foreach($this->search_data['results'] as $key => $result){
			$scripts .= '{id : ' . $result['id'] . ', title : "' . $result['title'] . '", lat : ' . $result['latitude'] . ', lng : ' . $result['longitude'] . ', infowindow : "' . str_replace('"', '\"', $result['infowindow']) . '"},';
		}
		$scripts .= '];</script>';
		echo $scripts;
	}

	public function displayResults($content)
	{
		global $post;
		if ( $this->request['resultspage'] != $post->ID ) return $content;
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
		$this->request['page'] = ( isset($temp_request['page_num']) ) ? intval($temp_request['page_num']) : 0;
		$this->request['per_page'] = ( isset($temp_request['per_page']) ) ? intval($temp_request['per_page']) : get_option('posts_per_page');
		$this->request['address'] = ( isset($temp_request['address']) ) ? sanitize_text_field($temp_request['address']) : null;
		$this->request['formatted_address'] = ( isset($temp_request['formatted_location']) ) ? sanitize_text_field($temp_request['formatted_location']) : null;
		$this->request['distance'] = ( isset($temp_request['distance']) ) ? intval($temp_request['distance']) : null;
		$this->request['latitude'] = ( isset($temp_request['latitude']) ) ? $temp_request['latitude'] : null;
		$this->request['longitude'] = ( isset($temp_request['longitude']) ) ? $temp_request['longitude'] : null;
		$this->request['unit'] = ( isset($temp_request['unit']) ) ? $temp_request['unit'] : 'miles';
		$this->request['geolocation'] = ( isset($temp_request['geolocation']) && $temp_request['geolocation'] == 'true' ) ? true : false;
		$this->request['search_page'] = ( isset($temp_request['search_page']) ) ? intval($temp_request['search_page']) : null;
		$this->request['resultspage'] = ( isset($temp_request['results_page']) ) ? intval($temp_request['results_page']) : null;
		$this->request['allow_empty_address'] = ( isset($temp_request['allow_empty_address']) && $temp_request['allow_empty_address'] == 'true' ) ? true : false;
		$this->request['formmethod'] = ( isset($temp_request['method']) && $temp_request['method'] == 'post' ) ? 'post' : 'get';
		$this->request['mapheight'] = ( isset($temp_request['mapheight']) ) ? intval($temp_request['mapheight']) : 250;
		// 'taxonomies' => $taxonomies,
	}

	/**
	* Pagination Fields
	*/
	private function paginationForm($direction = 'next')
	{
		if ( $this->request['per_page'] == 0 ) return;
		if ( $direction == 'back' && $this->request['page'] == 0 ) return;
		if ( $direction == 'next' && ( ($this->request['page'] + 1 ) == $this->search_data['max_num_pages']) ) return;
		$output = '<form method="' . $this->request['formmethod'] . '" action="' . get_the_permalink($this->request['resultspage']) . '" class="simple-locator-pagination-form';
		if ( $this->request['allow_empty_address'] == 'true' ) $output .= ' allow-empty';
		$output .= '">';
		$page .= ( $direction == 'next' ) ? $this->request['page'] + 1 : $this->request['page'] - 1;
		$output .= '
			<input type="hidden" name="page_num" value="' . $page . '">
			<input type="hidden" name="per_page" value="' . $this->request['per_page'] . '">
			<input type="hidden" name="address" value="' . $this->request['address'] . '">
			<input type="hidden" name="formatted_address" value="' . $this->request['formatted_address'] . '" />
			<input type="hidden" name="distance" value="' . $this->request['distance'] . '" />
			<input type="hidden" name="latitude" value="' . $this->request['latitude'] . '" />
			<input type="hidden" name="longitude" value="' . $this->request['longitude'] . '" />
			<input type="hidden" name="unit" value="' . $this->request['unit'] . '" />
			<input type="hidden" name="geolocation" value="' . $this->request['geolocation'] . '">
			<input type="hidden" name="search_page" value="' . $this->request['search_page'] . '" />
			<input type="hidden" name="results_page" value="' . $this->request['resultspage'] . '" />
			<input type="hidden" name="allow_empty_address" value="' . $this->request['allow_empty_address'] . '" />
			<input type="hidden" name="method" value="' . $this->request['formmethod'] . '" />
			<input type="hidden" name="mapheight" value="' . $this->request['mapheight'] . '" />
			<input type="hidden" name="simple_locator_results" value="true" />
		';
		$button_text = ( $direction == 'next' ) ? __('Next', 'simple-locator') : __('Back', 'simple-locator');
		$output .= '<input type="submit" class="button simple-locator-submit-button" value="' . $button_text . '">';
		$output .= '</form>';
		return $output;
	}

	/**
	* Perform the search
	*/
	private function search()
	{
		$this->location_search->search($this->request);
		$this->search_data['results'] = $this->location_search->getResults();
		$this->search_data['total_results'] = $this->location_search->getTotalResultCount();
		if ( $this->request['per_page'] == 0 ) return;
		$this->search_data['max_num_pages'] = ceil($this->search_data['total_results'] / $this->request['per_page']);
	}

	/**
	* Result Header
	*/
	private function resultsHeader()
	{
		$total_results = $this->search_data['total_results'];
		$total_results = ( $total_results == 1 ) 
			? $total_results . ' ' . apply_filters('simple_locator_non_ajax_location_text', __('location', 'simple-locator') )
			: $total_results . ' ' . apply_filters('simple_locator_non_ajax_locations_text', __('locations', 'simple-locator'));
		$output = '<h3 class="wpsl-results-header">' . $total_results . ' ' . __('found within', 'simple-locator') . ' ' . $this->request['distance'] . ' ' . $this->request['unit'] . ' ' . __('of', 'simple-locator') . ' ' . $this->request['address'] . '</h3>';
		return apply_filters('simple_locator_non_ajax_results_header', $output, $this->request, $this->search_data);
	}

	/**
	* Get the current result counts
	*/
	private function currentResultCounts()
	{
		if ( $this->request['per_page'] == 0 ) return;
		$current_start = $this->request['page'] * $this->request['per_page'] + 1;
		$current_end = $current_start + count($this->search_data['results']) - 1;
		$result_count = $current_start;
		if ( $current_start != $current_end ) $result_count .= '&ndash;' . $current_end;
		$result_text = ( $current_start != $current_end ) ? __('Showing results', 'simple-locator') : __('Showing result', 'simple-locator');
		$output = '<p class="wpsl-results-current-count"><em>' . $result_text . ' ' . $result_count . ' ' . __('of', 'simple-locator') . ' ' . $this->search_data['total_results'] . '</em></p>';
		return apply_filters('simple_locator_non_ajax_current_count', $output, $current_start, $current_end, $this->search_data['total_results']);
	}

	/**
	* Page Position
	*/
	private function pagePosition()
	{
		if ( $this->request['per_page'] == 0 ) return;
		$output = '<div class="simple-locator-form-page-selection">';
		$output .= '<p>' . __('Page', 'simple-locator') . ' ' . ($this->request['page'] + 1) . ' ' . __('of', 'simple-locator') . ' ' . $this->search_data['max_num_pages'] . '</p>';
		$output .= '</div>';
		return apply_filters('simple_locator_non_ajax_page_position', $output, $this->request, $this->search_data);
	}
}