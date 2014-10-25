<div class="wrap">
	<h1><?php _e('WP Simple Locator Settings', 'wpsimplelocator'); ?></h1>
	<form method="post" action="options.php">
		<?php settings_fields( 'wp-simple-locator' ); ?>
		<table class="form-table">
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
			<tr valign="top">
			<th scope="row"><?php _e('Post Type for locations', 'wpsimplelocator'); ?></th>
			<td>
				<select name="wpsl_post_type" id="wpsl_post_type">
				<?php
				foreach ( $this->getPostTypes() as $type ){
					$out = '<option value="' . $type['name'] . '"';
					if ( $type['name'] == $this->post_type ) $out .= ' selected';
					$out .= '>';
					$out .= ( $type['name'] == 'location' ) ? __('Locations (Simple Locator Default)', 'wpsimplelocator') : $type['label'];
					$out .= '</option>';
					echo $out;
				}
				?>
				</select>
			</td>
			</tr>
			<tr valign="top">
			<th scope="row"><?php _e('Latitude & Longitude Fields', 'wpsimplelocator'); ?></th>
			<td>
			<p>
				<label for="field_wpsl" class="wpsl-field-type">
					<input type="radio" name="wpsl_field_type" id="field_wpsl" value="wpsl" <?php if ( $this->field_type == 'wpsl' ) echo ' checked'; ?>>
					<?php _e('Use WP Simple Locator Fields', 'wpsimplelocator'); ?>
				</label>
			</p>
			<p>
				<label for="field_custom" class="wpsl-field-type">
					<input type="radio" name="wpsl_field_type" id="field_custom" value="custom" <?php if ( $this->field_type == 'custom' ) echo ' checked'; ?>>
					<?php _e('Use Other Custom Fields', 'wpsimplelocator'); ?>
				</label>
			</p>
			</td>
			</tr>
			<tr valign="top" class="latlng">
			<th scope="row"><?php _e('Latitude Field', 'wpsimplelocator'); ?></th>
			<td>
			<select id="lat_select">';
			<?php $this->show_field_options('wpsl_lat_field'); ?>
			</select>
			</td>
			</tr>

			<tr valign="top" class="latlng">
			<th scope="row"><?php _e('Longitude Field', 'wpsimplelocator'); ?></th>
			<td>
			<select id="lng_select">
			<?php $this->show_field_options('wpsl_lng_field'); ?>
			</select>
			</td>
			</tr>
		</table>

		<input type="hidden" id="wpsl_lat_field" name="wpsl_lat_field" 
		value="<?php echo ( get_option('wpsl_lat_field') ) ? get_option('wpsl_lat_field') : 'wpsl_latitude'; ?>" />

		<input type="hidden" id="wpsl_lng_field" name="wpsl_lng_field"
		value="<?php echo ( get_option('wpsl_lng_field') ) ? get_option('wpsl_lat_field') : 'wpsl_longitude'; ?>" />

		<?php submit_button(); ?>
	</form>
</div>