<?php settings_fields( 'wpsimplelocator-map' ); // wpsl_map_styles?>
<tr valign="top">
	<td colspan="2" style="padding:0;">
		<h3><?php _e('Map Styles', 'wpsimplelocator'); ?></h3>
		<p><?php _e('Change the appearance of the generated maps by choosing a theme, or pasting your custom style code.', 'wpsimplelocator'); ?></p>
	</td>
</tr>
<tr valign="top">
	<th scope="row"><?php _e('Map Styles', 'wpsimplelocator'); ?></th>
	<td>
		<select name="wpsl_map_styles_type" id="wpsl_map_styles_type">
			<option value="none" <?php if ( $this->map_options['type'] == 'none' ) echo 'selected'; ?>>
				<?php _e('None (Default Google Maps)', 'wpsimplelocator'); ?>
			</option>
			<option value="choice" <?php if ( $this->map_options['type'] == 'choice' ) echo 'selected'; ?>>
				<?php _e('Choose from List', 'wpsimplelocator'); ?>
			</option>
			<option value="custom" <?php if ( $this->map_options['type'] == 'custom' ) echo 'selected'; ?>>
				<?php _e('Paste my own styles', 'wpsimplelocator'); ?>
			</option>
		</select>
	</td>
</tr>
<tr valign="top" id="map-styles-choice" <?php if ( $this->map_options['type'] !== 'choice' ) echo 'style="display:none;"'; ?>>
	<td colspan="2" style="padding:0;">
		<h3><?php _e('Choose a Style', 'wpsimplelocator'); ?></h3>
		<ul id="map-styles"></ul>
		<input type="hidden" id="wpsl_map_styles_choice" name="wpsl_map_styles_choice" />
	</td>
</tr>
<tr valign="top" id="map-styles-custom" <?php if ( $this->map_options['type'] !== 'custom' ) echo 'style="display:none;"'; ?>>
	<td colspan="2" style="padding:0;">
		<p><?php _e('Styles must be properly a formatted Javascript array. Visit', 'wpsimplelocator'); ?> <a href="http://gmaps-samples-v3.googlecode.com/svn/trunk/styledmaps/wizard/index.html" target="_blank"><?php _e('the Google Maps Style Wizard', 'wpsimplelocator'); ?></a> <?php _e('for an easy way to generate properly formatted styles','wpsimplelocator'); ?>.</p>
		<textarea name="wpsl_map_styles" id="wpsl_map_styles" class="widefat" style="height:200px;margin-top:20px;">
			<?php echo get_option('wpsl_map_styles'); ?>
		</textarea>
	</td>
</tr>