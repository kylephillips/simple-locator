<?php settings_fields( 'wpsimplelocator-default' );?>
<div class="wpsl-settings">
	<div class="row subhead"><h4><?php _e('Default Map', 'simple-locator'); ?></h4></div>

	<div class="row">
		<div class="label">
			<h4><?php _e('Display Map on Page Load.', 'simple-locator'); ?></h4>
			<p><?php _e('Include a map below the form on page load.', 'simple-locator'); ?></p>
		</div>
		<div class="field">
			<label><input type="checkbox" id="wpsl_default_map_enable" name="wpsl_default_map[show]" data-simple-locator-default-checkbox value="true" <?php if ( $this->settings_repo->showDefaultMap() ) echo 'checked'; ?> /><?php _e('Show Default Map', 'simple-locator'); ?></label>
		</div>
	</div><!-- .row -->

	<div class="row" data-simple-locator-default-map-container style="display:none;">
		<div class="label align-top">
			<h4><?php _e('Default Map Location', 'simple-locator'); ?></h4>
			<p><?php _e('Set the location where the default map should load to. Zoom level will be saved as well. Drag and drop the marker for precise location.', 'simple-locator'); ?></p>
		</div>
		<div class="field align-top">
			<div class="wpsl-default-map" data-simple-locator-default-map>
				<div class="alert alert-error" style="display:none;" data-simple-locator-error><?php _e('The address could not be found.', 'simple-locator'); ?></div>
				<div class="search-form">
					<input type="text" id="wpsl_default_search" placeholder="<?php _e('Set location', 'simple-locator'); ?>" data-simple-locator-location-search-input>
					<button id="wpsl_default_submit" class="button" data-simple-locator-location-search-button><?php _e('Search', 'simple-locator'); ?></button>
				</div>
				<div id="wpsl-default"></div>
			</div>
			<input type="hidden" name="wpsl_default_map[latitude]" value="<?php echo $this->settings_repo->defaultMap('latitude'); ?>" id="wpsl_default_latitude" data-simple-locator-default-map-latitude>
			<input type="hidden" name="wpsl_default_map[longitude]" value="<?php echo $this->settings_repo->defaultMap('longitude'); ?>" id="wpsl_default_longitude" data-simple-locator-default-map-longitude>
			<input type="hidden" name="wpsl_default_map[zoom]" value="<?php echo $this->settings_repo->defaultMap('zoom'); ?>" id="wpsl_default_zoom" data-simple-locator-default-map-zoom>
		</div>
	</div><!-- .row -->

	<div class="row">
		<div class="label align-top">
			<h4><?php _e('Show Results on Page Load', 'simple-locator'); ?></h4>
			<p><?php _e('Center Default Map Using User\'s Location and automatically show results on page load. Important: map will default to above if geolocation is unavailable or denied by the user).', 'simple-locator'); ?></p>
		</div>
		<div class="field align-top">
			<label>
			<input type="checkbox" data-simple-locator-user-position-default name="wpsl_default_map[user_location]" value="true" <?php if ( $this->settings_repo->defaultMap('user_location') == 'true' ) echo 'checked'; ?> /><?php _e('Show Results Automatically Centered to User', 'simple-locator'); ?></label>
			<div style="display:none;" class="wpsl-error" data-simple-locator-no-https><?php _e('Your website doesn\'t appear to be running under the https protocol. User geolocation may not be available.', 'simple-locator'); ?> <a href="https://developers.google.com/web/updates/2016/04/geolocation-on-secure-contexts-only" target="_blank"><?php _e('Read More', 'simple-locator'); ?></a></div>
		</div>
	</div><!-- .row -->

</div><!-- .wpsl-settings -->