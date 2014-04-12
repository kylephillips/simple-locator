<?php

class WPSLShortcode {

	public function __construct()
	{
		add_shortcode('wp_simple_locator', array($this, 'wp_simple_locator'));
	}

	public function wp_simple_locator()
	{
	if ( get_option('wpsl_measurement_unit') ){
		$unit = get_option('wpsl_measurement_unit');
	} else {
		$unit = 'miles';
	}
	$output = '
	<form id="wpslsearch" class="simple-locator-form">
		<div id="searcherror" class="alert alert-error" style="display:none;"></div>
		<div class="zip">
			<label for="zip">Zip Code</label>
			<input type="tel" name="zip" id="zip" />
		</div>
		<div class="distance">
			<label for="distance">Distance</label>
			<select name="distance" id="distance">
				<option value="5">5 ' . $unit . '</option>
				<option value="10">10 ' . $unit . '</option>
				<option value="20">20 ' . $unit . '</option>
				<option value="30">30 ' . $unit . '</option>
				<option value="40">40 ' . $unit . '</option>
				<option value="50">50 ' . $unit . '</option>
			</select>
		</div>
		<div class="submit">
			<input type="hidden" name="latitude" id="latitude" />
			<input type="hidden" name="longitude" id="longitude" />
			<input type="hidden" name="unit" value="' . $unit . '" id="unit" />
			<button type="submit" id="wpslsubmit">Search</button>
		</div>
		</form>
	<div id="locatormap"></div>
	<div id="locatorresults" class="loading"></div>';
	if ( get_option('wpsl_google_api_key') ){
		echo '<script src="http://maps.google.com/maps/api/js?key=' . get_option('wpsl_google_api_key') . '&sensor=false"></script>';
	} else {
		echo '<script src="http://maps.google.com/maps/api/js?sensor=false"></script>';
	}
	return $output;
	}

}

