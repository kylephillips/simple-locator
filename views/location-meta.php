<div class="wpsl-meta">
	<p class="full">
		<label for="wpsl_address">Street Address</label>
		<input type="text" name="wpsl_address" id="wpsl_address" value="<?php echo $address; ?>" />
	</p>
	<p class="city">
		<label for="wpsl_city">City</label>
		<input type="text" name="wpsl_city" id="wpsl_city" value="<?php echo $city; ?>" />
	</p>
	<p class="state">
		<label for="wpsl_state">State</label>
		<input type="text" name="wpsl_state" id="wpsl_state" value="<?php echo $state; ?>" />
	</p>
	<p class="zip">
		<label for="wpsl_zip">Zip</label>
		<input type="text" name="wpsl_zip" id="wpsl_zip" value="<?php echo $zip; ?>" />
	</p>
	<div id="wpslmap"></div>
	<hr />
	<div class="latlng">
		<span>Geocode values will update on save. Fields are for display purpose only.</span>
		<p>
			<label for="wpsl_latitude">Latitude</label>
			<input type="text" name="wpsl_latitude" id="wpsl_latitude" value="<?php echo $latitude; ?>" />
		</p>
		<p class="lat">
			<label for="wpsl_longitude">Longitude</label>
			<input type="text" name="wpsl_longitude" id="wpsl_longitude" value="<?php echo $longitude; ?>"  />
		</p>
	</div>
	<hr />
	<p class="half">
		<label for="wpsl_phone">Phone Number</label>
		<input type="text" name="wpsl_phone" id="wpsl_phone" value="<?php echo $phone; ?>" />
	</p>
	<p class="half right">
		<label for="wpsl_website">Website</label>
		<input type="text" name="wpsl_website" id="wpsl_website" value="<?php echo $website; ?>" />
	</p>
	<hr />
	<p class="full">
		<label for="wpsl_additionalinfo">Additional Info</label>
		<textarea name="wpsl_additionalinfo" id="wpsl_additionalinfo"><?php echo $additionalinfo; ?></textarea>
	</p>
</div>
<script>
	var form = jQuery("form[name='post']");
	jQuery(function($){
		$(form).find("#publish").on('click', function(e){
			e.preventDefault();
			var streetaddress = $('#wpsl_address').val();
			var city = $('#wpsl_city').val();
			var state = $('#wpsl_state').val();
			var zip = $('#wpsl_zip').val();
			var address = streetaddress + ' ' + city + ' ' + state + ' ' + zip;
			geocodeAddress(address);
		});
		$(document).ready(function(){
			checkMapStatus();
		});
	});
</script>