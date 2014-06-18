<div class="wrap">
	<h1>WP Simple Locator Settings</h1>
	<form method="post" action="options.php">
		<?php settings_fields( 'wp-simple-locator' ); ?>
		<table class="form-table">
			<tr valign="top">
			<th scope="row">Google Maps API Key</th>
			<td><input type="text" name="wpsl_google_api_key" value="<?php echo get_option('wpsl_google_api_key'); ?>" /></td>
			</tr>
			<tr valign="top">
			<th scope="row">Measurement Unit</th>
			<td>
				<select name="wpsl_measurement_unit">
					<option value="miles"
					<?php if ( $this->unit == "miles") echo ' selected'; ?>
					>Miles</option>
					<option value="kilometers"
					<?php if ( $this->unit == "kilometers") echo ' selected'; ?>
					>Kilometers</option>
				</select>
			</td>
			</tr>
			<tr valign="top">
			<th scope="row">Post Type for locations</th>
			<td>
				<select name="wpsl_post_type" id="wpsl_post_type">
				<?php echo $this->get_post_types(); ?>
				</select>
			</td>
			</tr>
			<tr valign="top">
			<th scope="row">Latitude &amp; Longitude Fields</th>
			<td>
			<p>
				<label for="field_wpsl" class="wpsl-field-type">
					<input type="radio" name="wpsl_field_type" id="field_wpsl" value="wpsl"
					<?php if ( $this->field_type == 'wpsl' ) echo ' checked'; ?>
					>Use WP Simple Locator Fields
				</label>
			</p>
			<p>
				<label for="field_custom" class="wpsl-field-type">
					<input type="radio" name="wpsl_field_type" id="field_custom" value="custom"
					<?php if ( $this->field_type == 'custom' ) echo ' checked'; ?>
					>Use Other Custom Fields
				</label>
			</p>
			</td>
			</tr>
			<tr valign="top" class="latlng">
			<th scope="row">Latitude Field</th>
			<td>
			<select id="lat_select">';
			<?php $this->show_field_options('wpsl_lat_field'); ?>
			</select>
			</td>
			</tr>

			<tr valign="top" class="latlng">
			<th scope="row">Longitude Field</th>
			<td>
			<select id="lng_select">
			<?php $this->show_field_options('wpsl_lng_field'); ?>
			</select>
			</td>
			</tr>
		</table>

		<input type="hidden" id="wpsl_lat_field" name="wpsl_lat_field"
		<?php 
		if ( get_option('wpsl_lat_field') ){
			echo ' value="' . get_option('wpsl_lat_field') .  '"';
		} else {
			echo ' value="wpsl_latitude"';
		}
		?>
		 />
		<input type="hidden" id="wpsl_lng_field" name="wpsl_lng_field"
		<?php
		if ( get_option('wpsl_lng_field') ){
			echo ' value="' . get_option('wpsl_lng_field') . '"';
		} else {
			echo ' value="wpsl_longitude"';
		}
		?>
		/>

		<?php submit_button(); ?>
	</form>
</div>