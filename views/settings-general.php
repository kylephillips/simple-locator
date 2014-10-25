<?php settings_fields( 'wpsimplelocator-general' ); ?>
<tr valign="top">
	<th scope="row"><?php _e('Google Maps API Key', 'wpsimplelocator'); ?></th>
	<td><input type="text" name="wpsl_google_api_key" value="<?php echo get_option('wpsl_google_api_key'); ?>" /></td>
</tr>
<tr valign="top">
	<th scope="row"><?php _e('Measurement Unit', 'wpsimplelocator'); ?></th>
	<td>
		<select name="wpsl_measurement_unit">
			<option value="miles" <?php if ( $this->unit == "miles") echo ' selected'; ?>><?php _e('Miles', 'wpsimplelocator'); ?></option>
			<option value="kilometers" <?php if ( $this->unit == "kilometers") echo ' selected'; ?>><?php _e('Kilometers', 'wpsimplelocator'); ?></option>
		</select>
	</td>
</tr>