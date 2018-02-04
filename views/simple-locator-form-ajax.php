<?php
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
<div class="simple-locator-form" data-simple-locator-form-container>
<form data-simple-locator-form';
if ( isset($this->options['allowemptyaddress']) && $this->options['allowemptyaddress'] == 'true' ) $output .= 'class="allow-empty" data-simple-locator-form-allow-empty';
if ( $this->options['mapcontrols'] != 'show' ) $output .= ' data-simple-locator-hide-map-controls="true"';
if ( $this->options['mapcontainer'] != '') $output .= ' data-simple-locator-map-container="' . $this->options['mapcontainer'] . '"';
if ( $this->options['resultscontainer'] != '') $output .= ' data-simple-locator-results-container="' . $this->options['resultscontainer'] . '"';
$output .= ' data-simple-locator-map-control-position="' . $this->options['mapcontrolsposition'] . '"';
$output .= '>
	<div class="wpsl-error alert alert-error" style="display:none;" data-simple-locator-form-error></div>
	<div class="address-input form-field">
		<label for="address">' . $this->options['addresslabel'] . '</label>
		<input type="text" id="address" data-simple-locator-input-address name="address" class="address wpsl-search-form" placeholder="' . $this->options['placeholder'] . '"';
		if ( $this->settings_repo->autocomplete() ) $output .= ' data-simple-locator-autocomplete';
		$output .= ' />
	</div>
	<div class="distance form-field">
		<label for="distance">' . __('Distance', 'wpsimplelocator'). '</label>
		<select name="distance" class="distanceselect" data-simple-locator-input-distance>' .
			$this->distanceOptions() . 
		'</select>
	</div>';
	if ( isset($this->taxonomies) ) :
		if ( $this->options['taxonomy_field_type'] == 'select' ) :
			foreach ( $this->taxonomies as $tax_name => $taxonomy ) :
				$output .= '<div class="wpsl-taxonomy-filter">
				<label class="taxonomy-label">' . $taxonomy['label'] . '</label>
				<select name="taxonomy[' . $tax_name . ']">
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
					$output .= '<label><input type="checkbox" name="taxonomy[' . $tax_name . ']" value="' . $term->term_id . '" />' .$term->name . '</label>';
				}
				$output .= '</div><!-- .taxonomy -->';
			endforeach;
		endif;
	endif;
	$output .= '<div class="submit">
		<input type="hidden" data-simple-locator-input-latitude name="latitude" class="latitude" />
		<input type="hidden" data-simple-locator-input-longitude name="longitude" class="longitude" />
		<input type="hidden" data-simple-locator-input-formatted-location name="formatted_location" />
		<input type="hidden" data-simple-locator-input-geocode name="geocode" />
		<input type="hidden" data-simple-locator-input-unit name="unit" value="' . $this->unit_raw . '" class="unit" />
		<button type="submit" data-simple-locator-submit class="wpslsubmit">' . html_entity_decode($this->options['buttontext']) . '</button>
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
<div data-simple-locator-results class="wpsl-results loading"></div>
</div><!-- .simple-locator-form -->';

if ( isset($widget_instance) ) $output .= '</div><!-- .simple-locator-widget -->';