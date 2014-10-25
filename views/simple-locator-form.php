<?php
$output = '
<form id="wpslsearch" class="simple-locator-form">
	<div id="searcherror" class="alert alert-error" style="display:none;"></div>
	<div class="zip">
		<label for="zip">' . __('Zip/Postal Code', 'wpsimplelocator') . '</label>
		<input type="tel" name="zip" id="zip" />
	</div>
	<div class="distance">
		<label for="distance">' . __('Distance', 'wpsimplelocator'). '</label>
		<select name="distance" id="distance">' .
			$this->distanceOptions() . 
		'</select>
	</div>
	<div class="submit">
		<input type="hidden" name="latitude" id="latitude" />
		<input type="hidden" name="longitude" id="longitude" />
		<input type="hidden" name="unit" value="' . $this->unit . '" id="unit" />
		<button type="submit" id="wpslsubmit">Search</button>
	</div>
	</form>
<div id="locatormap"></div>
<div id="locatorresults" class="loading"></div>';