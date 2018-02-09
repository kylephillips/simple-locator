<?php
global $post;
$ajax = ( $this->options['ajax'] == 'false' ) ? false : true;
$output = "";

// Is this a widget form or a shortcode form
if ( isset($widget_instance) ) {
	$output .= '<div class="simple-locator-widget">';
	$mapheight = ( isset($instance['map_height']) ) ? $instance['map_height'] : 200;
	$this->options['placeholder'] = ( isset($instance['placeholder']) ) ? $instance['placeholder'] : '';
	$output .= '<span id="widget"></span>';
} else {
	$mapheight = $this->options['mapheight'];
}

$output .= '
<div class="simple-locator-form" data-simple-locator-form-container data-simple-locator-results-wrapper>
<form method="' . $this->options['formmethod'] . '" action="' . get_the_permalink($this->options['resultspage']) . '" data-simple-locator-form';
if ( isset($this->options['allowemptyaddress']) && $this->options['allowemptyaddress'] == 'true' ) $output .= ' class="allow-empty" data-simple-locator-form-allow-empty';
if ( $this->options['mapcontrols'] != 'show' ) $output .= ' data-simple-locator-hide-map-controls="true"';
if ( $this->options['mapcontainer'] != '') $output .= ' data-simple-locator-map-container="' . $this->options['mapcontainer'] . '"';
if ( $this->options['resultscontainer'] != '') $output .= ' data-simple-locator-results-container="' . $this->options['resultscontainer'] . '"';
if ( $ajax ) $output .= ' data-simple-locator-ajax-form="true"';
$output .= ' data-simple-locator-map-control-position="' . $this->options['mapcontrolsposition'] . '"';
$output .= '>
	<div class="wpsl-error alert alert-error" style="display:none;" data-simple-locator-form-error></div>
	<div class="address-input form-field">
		<label for="wpsl_address">' . $this->options['addresslabel'] . '</label>
		<input type="text" id="wpsl_address" data-simple-locator-input-address name="address" class="address wpsl-search-form" placeholder="' . $this->options['placeholder'] . '"';
		if ( $this->settings_repo->autocomplete() ) $output .= ' data-simple-locator-autocomplete';
		$output .= ' />
	</div>
	<div class="distance form-field">
		<label for="wpsl_distance">' . __('Distance', 'wpsimplelocator'). '</label>
		<select name="wpsl_distance" class="distanceselect" data-simple-locator-input-distance>' .
			$this->distanceOptions() . 
		'</select>
	</div>';
	if ( isset($this->taxonomies) ) :
		$output .= '<div class="wpsl-taxonomy-filters">';
		if ( $this->options['taxonomy_field_type'] == 'select' ) :
			foreach ( $this->taxonomies as $tax_name => $taxonomy ) :
				$output .= '<div class="wpsl-taxonomy-filter">
				<label for="wpsl_taxonomy_' . $tax_name . '" class="taxonomy-label">' . $taxonomy['label'] . '</label>
				<select id="wpsl_taxonomy_' . $tax_name . '" name="taxfilter[' . $tax_name . ']" data-simple-locator-taxonomy-select="' . $tax_name . '">
					<option value="">--</option>';
					foreach ( $taxonomy['terms'] as $term ){
						$output .= '<option value="' . $term->term_id . '" />' . $term->name . '</option>';
					}
				$output .= '</select>
				</div><!-- .taxonomy -->';
			endforeach;
		else :
			foreach ( $this->taxonomies as $tax_name => $taxonomy ) :
				$output .= '<div class="wpsl-taxonomy-filter checkboxes">
				<label class="taxonomy-label">' . $taxonomy['label'] . '</label>';
				foreach ( $taxonomy['terms'] as $term ){
					$output .= '<label for="wpsl_taxonomy_' . $tax_name . '"><input type="checkbox" id="wpsl_taxonomy_' . $tax_name . '" name="taxfilter[' . $tax_name . '][]" value="' . $term->term_id . '" data-simple-locator-taxonomy-checkbox="' . $tax_name . '" />' .$term->name . '</label>';
				}
				$output .= '</div><!-- .taxonomy -->';
			endforeach;
		endif;
		$output .= '</div><!-- .wpsl-taxonomy-filters -->';
	endif;
	$output .= '<div class="submit">
		<input type="hidden" data-simple-locator-input-latitude name="latitude" class="latitude" />
		<input type="hidden" data-simple-locator-input-longitude name="longitude" class="longitude" />
		<input type="hidden" data-simple-locator-input-formatted-location name="formatted_location" />
		<input type="hidden" name="page_num" value="0" />
		<input type="hidden" name="search_page" value="' . $post->ID . '" />
		<input type="hidden" name="results_page" value="' . $this->options['resultspage'] . '" />
		<input type="hidden" data-simple-locator-input-limit name="per_page" value="' . $this->options['perpage'] . '" />
		<input type="hidden" name="simple_locator_results" value="true" />
		<input type="hidden" name="method" value="' . $this->options['formmethod'] . '" />
		<input type="hidden" name="mapheight" value="' . $this->options['mapheight'] . '" />
		<input type="hidden" data-simple-locator-input-geocode name="geocode" />
		<input type="hidden" data-simple-locator-input-unit name="unit" value="' . $this->unit_raw . '" class="unit" />
		<button type="submit" data-simple-locator-submit class="wpslsubmit">' . html_entity_decode($this->options['buttontext']) . '</button>
		<div class="wpsl-icon-spinner"><div class="wpsl-icon-spinner-image"><img src="' . apply_filters('simple_locator_results_loading_spinner', \SimpleLocator\Helpers::plugin_url() . '/assets/images/loading-spinner.svg') . '" class="wpsl-spinner-image" /></div></div>
	</div>
	<div class="geo_button_cont"></div>
	</form>';
if ( $this->options['mapcontainer'] === '' ){
	$output .= '<div data-simple-locator-map class="wpsl-map loading"';
	if ( isset($mapheight) && $mapheight !== "" )  $output .= 'style="height:' . $mapheight . 'px;"';
	if ( $this->settings_repo->showDefaultMap() ) $output .= ' data-simple-locator-default-enabled';
	$output .= '></div><!-- .wpsl-map -->';
}

$output .= '
<div data-simple-locator-results class="wpsl-results"></div>
</div><!-- .simple-locator-form -->';

if ( isset($widget_instance) ) $output .= '</div><!-- .simple-locator-widget -->';