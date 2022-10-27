<?php
namespace SimpleLocator\Listeners;

use SimpleLocator\Services\LocationSearch\LocationSearch as Search;
use SimpleLocator\Services\LocationSearch\LocationSearchValidator;
use SimpleLocator\Services\LocationSearch\SaveSearch;
use SimpleLocator\Services\LocationSearch\ResultsInfoPresenter;
use SimpleLocator\Repositories\SettingsRepository;

/**
* Display Search Results for Non-Ajax Forms
*/
class LocationSearch
{
	/**
	* Settings Repository
	*/
	private $settings_repo;

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
	* Pagination Presenter Class
	* @var SimpleLocator\Services\LocationSearch\ResultsPaginationPresenter
	*/
	private $results_info;

	/**
	* Search Data
	*/
	private $search_data;

	public function __construct()
	{
		if ( !isset($_POST['simple_locator_results']) && !isset($_GET['simple_locator_results']) ) return;
		$this->settings_repo = new SettingsRepository;
		$this->location_search = new Search;
		$this->validator = new LocationSearchValidator;
		$this->save_search = new SaveSearch;
		add_action('init', [$this, 'initialize']);
		add_action('wp_head', [$this, 'addScriptData']);
		add_filter('the_content', [$this, 'displayResults']);
		add_action('simple_locator_results', [$this, 'resultsAction'], 10);
	}

	public function initialize()
	{
		$this->setRequest();
		$this->search();
		$this->saveSearch();
		do_action('simple_locator_results_loaded', $this->search_data, $this->request);
		$this->results_info = new ResultsInfoPresenter($this->request, $this->search_data);	
	}

	public function addScriptData()
	{
		wp_enqueue_script('google-maps');
		wp_enqueue_script('simple-locator');

		global $post;
		if ( $this->request['resultspage'] != $post->ID ) return;
		if ( count($this->search_data['results']) < 1 ) return;
		$scripts = '<script>var simple_locator_results = [';
		foreach($this->search_data['results'] as $key => $result){
			$scripts .= '{id : ' . $result['id'] . ', title : "' . $result['title'] . '", lat : ' . $result['latitude'] . ', lng : ' . $result['longitude'] . ', mappin : "' . $result['mappin'] . '", infowindow : "' . str_replace('"', '\"', $result['infowindow']) . '"},';
		}
		$scripts .= ']</script>';
		echo $scripts;
	}

	public function displayResults($content)
	{
		global $post;
		if ( $this->request['resultspage'] != $post->ID ) return $content;
		include(\SimpleLocator\Helpers::view('search-results'));
		if ( $this->settings_repo->resultsInContent() ){
			$content .= apply_filters('simple_locator_results_non_ajax', $output, $this->search_data, $this->request);
			return $content;
		}
	}

	/**
	* Results Action
	* 
	*/
	public function resultsAction()
	{
		global $post;
		if ( $this->request['resultspage'] != $post->ID ) return;
		if ( $this->settings_repo->resultsInContent() ) return;
		$results_output = include(\SimpleLocator\Helpers::view('search-results'));
		echo apply_filters('simple_locator_results_non_ajax', $output, $this->search_data, $this->request);
	}

	/**
	* Set the request data
	*/
	private function setRequest()
	{
		$temp_request = ( isset($_GET['method']) ) ? $_GET : $_POST;
		$this->request = [];
		$this->request['page'] = ( isset($temp_request['page_num']) ) 
			? intval(sanitize_text_field($temp_request['page_num'])) : 1;
		if ( !is_numeric($this->request['page']) || $this->request['page'] < 1 ) $this->request['page'] = 1;
		$this->request['per_page'] = ( isset($temp_request['per_page']) ) 
			? intval(sanitize_text_field($temp_request['per_page'])) : get_option('posts_per_page');
		$this->request['address'] = ( isset($temp_request['address']) ) 
			? sanitize_text_field($temp_request['address']) : null;
		$this->request['formatted_address'] = ( isset($temp_request['formatted_location']) ) 
			? sanitize_text_field($temp_request['formatted_location']) : null;
		$this->request['distance'] = ( isset($temp_request['wpsl_distance']) ) 
			? intval(sanitize_text_field($temp_request['wpsl_distance'])) : null;
		$this->request['latitude'] = ( isset($temp_request['latitude']) ) 
			? sanitize_text_field($temp_request['latitude']) : null;
		$this->request['longitude'] = ( isset($temp_request['longitude']) ) 
			? sanitize_text_field($temp_request['longitude']) : null;
		$this->request['unit'] = ( isset($temp_request['unit']) ) 
			? sanitize_text_field($temp_request['unit']) : 'miles';
		$this->request['geolocation'] = ( isset($temp_request['geolocation']) && $temp_request['geolocation'] == '1' ) ? true : false;
		$this->request['search_page'] = ( isset($temp_request['search_page']) ) 
			? intval(sanitize_text_field($temp_request['search_page'])) : null;
		$this->request['resultspage'] = ( isset($temp_request['results_page']) ) 
			? intval(sanitize_text_field($temp_request['results_page'])) : null;
		$this->request['allow_empty_address'] = ( isset($temp_request['allow_empty_address']) && $temp_request['allow_empty_address'] == 'true' ) ? true : false;
		$this->request['formmethod'] = ( isset($temp_request['method']) && $temp_request['method'] == 'post' ) ? 'post' : 'get';
		$this->request['mapheight'] = ( isset($temp_request['mapheight']) ) 
			? intval(sanitize_text_field($temp_request['mapheight'])) : 250;
		$this->request['taxfilter'] = null;
		if ( isset($temp_request['taxfilter']) && is_array($temp_request['taxfilter']) ){
			$taxfilter = [];
			foreach ( $temp_request['taxfilter'] as $taxonomy => $terms ){
				if ( is_array($terms) ){
					foreach ( $terms as $term ){
						$taxfilter[sanitize_text_field($taxonomy)][] = sanitize_text_field($term);
					}
				} else {
					$taxfilter[sanitize_text_field($taxonomy)] = sanitize_text_field($terms);
				}
			}
			$this->request['taxfilter'] = $taxfilter;
		}
		$this->request['new_search'] = ( isset($temp_request['back']) || isset($temp_request['next']) ) ? false : true;
		$this->formatTaxonomies();
	}

	/**
	* Format Taxonomies
	*/
	private function formatTaxonomies()
	{
		if ( !$this->request['taxfilter'] ) return;
		$tax_array = [];
		foreach ( $this->request['taxfilter'] as $tax => $term_id ){
			if ( !is_array($term_id) ){
				$tax_array[$tax] = [$term_id];
				continue;
			}
			foreach ( $term_id as $id ){
				$tax_array[$tax][] = $id;
			}
		}
		$this->request['taxfilter'] = $tax_array;
	}

	/**
	* Perform the search
	*/
	private function search()
	{
		$this->location_search->search($this->request);
		$this->search_data['results'] = $this->location_search->getResults();
		$this->search_data['total_results'] = $this->location_search->getTotalResultCount();
		if ( $this->request['per_page'] == 0 ) {
			$this->search_data['max_num_pages'] = 1;
			return;
		}
		$this->search_data['max_num_pages'] = ceil($this->search_data['total_results'] / $this->request['per_page']);
		if ( $this->request['page'] > $this->search_data['max_num_pages'] ){
			$this->request['page'] = $this->search_data['max_num_pages'];
			$this->search();
		}
	}

	/**
	* Save the search
	*/
	private function saveSearch()
	{
		if ( !$this->request['new_search'] ) return;
		if ( !get_option('wpsl_save_searches') ) return;
		$this->save_search->save($this->request);
	}
}