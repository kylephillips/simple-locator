<?php 
settings_fields( 'wpsimplelocator-general' ); 
$location_btn = get_option('wpsl_geo_button');
if ( !isset($location_btn['enabled']) || $location_btn['enabled'] == "" ) $location_btn['enabled'] = false;
if ( !isset($location_btn['text']) || $location_btn['text'] == "" ) $location_btn['text'] = __('Use my location', 'wpsimplelocator');
?>

</table>

<div class="wpsl-settings-general-head">
	<h3><?php _e('Google Maps API Key', 'wpsimplelocator'); ?></h3>
	<p><?php _e('While not required, it\'s a good idea to sign up for a Google Maps API key. If your website receives a spike in traffic, this will allow you to monitor the usage of the API and possibly prevent the cutoff of access.', 'wpsimplelocator'); ?> <a href="https://developers.google.com/maps/documentation/javascript/tutorial#api_key" target="_blank"><?php _e('How to obtain an API key', 'wpsimplelocator'); ?></a></p>
	<p>
		<strong><label for="wpsl_google_api_key"><?php _e('Google Maps Javascript API V3 Key (public)', 'wpsimplelocator');?></label></strong><br>
		<input type="text" name="wpsl_google_api_key" id="wpsl_google_api_key" value="<?php echo get_option('wpsl_google_api_key'); ?>" />
	</p>
	<p><?php _e('Use of the built-in importer requires a server API key with the Geocoding API enabled. This should not be a public key. It should be a server key, with your server\'s IP address whitelisted.', 'wpsimplelocator'); ?></p>
	<p>
		<strong><label for="wpsl_google_geocode_api_key"><?php _e('Google Maps Geocode API Key (server)', 'wpsimplelocator');?></label></strong><br>
		<input type="text" name="wpsl_google_geocode_api_key" id="wpsl_google_geocode_api_key" value="<?php echo get_option('wpsl_google_geocode_api_key'); ?>" />
	</p>
</div>

<table class="form-table">
<tr valign="top">
	<th scope="row"><?php _e('Measurement Unit', 'wpsimplelocator'); ?></th>
	<td>
		<select name="wpsl_measurement_unit">
			<option value="miles" <?php if ( $this->unit == "miles") echo ' selected'; ?>><?php _e('Miles', 'wpsimplelocator'); ?></option>
			<option value="kilometers" <?php if ( $this->unit == "kilometers") echo ' selected'; ?>><?php _e('Kilometers', 'wpsimplelocator'); ?></option>
		</select>
	</td>
</tr>
<tr valign="top">
	<th scope="row"><?php _e('Custom Map Pin', 'wpsimplelocator'); ?></th>
	<td>
		<div id="map-pin-image-cont" style="float:left;">
			<?php 
			if ( get_option('wpsl_map_pin') ){
				echo '<img src="' . get_option('wpsl_map_pin') . '" id="map-pin-image" />';
			}
			?>
		</div>
		<?php 
		if ( get_option('wpsl_map_pin') ){
			echo '<input id="remove_map_pin" type="button" value="' . __('Remove', 'wpsimplelocator') . '" class="button action" style="margin-right:5px;margin-left:10px;" />';
		} else {
			echo '<input id="upload_image_button" type="button" value="Upload" class="button action" />';
		} ?>
		<input id="wpsl_map_pin" type="text" size="36" name="wpsl_map_pin" value="<?php echo get_option('wpsl_map_pin'); ?>" style="display:none;" />
	</td>
</tr>
<tr valign="top">
	<th scope="row"><?php _e('Output Simple Locator CSS', 'wpsimplelocator'); ?></th>
	<td>
		<label>
			<input type="radio" value="true" name="wpsl_output_css" <?php if ( get_option('wpsl_output_css') == 'true') echo 'checked'; ?> />
			<?php _e('Yes', 'wpsimplelocator'); ?>
		</label><br />
		<label>
			<input type="radio" value="false" name="wpsl_output_css" <?php if ( get_option('wpsl_output_css') !== 'true') echo 'checked'; ?> />
			<?php _e('No', 'wpsimplelocator'); ?>
		</label>
	</td>
</tr>
<tr valign="top">
	<th scope="row"><?php _e('Include Google Maps API', 'wpsimplelocator'); ?>*</th>
	<td>
		<label>
			<input type="radio" value="true" name="wpsl_gmaps_api" <?php if ( get_option('wpsl_gmaps_api') == 'true') echo 'checked'; ?> />
			<?php _e('Yes', 'wpsimplelocator'); ?>
		</label><br />
		<label>
			<input type="radio" value="false" name="wpsl_gmaps_api" <?php if ( get_option('wpsl_gmaps_api') !== 'true') echo 'checked'; ?> />
			<?php _e('No', 'wpsimplelocator'); ?>
		</label>
		<p style="font-style:oblique;font-size:13px;"><?php _e('The Google Maps API is required for Simple Locator to function. If another plugin in use includes the Google Maps API, disable it here to avoid it being included multiple times on the front end of your site.', 'wpsimplelocator'); ?></p>
	</td>
</tr>
<tr valign="top">
	<th scope="row"><?php _e('Enable Autocomplete in Search', 'wpsimplelocator'); ?></th>
	<td>
		<label>
			<input type="checkbox" value="true" name="wpsl_enable_autocomplete" <?php if ( get_option('wpsl_enable_autocomplete') == 'true') echo 'checked'; ?> />
			<?php _e('Enable', 'wpsimplelocator'); ?>
		</label>
	</td>
</tr>
<tr valign="top">
	<th scope="row"><?php _e('Display Map in Singular View', 'wpsimplelocator'); ?></th>
	<td>
		<label>
			<input type="radio" value="true" name="wpsl_singular_data" <?php if ( get_option('wpsl_singular_data') == 'true') echo 'checked'; ?> />
			<?php _e('Yes', 'wpsimplelocator'); ?>
		</label><br />
		<label>
			<input type="radio" value="false" name="wpsl_singular_data" <?php if ( get_option('wpsl_singular_data') !== 'true') echo 'checked'; ?> />
			<?php _e('No', 'wpsimplelocator'); ?>
		</label>
	</td>
</tr>
<tr valign="top">
	<th scope="row"><?php _e('Location Button', 'wpsimplelocator'); ?></th>
	<td>
		<label>
			<input type="checkbox" id="wpsl_geo_button_enable" name="wpsl_geo_button[enabled]" value="true" <?php if ( $location_btn['enabled'] ) echo 'checked'; ?> />
			<?php _e('Display location button on geolocation enabled devices/browsers', 'wpsimplelocator');?>

		</label>
	</td>
</tr>
<tr valign="top" class="wpsl-location-text">
	<th scope="row"><?php _e('Location Button Text', 'wpsimplelocator'); ?></th>
	<td>
		<input type="text" name="wpsl_geo_button[text]" value="<?php echo esc_html($location_btn['text']); ?>" />
	</td>
</tr>