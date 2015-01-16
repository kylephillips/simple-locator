<?php
$output = "";

// Is this a widget form or a shortcode form
if ( isset($widget_instance) ) {
	$output .= '<div class="simple-locator-widget">';
	$mapheight = ( isset($instance['map_height']) ) ? $instance['map_height'] : 200;
	$this->options['addresslabel'] = __('Zip/Postal Code', 'wpsimplelocator');
	$this->options['mapcontainer'] = '.wpsl-map';
	$output .= '<span id="widget"></span>';
} else {
	$mapheight = $this->options['mapheight'];
}


$output .= '
<div class="simple-locator-form">
<form>
	<div class="wpsl-error alert alert-error" style="display:none;"></div>
	<div class="address-input form-field">
		<label for="zip">' . $this->options['addresslabel'] . '</label>
		<input type="tel" name="address" class="address" />
	</div>
	<div class="distance form-field">
		<label for="distance">' . __('Distance', 'wpsimplelocator'). '</label>
		<select name="distance" class="distanceselect">' .
			$this->distanceOptions() . 
		'</select>
	</div>
	<div class="submit">
		<input type="hidden" name="latitude" class="latitude" />
		<input type="hidden" name="longitude" class="longitude" />
		<input type="hidden" name="unit" value="' . $this->unit . '" class="unit" />
		<button type="submit" class="wpslsubmit">' . html_entity_decode($this->options['buttontext']) . '</button>';
		if ( $this->options['showgeobutton'] == "true" ){
			$output .= 'show geo button';
		}
		$output .= '
	</div>
	</form>';
if ( $this->options['mapcontainer'] === '.wpsl-map' ){
	$output .= ( isset($mapheight) && $mapheight !== "" ) 
		? '<div class="wpsl-map" style="height:' . $mapheight . 'px;"></div>' 
		: '<div class="wpsl-map"></div>';
}

$output .= '
<div class="wpsl-results" class="loading"></div>
</div><!-- .simple-locator-form -->';
if ( isset($widget_instance) ) $output .= '</div><!-- .simple-locator-widget -->';