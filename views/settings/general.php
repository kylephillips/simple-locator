<?php settings_fields( 'wpsimplelocator-general' ); ?>
<div class="wpsl-settings">
	<div class="row">
		<div class="label"><h4><?php _e('Plugin Version', 'simple-locator'); ?></h4></div>
		<div class="field">
			<p class="no-margin"><?php echo get_option('wpsl_version'); ?></p>
		</div>
	</div>

	<div class="row">
		<div class="label"><h4><?php _e('Measurement Unit', 'simple-locator'); ?></h4></div>
		<div class="field">
			<select name="wpsl_measurement_unit">
				<option value="miles" <?php if ( $this->unit == "miles") echo ' selected'; ?>><?php _e('Miles', 'simple-locator'); ?></option>
				<option value="kilometers" <?php if ( $this->unit == "kilometers") echo ' selected'; ?>><?php _e('Kilometers', 'simple-locator'); ?></option>
			</select>
		</div>
	</div>

	<div class="row subhead"><h4><?php _e('Map Service', 'simple-locator'); ?></h4></div>

	<div class="row">
		<div class="label"><h4><?php _e('Map Service', 'simple-locator'); ?></h4></div>
		<div class="field">
			<label>
				<input type="radio" value="google" name="wpsl_map_service" <?php if ($this->settings_repo->mapService() == 'google' ) echo 'checked'; ?> />
				<?php _e('Google Maps', 'simple-locator'); ?>
			</label>
		</div>
	</div><!-- .row -->

	<div class="row">
		<div class="label">
			<h4><?php _e('Google Maps Javascript API Key', 'simple-locator'); ?></h4>
			<p><?php _e('A V3 API key is required for Simple Locator to function using the Google Maps library. The Maps library must be enabled along with a billing account in Google.', 'simple-locator'); ?> <a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank"><?php _e('How to obtain an API key', 'simple-locator'); ?></a></p>
		</div>
		<div class="field">
			<input type="text" name="wpsl_google_api_key" id="wpsl_google_api_key" value="<?php echo get_option('wpsl_google_api_key'); ?>" />
		</div>
	</div><!-- .row -->

	<div class="row">
		<div class="label">
			<h4><?php _e('Google Maps Javascript API Key - Import', 'simple-locator'); ?></h4>
			<p><?php printf(__('To use the import functionality, the Geocoding library must be added to this key. Google does not allow browser restrictions when calling the geocoding library server-side. To limit usage to this server, enter %s as an IP restriction', 'simple-locator'), $this->remote_address->getIpAddress()); ?> <a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank"><?php _e('How to obtain an API key', 'simple-locator'); ?></a></p>
		</div>
		<div class="field">
			<input type="text" name="wpsl_google_geocode_api_key" id="wpsl_google_geocode_api_key" value="<?php echo get_option('wpsl_google_geocode_api_key'); ?>" />
		</div>
	</div><!-- .row -->

	<div class="row">
		<div class="label">
			<h4><?php _e('Include Google Maps Library', 'simple-locator'); ?></h4>
			<p><?php _e('A Google Maps API with a valid key is required for Simple Locator to function. If another plugin includes the Google Maps API, disable it here to avoid conflicts.', 'simple-locator'); ?></p>
		</div>
		<div class="field">
			<p class="no-margin"><label>
				<input type="checkbox" value="true" name="wpsl_gmaps_api" <?php if ( $this->settings_repo->includeMapLibrary() ) echo 'checked'; ?> />
				<?php _e('Include in Front End', 'simple-locator'); ?>
			</label></p>
			<p class="no-margin"><label>
				<input type="checkbox" value="true" name="wpsl_gmaps_api_admin" <?php if ( $this->settings_repo->includeMapLibrary('admin') ) echo 'checked'; ?> />
				<?php _e('Include in Admin', 'simple-locator'); ?>
			</label></p>
		</div>
	</div><!-- .row -->

	<div class="row">
		<div class="label">
			<h4><?php _e('Enable Autocomplete in Search', 'simple-locator'); ?></h4>
			<p><?php _e('Autocomplete requires the places library to be enabled for your Google API key. Autocomplete options may be customized under the Map Display tab.', 'simple-locator'); ?></p>
		</div>
		<div class="field">
			<label>
				<input type="checkbox" value="true" name="wpsl_enable_autocomplete" <?php if ( get_option('wpsl_enable_autocomplete') == 'true') echo 'checked'; ?> />
				<?php _e('Enable Autcomplete in Location Field', 'simple-locator'); ?>
			</label>
		</div>
	</div><!-- .row -->

	<div class="row subhead"><h4><?php _e('Map Pins', 'simple-locator'); ?></h4></div>

	<div class="row">
		<div class="label"><h4><?php _e('Custom Map Pin (Results)', 'simple-locator'); ?></h4></div>
		<div class="field">
			<div style="float:left;" data-simple-locator-map-pin-image-container>
				<?php 
				if ( get_option('wpsl_map_pin') ){
					echo '<img src="' . get_option('wpsl_map_pin') . '" data-simple-locator-map-pin-image />';
				}
				?>
			</div>
			<?php 
			if ( get_option('wpsl_map_pin') ){
				echo '<input type="button" value="' . __('Remove', 'simple-locator') . '" class="button action" style="margin-right:5px;margin-left:10px;" data-simple-locator-remove-pin-button />';
			} else {
				echo '<input type="button" value="Upload" class="button action" data-simple-locator-upload-pin-button />';
			} ?>
			<input id="wpsl_map_pin" style="display:none;" type="text" size="36" name="wpsl_map_pin" value="<?php echo get_option('wpsl_map_pin'); ?>"  data-simple-locator-map-pin-input />
		</div><!-- .field -->
	</div><!-- .row -->

	<div class="row">
		<div class="label">
			<h4><?php _e('User Map Pin', 'simple-locator'); ?></h4>
			<p><?php _e('Displays a pin on the search results map marking the user\'s location.', 'simple-locator'); ?></p>
		</div>
		<div class="field">
			<label><input type="checkbox" data-simple-locator-toggle-user-pin name="wpsl_include_user_pin" value="true" <?php if ( $this->settings_repo->includeUserPin() ) echo 'checked'; ?> /><?php _e('Include User Pin in Search Results', 'maytronics'); ?></label>
		</div>
	</div><!-- .row -->

	<div class="row" data-simple-locator-user-map-pin>
		<div class="label"><h4><?php _e('Custom Map Pin (User Location)', 'simple-locator'); ?></h4></div>
		<div class="field">
			<div style="float:left;" data-simple-locator-map-pin-image-container>
				<?php 
				if ( get_option('wpsl_map_pin_user') ){
					echo '<img src="' . get_option('wpsl_map_pin_user') . '" data-simple-locator-map-pin-image />';
				}
				?>
			</div>
			<?php 
			if ( get_option('wpsl_map_pin_user') ){
				echo '<input type="button" value="' . __('Remove', 'simple-locator') . '" class="button action" style="margin-right:5px;margin-left:10px;" data-simple-locator-remove-pin-button />';
			} else {
				echo '<input type="button" value="Upload" class="button action" data-simple-locator-upload-pin-button />';
			} ?>
			<input id="wpsl_map_pin_user" type="text" size="36" name="wpsl_map_pin_user" value="<?php echo get_option('wpsl_map_pin_user'); ?>" style="display:none;" data-simple-locator-map-pin-input />
		</div><!-- .field -->
	</div><!-- .row -->

	<div class="row" data-simple-locator-cluster>
		<div class="label"><h4><?php _e('Enable marker clusters', 'simple-locator'); ?></h4></div>
		<div class="field">
			<label>
			<input type="checkbox" id="wpsl_marker_clusters" value="true" name="wpsl_marker_clusters" data-simple-locator-marker-cluster-input <?php if ($this->settings_repo->useMarkerClusters()) echo 'checked'; ?> />
			<?php _e('Enable marker clustering', 'simple-locator'); ?></label>
		</div>
	</div>

	<div class="row subhead"><h4><?php _e('Geolocation', 'simple-locator'); ?></h4></div>

	<div class="row">
		<div class="label">
			<h4><?php _e('Use My Location Button', 'simple-locator'); ?></h4>
			<p><?php _e('The location button will only appear if geolocation is available in the user\'s browser. Geolocation is available only for sites running under https protocol.', 'simple-locator'); ?><a href="https://developers.google.com/web/updates/2016/04/geolocation-on-secure-contexts-only" target="_blank"><?php _e('More Information', 'simple-locator'); ?></a></p>
		</div>
		<div class="field">
			<label>
				<input type="checkbox" id="wpsl_geo_button_enable" name="wpsl_geo_button[enabled]" value="true" <?php if ( $this->settings_repo->geoButton('enabled') ) echo 'checked'; ?> data-simple-locator-geo-enabled-checkbox />
				<?php _e('Display Geolocation Button when Available', 'simple-locator');?>
			</label>
			<div style="display:none;" class="wpsl-error" data-simple-locator-no-https><?php _e('Your website doesn\'t appear to be running under the https protocol. User geolocation may not be available.', 'simple-locator'); ?> <a href="https://developers.google.com/web/updates/2016/04/geolocation-on-secure-contexts-only" target="_blank"><?php _e('Read More', 'simple-locator'); ?></a></div>
		</div><!-- .field -->
	</div><!-- .row -->

	<div class="row wpsl-location-text" data-simple-locator-location-button-text>
		<div class="label">
			<h4><?php _e('Location Button Text', 'simple-locator'); ?></h4>
			<p><?php _e('May contain HTML tags.', 'simple-locator'); ?></p>
		</div>
		<div class="field">
			<input type="text" name="wpsl_geo_button[text]" value="<?php echo esc_html($this->settings_repo->geoButton('text')); ?>" />
		</div>
	</div><!-- .row -->

	<div class="row subhead"><h4><?php _e('Developer Tools & Theme Options', 'simple-locator'); ?></h4></div>

	<div class="row">
		<div class="label">
			<h4><?php _e('Display Map in Singular View', 'simple-locator'); ?></h4>
			<p><?php _e('The map will be automatically added to the selected post type\'s content. Maps may also be placed manually using the provided shortcode.', 'simple-locator'); ?></p>
		</div>
		<div class="field">
			<label>
				<input type="radio" value="true" name="wpsl_singular_data" <?php if ( get_option('wpsl_singular_data') == 'true') echo 'checked'; ?> />
				<?php _e('Yes', 'simple-locator'); ?>
			</label><br />
			<label>
				<input type="radio" value="false" name="wpsl_singular_data" <?php if ( get_option('wpsl_singular_data') !== 'true') echo 'checked'; ?> />
				<?php _e('No', 'simple-locator'); ?>
			</label>
		</div>
	</div><!-- .row -->

	<div class="row">
		<div class="label"><h4><?php _e('Output Simple Locator CSS', 'simple-locator'); ?></h4></div>
		<div class="field">
			<label>
				<input type="checkbox" value="true" name="wpsl_output_css" <?php if ( $this->settings_repo->includeCss() ) echo 'checked'; ?> />
				<?php _e('Include Plugin CSS', 'simple-locator'); ?>
			</label>
		</div>
	</div><!-- .row -->

	<div class="row">
		<div class="label">
			<h4><?php _e('Enable Javascript Debugging', 'simple-locator'); ?></h4>
			<p><?php _e('Logs server responses to the browser console for debugging purposes.', 'simple-locator'); ?></p>
		</div>
		<div class="field">
			<label><input type="checkbox" name="wpsl_js_debug" value="true" <?php if ( $this->settings_repo->jsDebug() ) echo 'checked';?> /><?php _e('Enable Javascript Console Logging', 'simple-locator'); ?></label>
		</div>
	</div><!-- .row -->

	<div class="row">
		<div class="label">
			<h4><?php _e('Save Searches', 'simple-locator'); ?></h4>
			<p><?php _e('Save user search details. Available under the "Search Log" tab after enabling.', 'simple-locator'); ?></p>
		</div>
		<div class="field">
			<label>
				<input type="checkbox" value="true" name="wpsl_save_searches" <?php if ( get_option('wpsl_save_searches') == 'true') echo 'checked'; ?> />
				<?php _e('Save Search History', 'simple-locator'); ?>
			</label>
		</div>
	</div><!-- .row -->

	<div class="row">
		<div class="label">
			<h4><?php _e('Non-AJAX Results', 'simple-locator'); ?></h4>
			<p><?php _e('By default, non-ajax results display in the post content of the specified page (or the current page if a resultspage option is not provided).', 'simple-locator'); ?></p>
		</div>
		<div class="field">
			<label>
				<input type="checkbox" value="true" name="wpsl_results_content_disabled" <?php if ( !$this->settings_repo->resultsInContent() ) echo 'checked'; ?> />
				<?php _e('Disable the Content Filter on Non-Ajax Results', 'simple-locator'); ?>
			</label>
			<p><?php _e('To display results in a custom area of your template, add following action:', 'simple-locator'); ?><br><code>do_action('simple_locator_results')</code></p>
		</div>
	</div><!-- .row -->

	<div class="row">
		<div class="label">
			<h4><?php _e('Admin Table Map', 'simple-locator'); ?></h4>
			<p><?php _e('Display a map of locations in the admin above the post list table.', 'simple-locator'); ?></p>
		</div>
		<div class="field">
			<label>
				<input type="checkbox" value="true" name="wpsl_display_admin_table_map" <?php if ( $this->settings_repo->includeAdminListMap() ) echo 'checked'; ?> />
				<?php _e('Display Admin Map in Post Listing', 'simple-locator'); ?>
			</label>
		</div>
	</div><!-- .row -->
</div><!-- .wpsl-settings -->