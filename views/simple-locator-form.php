<?php
$output = "";
$mapheight = $this->options['mapheight'];

$search = ( isset($_POST['nonce']) ) ? new SimpleLocator\Listeners\LocationSearch : false;
$latitude = ( $search ) ? $search->data('latitude') : "";
$longitude = ( $search ) ? $search->data('longitude') : "";
$page = ( $search ) ? $search->data('page') : "0";
$search_term = ( $search ) ? $search->data('address') : "";

$output .= '
<div class="simple-locator-form">
<form action="" method="post">
	<input type="hidden" name="action" id="wpsl_action" value="locatorsearch" />
	<input type="hidden" name="limit" value="' . $this->options['perpage'] . '" />
	<input type="hidden" name="page" value="0" />
	<input type="hidden" name="latitude" class="latitude" value="' . $latitude . '" />
	<input type="hidden" name="longitude" class="longitude" value="' . $longitude . '" />
	<input type="hidden" name="unit" value="' . $this->unit . '" class="unit" />';
	if ( $search && $search->data('errors') ){
		$output .= '<div class="wpsl-error alert alert-error">' . $search->data('errors') . '</div>';
	}
	$output .= '
	<div class="wpsl-error alert alert-error" style="display:none;"></div>
	<div class="address-input form-field">
		<label for="zip">' . $this->options['addresslabel'] . '</label>
		<input type="text" name="address" class="address wpsl-search-form" placeholder="' . $this->options['placeholder'] . '" value="' . $search_term . '" />
	</div>
	<div class="distance form-field">
		<label for="distance">' . __('Distance', 'wpsimplelocator'). '</label>
		<select name="distance" class="distanceselect">' .
			$this->distanceOptions() . 
		'</select>
	</div>
	<div class="submit">
		<button type="submit" class="wpslsubmit">' . html_entity_decode($this->options['buttontext']) . '</button>
	</div>
	<div class="geo_button_cont"></div>
</form>';


$output .= '
</div><!-- .simple-locator-form -->';


if ( $search && !$search->data('errors') ) :
	$results = $search->results();
	
	if ( $results ) :

		$output .= '<h3>' . $search->resultCount() . ' Results within ' . $search->data('distance') . ' ' . $search->data('unit') . ' of ' . $search->data('formatted_address') . '</h3>';
		$output .= '<h5>' . __('Page', 'wpsimplelocator') . ' ' . $page . ' of ' . $search->data('max_num_pages') . '</h5>';

		if ( $this->options['mapcontainer'] === '.wpsl-map' ){
			$output .= ( isset($mapheight) && $mapheight !== "" ) 
				? '<div class="wpsl-map" style="height:' . $mapheight . 'px;"></div>' 
				: '<div class="wpsl-map"></div>';
		}

		$output .= '<ul class="wpsl-nonajax-results">';
		foreach($results as $result){
			$output .= '<li data-wpsl-result data-lat="' . $result['latitude'] . '" data-lng="' . $result['longitude'] . '" data-permalink="' . $result['permalink'] . '" data-title="' . $result['title'] . '">' . $result['output'] . '</li>';
		}
		$output .= '</ul>';
		$output .= $search->pagination();
		$output .= '<script>jQuery(document).ready(function(){new NonAjaxResults; });</script>';
	else : 
		$output .= '<h3>' . $this->options['noresultstext'] . '</h3>';
	endif;

endif;