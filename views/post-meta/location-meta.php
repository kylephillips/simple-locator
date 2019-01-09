<?php wp_nonce_field( 'my_wpsl_meta_box_nonce', 'wpsl_meta_box_nonce' ); ?>
<div class="wpsl-meta">
	<p class="full wpsl-address-field">
		<?php echo $this->form_fields->address($this->meta['address'], $post->ID); ?>
	</p>
	<p class="full wpsl-address-two-field">
		<?php echo $this->form_fields->address_two($this->meta['address_two'], $post->ID); ?>
	</p>
	<p class="city wpsl-city-field">
		<?php echo $this->form_fields->city($this->meta['city'], $post->ID); ?>
	</p>
	<p class="state wpsl-state-field">
		<?php echo $this->form_fields->city($this->meta['state'], $post->ID); ?>
	</p>
	<p class="zip wpsl-zip-field">
		<?php echo $this->form_fields->postalCode($this->meta['zip'], $post->ID); ?>
	</p>
	<p class="full wpsl-country-field">
		<?php echo $this->form_fields->country($this->meta['country'], $post->ID); ?>
	</p>
	<div id="wpslmap"></div>
	<hr />
	<div class="latlng">
		<span><?php _e('Geocode values will update on save. Fields are for display purpose only.', 'simple-locator'); ?></span>
		<p class="wpsl-latitude-field">
			<label for="wpsl_latitude"><?php _e('Latitude', 'simple-locator'); ?></label>
			<input type="text" name="wpsl_latitude" id="wpsl_latitude" value="<?php echo $this->meta['latitude']; ?>" readonly />
		</p>
		<p class="lat wpsl-longitude-field">
			<label for="wpsl_longitude"><?php _e('Longitude', 'simple-locator'); ?></label>
			<input type="text" name="wpsl_longitude" id="wpsl_longitude" value="<?php echo $this->meta['longitude']; ?>" readonly />
		</p>
	</div>
	<div class="wpsl-extra-meta-fields">
		<hr />
		<p class="half wpsl-phone-field">
			<?php echo $this->form_fields->phone($this->meta['phone'], $post->ID); ?>
		</p>
		<p class="half right wpsl-website-field">
			<?php echo $this->form_fields->website($this->meta['website'], $post->ID); ?>
		</p>
		<hr />
		<p class="full wpsl-additional-field">
			<?php echo $this->form_fields->additionalInfo($this->meta['additionalinfo'], $post->ID); ?>
		</p>
	</div>
	<input type="hidden" name="wpsl_custom_geo" id="wpsl_custom_geo" value="<?php echo $this->meta['mappinrelocated']; ?>">
</div>
<?php include('error-modal.php');?>