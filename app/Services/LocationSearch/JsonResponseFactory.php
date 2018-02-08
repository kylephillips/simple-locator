<?php 
namespace SimpleLocator\Services\LocationSearch;

use SimpleLocator\Services\LocationSearch\ResultsInfoPresenter;

/**
* Build the JSON Response Array for a Location Search
*/
class JsonResponseFactory 
{
	/**
	* Response Data
	*/
	private $data;

	/**
	* Request
	*/
	private $request;

	/**
	* Set the Response Data
	*/
	private function setData($results, $total_count = 0)
	{
		$taxonomies = ( isset($this->request['taxonomies']) ) ? $this->request['taxonomies'] : null;
		$address = ( isset($this->request['address']) ) ? sanitize_text_field($this->request['address']) : null;
		$formatted_address = ( isset($this->request['formatted_address']) ) ? sanitize_text_field($this->request['formatted_address']) : null;
		$geolocation = ( isset($this->request['geolocation']) && $this->request['geolocation'] == 'true' ) ? true : false;
		$allow_empty_address = ( isset($this->request['allow_empty_address']) && $this->request['allow_empty_address'] == 'true' ) ? true : false;
		$page = ( isset($this->request['page']) ) ? intval($this->request['page']) : null;
		$per_page = ( isset($this->request['per_page']) ) ? intval($this->request['per_page']) : -1;

		// Additional Pagination/etc…
		$search_data = [];
		$search_data['results'] = $results;
		$search_data['total_results'] = $total_count;
		if ( $this->request['per_page'] > 0 ) $search_data['max_num_pages'] = ceil($total_count / $this->request['per_page']);
		$result_info_presenter = new ResultsInfoPresenter($this->request, $search_data);

		$this->data = [
			'address' => $address,
			'formatted_address' => $formatted_address,
			'distance' => sanitize_text_field($this->request['distance']),
			'latitude' => sanitize_text_field($this->request['latitude']),
			'longitude' => sanitize_text_field($this->request['longitude']),
			'unit' => sanitize_text_field($this->request['unit']),
			'geolocation' => $geolocation,
			'taxonomies' => $taxonomies,
			'allow_empty_address' => $allow_empty_address,
			'page' => $page,
			'per_page' => $per_page,
			'results_header' => $result_info_presenter->resultsHeader(),
			'current_counts' => $result_info_presenter->currentResultCounts(),
			'page_position' => $result_info_presenter->pagePosition(),
			'total_pages' => $search_data['max_num_pages'],
			'back_button' => $result_info_presenter->pagination('back', false),
			'next_button' => $result_info_presenter->pagination('next', false),
			'loading_spinner' => $result_info_presenter->loadingSpinner()
		];
	}

	/**
	* Build the Response Array
	* @return array
	*/
	public function build($results, $results_count, $total_count = 0, $request = null)
	{
		$this->request = ( $request ) ? $request : $_POST;
		$this->setData($results, $total_count);
		return [
			'status' => 'success', 
			'distance'=> $this->data['distance'],
			'latitude' => $this->data['latitude'],
			'longitude' => $this->data['longitude'],
			'unit' => $this->data['unit'],
			'formatted_address' => $this->data['formatted_address'],
			'address' => $this->data['address'],
			'result_count' => $results_count,
			'geolocation' => $this->data['geolocation'],
			'taxonomies' => $this->data['taxonomies'],
			'allow_empty_address' => $this->data['allow_empty_address'],
			'total_count' => $total_count,
			'page' => $this->data['page'],
			'per_page' => $this->data['per_page'],
			'total_pages' => $this->data['total_pages'],
			'results_header' => $this->data['results_header'],
			'current_counts' => $this->data['current_counts'],
			'page_position' => $this->data['page_position'],
			'back_button' => $this->data['back_button'],
			'next_button' => $this->data['next_button'],
			'loading_spinner' => $this->data['loading_spinner'],
			'results' => $results
		];
	}
}