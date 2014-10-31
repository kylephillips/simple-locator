<?php wp_nonce_field( 'my_wpsl_meta_box_nonce', 'wpsl_meta_box_nonce' ); ?>
<div class="wpsl-meta">
	<p class="full">
		<label for="wpsl_address"><?php _e('Street Address', 'wpsimplelocator'); ?></label>
		<input type="text" name="wpsl_address" id="wpsl_address" value="<?php echo $this->meta['address']; ?>" />
	</p>
	<p class="city">
		<label for="wpsl_city"><?php _e('City', 'wpsimplelocator'); ?></label>
		<input type="text" name="wpsl_city" id="wpsl_city" value="<?php echo $this->meta['city']; ?>" />
	</p>
	<p class="state">
		<label for="wpsl_state"><?php _e('State', 'wpsimplelocator'); ?></label>
		<input type="text" name="wpsl_state" id="wpsl_state" value="<?php echo $this->meta['state']; ?>" />
	</p>
	<p class="zip">
		<label for="wpsl_zip"><?php _e('Zip', 'wpsimplelocator'); ?></label>
		<input type="text" name="wpsl_zip" id="wpsl_zip" value="<?php echo $this->meta['zip']; ?>" />
	</p>
	<div id="wpslmap"></div>
	<hr />
	<div class="latlng">
		<span><?php _e('Geocode values will update on save. Fields are for display purpose only.', 'wpsimplelocator'); ?></span>
		<p>
			<label for="wpsl_latitude"><?php _e('Latitude', 'wpsimplelocator'); ?></label>
			<input type="text" name="wpsl_latitude" id="wpsl_latitude" value="<?php echo $this->meta['latitude']; ?>" readonly />
		</p>
		<p class="lat">
			<label for="wpsl_longitude"><?php _e('Longitude', 'wpsimplelocator'); ?></label>
			<input type="text" name="wpsl_longitude" id="wpsl_longitude" value="<?php echo $this->meta['longitude']; ?>" readonly />
		</p>
	</div>
	<hr />
	<p class="half">
		<label for="wpsl_phone"><?php _e('Phone Number', 'wpsimplelocator'); ?></label>
		<input type="text" name="wpsl_phone" id="wpsl_phone" value="<?php echo $this->meta['phone']; ?>" />
	</p>
	<p class="half right">
		<label for="wpsl_website"><?php _e('Website', 'wpsimplelocator'); ?></label>
		<input type="text" name="wpsl_website" id="wpsl_website" value="<?php echo $this->meta['website']; ?>" />
	</p>
	<hr />
	<p class="full">
		<label for="wpsl_additionalinfo"><?php _e('Additional Info', 'wpsimplelocator'); ?></label>
		<textarea name="wpsl_additionalinfo" id="wpsl_additionalinfo"><?php echo $this->meta['additionalinfo']; ?></textarea>
	</p>
</div>