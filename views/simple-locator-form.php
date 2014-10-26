<?php
$output = "";
if ( isset($widget_instance) ) {
	$output .= '<div class="simple-locator-widget">';
	$mapheight = $instance['map_height'];
} else {
	$mapheight = $this->options['mapheight'];
}
$output .= '
<div class="simple-locator-form">
<form>
	<div class="wpsl-error alert alert-error" style="display:none;"></div>
	<div class="zip form-field">
		<label for="zip">' . __('Zip/Postal Code', 'wpsimplelocator') . '</label>
		<input type="tel" name="zip" class="zipcode" />
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
		<button type="submit" class="wpslsubmit">' . $this->options['buttontext'] . '</button>
	</div>
	</form>';

$output .= ( isset($mapheight) && $mapheight !== "" ) ? '<div class="wpsl-map" style="height:' . $mapheight . 'px;"></div>' : '<div class="wpsl-map"></div>';

$output .= '
<div class="wpsl-results" class="loading"></div>
</div><!-- .simple-locator-form -->';
if ( isset($widget_instance) ) $output .= '</div><!-- .simple-locator-widget -->';