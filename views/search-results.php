<?php
$total_results = $this->search_data['total_results'];
$total_results = ( $total_results == 1 ) 
	? $total_results . ' ' . apply_filters('simple_locator_non_ajax_location_text', __('location', 'simple-locator') )
	: $total_results . ' ' . apply_filters('simple_locator_non_ajax_locations_text', __('locations', 'simple-locator'));

$output = '<div class="wpsl-results non-ajax" style="display:block;">';

$results_header = '<h3 class="wpsl-results-header">' . $total_results . ' ' . __('found within', 'simple-locator') . ' ' . $this->request['distance'] . ' ' . $this->request['unit'] . ' ' . __('of', 'simple-locator') . ' ' . $this->request['address'] . '</h3>';
$output .= apply_filters('simple_locator_non_ajax_results_header', $results_header, $this->request);

if ( count($this->search_data['results']) > 0 ) :
	$results_output = '<div class="wpsl-results-wrapper">';
	foreach($this->search_data['results'] as $result) :
		$results_output .= $result['output'];
	endforeach;
	$results_output .= '</div>';
	$output .= apply_filters('simple_locator_non_ajax_results_ouput', $results_output, $this->request);
endif;

// TODO: Pagination, hidden form and fields

$output .= '</div>';