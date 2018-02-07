<?php
/**
*  @todo Error Handling, No results, Page Jump, AJAX Pagination, New Search Link, Dont store paginated searches
*/
$output .= '<div data-simple-locator-results-wrapper class="wpsl-results non-ajax" style="display:block;">';

// Results
if ( count($this->search_data['results']) > 0 ) :
	$output .= $this->resultsHeader();
	$output .= $this->currentResultCounts();

	$output .= '<div data-simple-locator-map-non-ajax class="wpsl-map loading"';
	if ( isset($this->request['mapheight']) && $this->request['mapheight'] !== "" )  $output .= 'style="height:' . $this->request['mapheight'] . 'px;"';
	$output .= '></div><!-- .wpsl-map -->';

	$results_output = '<div class="wpsl-results-wrapper">';
	foreach($this->search_data['results'] as $result) :
		$results_output .= $result['output'];
	endforeach;
	$results_output .= '</div>';
	$output .= apply_filters('simple_locator_non_ajax_results_output', $results_output, $this->request);
endif;

$output .= $this->paginationForm('back');
$output .= $this->paginationForm('next');
$output .= $this->pagePosition();

$output .= '</div><!-- .wpsl-results -->';