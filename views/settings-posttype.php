<?php settings_fields( 'wpsimplelocator-posttype' ); ?>
<tr valign="top">
	<td colspan="2" style="padding:0;">
		<h3><?php _e('Location Post Type', 'wpsimplelocator'); ?></h3>
		<p><?php _e('Simple Pages comes with a post type of "Locations" with all the fields you need. If you\'d like to use a custom post type, select it below. If you have existing fields for latitude and longitude, select those.', 'wpsimplelocator'); ?></p>
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
				<?php _e('Use Simple Locator Fields', 'wpsimplelocator'); ?>
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

		<input type="hidden" id="wpsl_lat_field" name="wpsl_lat_field" 
		value="<?php echo ( get_option('wpsl_lat_field') ) ? get_option('wpsl_lat_field') : 'wpsl_latitude'; ?>" />

		<input type="hidden" id="wpsl_lng_field" name="wpsl_lng_field"
		value="<?php echo ( get_option('wpsl_lng_field') ) ? get_option('wpsl_lat_field') : 'wpsl_longitude'; ?>" />
	</td>
</tr>