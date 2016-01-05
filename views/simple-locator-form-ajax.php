<?php
$output = "";

// Is this a widget form or a shortcode form
if ( isset($widget_instance) ) {
	$output .= '<div class="simple-locator-widget">';
	$mapheight = ( isset($instance['map_height']) ) ? $instance['map_height'] : 200;
	$this->options['addresslabel'] = __('Zip/Postal Code', 'wpsimplelocator');
	$this->options['mapcontainer'] = '.wpsl-map';
	$this->options['placeholder'] = ( isset($instance['placeholder']) ) ? $instance['placeholder'] : '';
	$output .= '<span id="widget"></span>';
} else {
	$mapheight = $this->options['mapheight'];
}

$output .= '
<div class="simple-locator-form">
<form';
if ( isset($this->options['allowemptyaddress']) && $this->options['allowemptyaddress'] == 'true' ) $output .= 'class="allow-empty"';
$output .= '>
	<div class="wpsl-error alert alert-error" style="display:none;"></div>
	<div class="address-input form-field">
		<label for="zip">' . $this->options['addresslabel'] . '</label>
		<input type="text" name="address" class="address wpsl-search-form" placeholder="' . $this->options['placeholder'] . '" />
	</div>
	<div class="distance form-field">
		<label for="distance">' . __('Distance', 'wpsimplelocator'). '</label>
		<select name="distance" class="distanceselect">' .
			$this->distanceOptions() . 
		'</select>
	</div>';
	if ( isset($this->taxonomies) ) :
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
	endif;
	$output .= '<div class="submit">
		<input type="hidden" name="latitude" class="latitude" />
		<input type="hidden" name="longitude" class="longitude" />
		<input type="hidden" name="unit" value="' . $this->unit_raw . '" class="unit" />
		<button type="submit" class="wpslsubmit">' . html_entity_decode($this->options['buttontext']) . '</button>
	</div>
	<div class="geo_button_cont"></div>
	</form>';
if ( $this->options['mapcontainer'] === '.wpsl-map' ){
	$output .= ( isset($mapheight) && $mapheight !== "" ) 
		? '<div class="wpsl-map" style="height:' . $mapheight . 'px;"></div>' 
		: '<div class="wpsl-map"></div>';
}

$output .= '
<div class="wpsl-results loading"></div>
</div><!-- .simple-locator-form -->';

if ( isset($widget_instance) ) $output .= '</div><!-- .simple-locator-widget -->';