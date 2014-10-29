<?php settings_fields( 'wpsimplelocator-map' ); // wpsl_map_styles?>
<tr valign="top">
	<td colspan="2" style="padding:0;">
		<h3><?php _e('Map Styles', 'wpsimplelocator'); ?></h3>
		<p>Change the appearance of the generated maps by choosing a theme, or uploading your custom style code.</p>
	</td>
</tr>
<tr valign="top">
	<th scope="row"><?php _e('Map Styles', 'wpsimplelocator'); ?></th>
	<td>
		<select name="wpsl_map_styles_type" id="wpsl_map_styles_type">
			<option value="none" <?php if ( $this->map_options['type'] == 'none' ) echo 'selected'; ?>>None (Default Google Maps)</option>
			<option value="choice" <?php if ( $this->map_options['type'] == 'choice' ) echo 'selected'; ?>>Choose from List</option>
			<option value="custom" <?php if ( $this->map_options['type'] == 'custom' ) echo 'selected'; ?>>Paste my own styles</option>
		</select>
	</td>
</tr>
<tr valign="top" id="map-styles-choice" <?php if ( $this->map_options['type'] !== 'choice' ) echo 'style="display:none;"'; ?>>
	<td colspan="2" style="padding:0;">
		<h3>Choose a Style</h3>
		<ul id="map-styles"></ul>
	</td>
</tr>
<tr valign="top" id="map-styles-custom" <?php if ( $this->map_options['type'] !== 'custom' ) echo 'style="display:none;"'; ?>>
	<td colspan="2" style="padding:0;">
		<textarea name="wpsl_map_styles" id="wpsl_map_styles" class="widefat" style="height:200px;margin-top:20px;"><?php echo get_option('wpsl_map_styles'); ?></textarea>
	</td>
</tr>