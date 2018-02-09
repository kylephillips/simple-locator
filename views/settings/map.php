<?php settings_fields( 'wpsimplelocator-map' ); // wpsl_map_styles?>
<tr valign="top">
	<td colspan="2" style="padding:0;">
		<h3><?php _e('Map Styles', 'simple-locator'); ?></h3>
		<p><?php _e('Change the appearance of the generated maps by choosing a theme, or pasting your custom style code.', 'simple-locator'); ?></p>
	</td>
</tr>
<tr valign="top">
	<th scope="row"><?php _e('Map Styles', 'simple-locator'); ?></th>
	<td>
		<select name="wpsl_map_styles_type" id="wpsl_map_styles_type" data-simple-locator-map-style-choice>
			<option value="none" <?php if ( $this->map_options['type'] == 'none' ) echo 'selected'; ?>>
				<?php _e('None (Default Google Maps)', 'simple-locator'); ?>
			</option>
			<option value="choice" <?php if ( $this->map_options['type'] == 'choice' ) echo 'selected'; ?>>
				<?php _e('Choose from List', 'simple-locator'); ?>
			</option>
			<option value="custom" <?php if ( $this->map_options['type'] == 'custom' ) echo 'selected'; ?>>
				<?php _e('Paste my own styles', 'simple-locator'); ?>
			</option>
		</select>
	</td>
</tr>
<tr valign="top" id="map-styles-choice" data-simple-locator-map-style="choice" <?php if ( $this->map_options['type'] !== 'choice' ) echo 'style="display:none;"'; ?>>
	<td colspan="2" style="padding:0;">
		<h3><?php _e('Choose a Style', 'simple-locator'); ?></h3>
		<ul id="map-styles" data-simple-locator-map-style-list></ul>
		<input type="hidden" id="wpsl_map_styles_choice" name="wpsl_map_styles_choice" data-simple-locator-map-style-choice-input value="<?php echo $this->map_options['choice']; ?>" />
	</td>
</tr>
<tr valign="top" id="map-styles-custom" data-simple-locator-map-style="custom" <?php if ( $this->map_options['type'] !== 'custom' ) echo 'style="display:none;"'; ?>>
	<td colspan="2" style="padding:0;">
		<p><?php _e('Styles must be properly a formatted Javascript array. Visit', 'simple-locator'); ?> <a href="http://gmaps-samples-v3.googlecode.com/svn/trunk/styledmaps/wizard/index.html" target="_blank"><?php _e('the Google Maps Style Wizard', 'simple-locator'); ?></a> <?php _e('for an easy way to generate properly formatted styles','simple-locator'); ?>.</p>
		<textarea name="wpsl_map_styles" id="wpsl_map_styles" class="widefat" style="height:200px;margin-top:20px;">
			<?php echo get_option('wpsl_map_styles'); ?>
		</textarea>
	</td>
</tr>
<tr valign="top">
	<td colspan="2">
		<h3><?php _e('Google Map Options', 'simple-locator'); ?></h3>
		<p style="margin-bottom:20px;"><?php _e('Important: This must be a properly formatted JavaScript object. Changing this value will overwrite any other settings such as map styles or shortcode options. To use the map styles specified above, use the global object <code>wpsl_locator.mapstyles</code>.', 'simple-locator'); ?> <a href="https://developers.google.com/maps/documentation/javascript/reference#MapOptions" target="_blank"> <?php _e('For available properties and more information, visit the Google Maps Javascript API reference.', 'simple-locator'); ?></a></p>
		<label><input type="checkbox" name="wpsl_custom_map_options" value="1" id="wpsl_custom_map_options" data-simple-locator-custom-map-options-checkbox <?php if ( $this->settings_repo->customMapOptions() ) echo 'checked'; ?>><?php _e('Customize Javscript Map Options', 'simple-locator');?></label>
		<textarea id="wpsl_map_options" name="wpsl_map_options" data-simple-locator-custom-map-options style="width:100%;height:200px;margin-top:20px;<?php if ( !$this->settings_repo->customMapOptions() ) echo 'display:none;' ?>"><?php echo $this->settings_repo->mapOptions(); ?></textarea>
	</td>
</tr>