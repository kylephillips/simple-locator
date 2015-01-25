<?php settings_fields( 'wpsimplelocator-default' ); // wpsl_map_styles ?>
<tr valign="top">
	<td colspan="2" style="padding:0;">
		<h3><?php _e('Default Map', 'wpsimplelocator'); ?></h3>
	</td>
</tr>
<tr valign="top">
	<td colspan="2" style="padding:20px 0 0 0;">
		<label>
			<input type="checkbox" id="wpsl_default_map_enable" name="wpsl_default_map[show]" value="true" <?php if ( $this->settings_repo->showDefaultMap() ) echo 'checked'; ?> />
			<?php _e('Show Default Map on Page Load', 'wpsimplelocator'); ?>
		</label>
	</td>
</tr>
<tr valign="top">
	<td colspan="2">
		<div class="wpsl-default-map">
			<input type="text" id="wpsl_default_search" placeholder="<?php _e('Set location', 'wpsimplelocator'); ?>">
			<button id="wpsl_default_submit" class="button"><?php _e('Search', 'wpsimplelocator'); ?></button>
			<div id="wpsl-default"></div>
			<p><?php _e('Zoom level will be saved', 'wpsimplelocator'); ?></p>
		</div>
		<input type="hidden" name="wpsl_default_map[latitude]" value="<?php echo $this->settings_repo->defaultMap('latitude'); ?>" id="wpsl_default_latitude">
		<input type="hidden" name="wpsl_default_map[longitude]" value="<?php echo $this->settings_repo->defaultMap('longitude'); ?>" id="wpsl_default_longitude">
		<input type="hidden" name="wpsl_default_map[zoom]" value="<?php echo $this->settings_repo->defaultMap('zoom'); ?>" id="wpsl_default_zoom">
		<?php include('error-modal-settings.php'); ?>
	</td>
</tr>
<tr valign="top">
	<td colspan="2" style="padding:20px 0 0 0;">
		<label>
			<input type="checkbox" name="wpsl_default_map[user_location]" value="true" <?php if ( $this->settings_repo->defaultMap('user_location') == 'true' ) echo 'checked'; ?> />
			<?php _e('Center Default Map Using User\'s Location and automatically show results on page load (Map will default to above if geolocation is unavailable or refused by the user).', 'wpsimplelocator'); ?>
		</label>
	</td>
</tr>