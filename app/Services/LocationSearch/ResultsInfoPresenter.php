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
		$address = ( isset($this->request['address']) ) ? $this->request['address'] : '';
		$distance = ( isset($this->request['distance']) ) ? $this->request['distance'] : '';
		$unit = ( isset($this->request['unit']) ) ? $this->request['unit'] : get_option('wpsl_measurement_unit');
		$geolocation = ( isset($this->request['geolocation']) && $this->request['geolocation'] == 'true' ) ? true : false;

		$location = ( $geolocation ) ? apply_filters('simple_locator_your_location_text', __('your location', 'simple-locator')) : $address;
		$total_results = ( $total_results == 1 ) 
			? $total_results . ' ' . apply_filters('simple_locator_non_ajax_location_text', __('location', 'simple-locator') )
			: $total_results . ' ' . apply_filters('simple_locator_non_ajax_locations_text', __('locations', 'simple-locator'));

		$output = ( $address == '' && !$geolocation )
			? '<h3 class="wpsl-results-header">' . $total_results . '</h3>'
			: '<h3 class="wpsl-results-header">' . $total_results . ' ' . __('found within', 'simple-locator') . ' ' . $distance . ' ' . $unit . ' ' . __('of', 'simple-locator') . ' ' . $location . '</h3>';

		return apply_filters('simple_locator_non_ajax_results_header', $output, $this->request, $this->search_data);
	}

	/**
	* Pagination Fields
	*/
	public function pagination($direction = 'next', $include_hidden_fields = true, $autoload = false)
	{
		if ( !isset($this->request['per_page']) || $this->request['per_page'] == 0 ) return null;
		if ( $direction == 'back' && $this->request['page'] == 1 ) return null;
		if ( $direction == 'next' && ( $this->request['page'] == $this->search_data['max_num_pages']) ) return null;

		$button_text = ( $direction == 'next' ) ? __('Next', 'simple-locator') : __('Back', 'simple-locator');
		$button_class = ( $direction == 'next' ) ? 'button-next' : 'button-previous';
		$button = '<button type="submit" data-simple-locator-pagination="' . $direction . '" class="button wpsl-pagination-button ' . $button_class . '">' . $button_text . '</button>';

		if ( !$include_hidden_fields && !$autoload ) return $button;

		$output = '<form method="' . $this->request['formmethod'] . '" action="' . get_the_permalink($this->request['resultspage']) . '" class="simple-locator-pagination-form';
		if ( $this->request['allow_empty_address'] == 'true' ) $output .= ' allow-empty';
		$output .= '">';
		
		$page = ( $direction == 'next' ) ? $this->request['page'] + 1 : $this->request['page'] - 1;
		if ( $autoload ) $page = 1;
		
		$output .= '<input type="hidden" name="page_num" value="' . $page . '">';
		$output .= $this->hiddenFields();

		$output .= ( $direction && !$autoload == 'next' )
			? '<input type="hidden" name="back" value="true">'
			: '<input type="hidden" name="next" value="true">';
		$output .= $button;
		$output .= '</form>';
		return apply_filters('simple_locator_directional_pagination', $output, $direction, $this->request, $this->search_data);
	}

	/**
	* Get the current result counts (Showing results X - X of X)
	*/
	public function currentResultCounts()
	{
		if ( !isset($this->request['per_page']) || $this->request['per_page'] == 0 || !$this->search_data['results'] ) return;
		$current_start = (intval($this->request['page']) - 1) * intval($this->request['per_page']) + 1;
		$current_end = $current_start + count($this->search_data['results']) - 1;
		if ( $current_start != $current_end ) $current_start .= '&ndash;' . $current_end;
		$output = '<p class="wpsl-results-current-count"><em>' . __('Showing', 'simple-locator') . ' ' . $current_start . ' ' . __('of', 'simple-locator') . ' ' . $this->search_data['total_results'] . '</em></p>';
		return apply_filters('simple_locator_non_ajax_current_count', $output, $current_start, $current_end, $this->search_data['total_results']);
	}

	/**
	* Page Position (Page X of X)
	*/
	public function pagePosition()
	{
		if ( !isset($this->request['per_page']) || $this->request['per_page'] == 0 ) return;
		if ( $this->search_data['max_num_pages'] < 2 ) return;
		$output = '<div class="wpsl-form-page-selection">';
		$output .= '<p>' . __('Page', 'simple-locator') . ' ' . $this->request['page'] . ' ' . __('of', 'simple-locator') . ' ' . $this->search_data['max_num_pages'] . '</p>';
		$output .= '</div>';
		return apply_filters('simple_locator_non_ajax_page_position', $output, $this->request, $this->search_data);
	}

	/**
	* Go to Page Form
	*/
	public function goToPage($include_hidden_fields = true)
	{
		if ( $this->search_data['max_num_pages'] < 2 ) return;
		$method = ( isset($this->request['formmethod']) && $this->request['formmethod'] == 'get' ) ? 'get' : 'post';
		$resultspage = ( isset($this->request['resultspage']) ) ? get_the_permalink($this->request['resultspage']) : '';
		$allow_empty = ( isset($this->request['allow_empty_address']) && $this->request['allow_empty_address'] == 'true' ) ? true : false;

		$output = '<form method="' . $method . '" action="' . $resultspage . '" class="wpsl-jump-to-page-form';
		if ( $allow_empty  ) $output .= ' allow-empty';
		$output .= '" data-simple-locator-page-jump-form>';
		$output .= '<p class="current-page">' . __('Page', 'simple-locator') . '</p>';
		$output .= '<input type="tel" name="page_num" value="' . $this->request['page'] . '" class="wpsl-input input">';
		$output .= '<p class="total-pages">' . __('of', 'simple-locator') . ' ' . $this->search_data['max_num_pages'] . '</p>';
		$output .= '<button type="submit" class="button wpsl-pagination-button" style="display:none;">' . __('Go', 'simple-locator') . '</button>';
		if ( $include_hidden_fields ) $output .= $this->hiddenFields();
		$output .= '</form>';
		return apply_filters('simple_locator_go_to_page_form', $output, $this->request, $this->search_data);
	}

	/**
	* No Results Found
	*/
	public function noResultsFoundError()
	{
		$error = '<div class="alert alert-error wpsl-error">' . __('No locations were found within', 'simple-locator') . ' ' . $this->request['distance'] . ' ' . $this->request['unit'] . ' ' . __('of','simple-locator') . ' ' . $this->request['formatted_address'] . '. ' . $this->newSearchLink() . '</div>';
		return apply_filters('simple_locator_no_results_error', $error, $this->request, $this->search_data);
	}

	/**
	* Loading Spinner
	*/
	public function loadingSpinner()
	{
		return '<div class="wpsl-icon-spinner"><div class="wpsl-icon-spinner-image"><img src="' . apply_filters('simple_locator_results_loading_spinner', \SimpleLocator\Helpers::plugin_url() . '/assets/images/loading-spinner.svg') . '" class="wpsl-spinner-image" /></div></div>';
	}

	/**
	* New Search link
	*/
	public function newSearchLink()
	{
		if ( !isset($this->request['search_page']) ) return;
		$link = '<a href="' . get_the_permalink($this->request['search_page']) . '" class="wpsl-new-search-link">' . __('New Search', 'simple-locator') . '</a>';
		return apply_filters('simple_locator_new_search_link', $link, $this->request, $this->search_data);
	}

	/**
	* Hidden fields
	*/
	private function hiddenFields()
	{
		$output = '<input type="hidden" name="per_page" value="' . $this->request['per_page'] . '">';
		if ( isset($this->request['address']) ) $output .= 
			'<input type="hidden" name="address" value="' . $this->request['address'] . '">';
		if ( isset($this->request['formatted_address']) ) $output .= 
			'<input type="hidden" name="formatted_address" value="' . $this->request['formatted_address'] . '" />';
		if ( isset($this->request['distance']) ) $output .= 
			'<input type="hidden" name="wpsl_distance" value="' . $this->request['distance'] . '" />';
		if ( isset($this->request['latitude']) ) $output .= 
			'<input type="hidden" name="latitude" value="' . $this->request['latitude'] . '" />';
		if ( isset($this->request['longitude']) ) $output .= 
			'<input type="hidden" name="longitude" value="' . $this->request['longitude'] . '" />';
		$output .= '
			<input type="hidden" name="unit" value="' . $this->request['unit'] . '" />
			<input type="hidden" name="geolocation" value="' . $this->request['geolocation'] . '">
			<input type="hidden" name="search_page" value="' . $this->request['search_page'] . '" />
			<input type="hidden" name="results_page" value="' . $this->request['resultspage'] . '" />
			<input type="hidden" name="allow_empty_address" value="' . $this->request['allow_empty_address'] . '" />
			<input type="hidden" name="method" value="' . $this->request['formmethod'] . '" />
			<input type="hidden" name="mapheight" value="' . $this->request['mapheight'] . '" />
			<input type="hidden" name="simple_locator_results" value="true" />';
		if ( $this->request['formmethod'] == 'get' ) $output .= '<input type="hidden" name="page_id" value="' . $this->request['resultspage'] . '">';
		return $output;
	}
}