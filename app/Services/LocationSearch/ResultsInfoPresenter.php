<?php
namespace SimpleLocator\Services\LocationSearch;

/**
* Presenter Methods for Pagination, Results Found, Etc…
*/
class ResultsInfoPresenter
{
	/**
	* The Request Array
	*/
	private $request;

	/**
	* The Search Data Array
	*/
	private $search_data;

	public function __construct($request, $search_data)
	{
		$this->request = $request;
		$this->search_data = $search_data;
	}

	/**
	* Result Header (X locations found within X Miles of X)
	*/
	public function resultsHeader()
	{
		$total_results = $this->search_data['total_results'];
		$total_results = ( $total_results == 1 ) 
			? $total_results . ' ' . apply_filters('simple_locator_non_ajax_location_text', __('location', 'simple-locator') )
			: $total_results . ' ' . apply_filters('simple_locator_non_ajax_locations_text', __('locations', 'simple-locator'));
		$output = '<h3 class="wpsl-results-header">' . $total_results . ' ' . __('found within', 'simple-locator') . ' ' . $this->request['distance'] . ' ' . $this->request['unit'] . ' ' . __('of', 'simple-locator') . ' ' . $this->request['address'] . '</h3>';
		return apply_filters('simple_locator_non_ajax_results_header', $output, $this->request, $this->search_data);
	}

	/**
	* Pagination Fields
	*/
	public function pagination($direction = 'next', $include_hidden_fields = true)
	{
		if ( $this->request['per_page'] == 0 ) return null;
		if ( $direction == 'back' && $this->request['page'] == 0 ) return null;
		if ( $direction == 'next' && ( ($this->request['page'] + 1 ) == $this->search_data['max_num_pages']) ) return null;

		$button_text = ( $direction == 'next' ) ? __('Next', 'simple-locator') : __('Back', 'simple-locator');
		$button = '<input type="submit" data-simple-locator-pagination="' . $direction . '" class="button simple-locator-submit-button" value="' . $button_text . '">';

		if ( !$include_hidden_fields ) return $button;

		$output = '<form method="' . $this->request['formmethod'] . '" action="' . get_the_permalink($this->request['resultspage']) . '" class="simple-locator-pagination-form';
		if ( $this->request['allow_empty_address'] == 'true' ) $output .= ' allow-empty';
		$output .= '">';
		$page = ( $direction == 'next' ) ? $this->request['page'] + 1 : $this->request['page'] - 1;
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
		$output .= $button;
		$output .= '</form>';
		return $output;
	}

	/**
	* Get the current result counts (Showing results X - X of X)
	*/
	public function currentResultCounts()
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
	* Page Position (Page X of X)
	*/
	public function pagePosition()
	{
		if ( $this->request['per_page'] == 0 ) return;
		$output = '<div class="simple-locator-form-page-selection">';
		$output .= '<p>' . __('Page', 'simple-locator') . ' ' . ($this->request['page'] + 1) . ' ' . __('of', 'simple-locator') . ' ' . $this->search_data['max_num_pages'] . '</p>';
		$output .= '</div>';
		return apply_filters('simple_locator_non_ajax_page_position', $output, $this->request, $this->search_data);
	}

	/**
	* No Results Found
	*/
	public function noResultsFoundError()
	{
		$error = '<div class="alert alert-error wpsl-error">' . __('No locations were found within', 'simple-locator') . ' ' . $this->request['distance'] . ' ' . $this->request['unit'] . ' ' . __('of','simple-locator') . ' ' . $this->request['formatted_address'] . '</div>';
		return apply_filters('simple_locator_no_results_error', $error, $this->request, $this->search_data);
	}
}