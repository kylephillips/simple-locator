<?php
/**
* @see SimpleLocator\Listeners\LocationSearch
* @todo Error Handling, No results, Page Jump, AJAX Pagination, New Search Link, Dont store paginated searches, taxonomies in non-ajax, dont refresh map in ajax searches, add search center icon and setting to customize
*/
$output = '<div data-simple-locator-results-wrapper class="wpsl-results non-ajax" style="display:block;">';

// Results
if ( count($this->search_data['results']) > 0 ) :
	$output .= $this->results_info->resultsHeader();
	$output .= $this->results_info->currentResultCounts();

	$output .= '<div data-simple-locator-map-non-ajax class="wpsl-map loading"';
	if ( isset($this->request['mapheight']) && $this->request['mapheight'] !== "" )  $output .= 'style="height:' . $this->request['mapheight'] . 'px;"';
	$output .= ' data-latitude="' . $this->request['latitude'] . '"';
	$output .= ' data-longitude="' . $this->request['longitude'] . '"';
	$output .= '></div><!-- .wpsl-map -->';

	$results_output = '<div class="wpsl-results-wrapper">';
	foreach($this->search_data['results'] as $result) :
		$results_output .= $result['output'];
	endforeach;
	$results_output .= '</div>';
	$output .= apply_filters('simple_locator_non_ajax_results_output', $results_output, $this->request);
endif;

$output .= '<div class="simple-locator-non-ajax-pagination">';
$output .= $this->results_info->pagination('back');
$output .= $this->results_info->pagination('next');
$output .= $this->results_info->pagePosition();
$output .= '</div>';

$output .= '</div><!-- .wpsl-results -->';