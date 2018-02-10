<?php settings_fields( 'wpsimplelocator-map' ); // wpsl_map_styles?>
<div class="wpsl-settings">
	<div class="row subhead"><h4><?php _e('Map Styles', 'simple-locator'); ?></h4></div>

	<div class="row">
		<div class="label"><h4><?php _e('Map Style Type', 'simple-locator'); ?></h4></div>
		<div class="field">
			<select name="wpsl_map_styles_type" class="full" id="wpsl_map_styles_type" data-simple-locator-map-style-choice>
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
		</div>
	</div><!-- .row -->

	<div class="wpsl-settings-map-style-choice" data-simple-locator-map-style="choice" <?php if ( $this->map_options['type'] !== 'choice' ) echo 'style="display:none;"'; ?>>
		<ul data-simple-locator-map-style-list></ul>
		<input type="hidden" id="wpsl_map_styles_choice" name="wpsl_map_styles_choice" data-simple-locator-map-style-choice-input value="<?php echo $this->map_options['choice']; ?>" />
	</div><!-- .map-style-choice -->

	<div class="wpsl-settings-map-style-custom" data-simple-locator-map-style="custom" <?php if ( $this->map_options['type'] !== 'custom' ) echo 'style="display:none;"'; ?>>
		<div class="wpsl-alert"><?php _e('Styles must be properly a formatted Javascript array. Visit', 'simple-locator'); ?> <a href="https://mapstyle.withgoogle.com/" target="_blank"><?php _e('the Google Maps Style Wizard', 'simple-locator'); ?></a> <?php _e('for an easy way to generate properly formatted styles','simple-locator'); ?>.</div>
		<textarea name="wpsl_map_styles" id="wpsl_map_styles" class="widefat" style="height:200px;margin-top:20px;">
			<?php echo get_option('wpsl_map_styles'); ?>
		</textarea>
	</div><!-- .map-style-custom -->

	<div class="row subhead"><h4><?php _e('Map Service Options', 'simple-locator'); ?></h4></div>

	<div class="row">
		<div class="label align-top">
			<h4><?php _e('Customize Javascript Map Options', 'simple-locator'); ?></h4>
			<p><?php _e('Options must be a properly formatted object. The options are passed into the Google Map object on creation.', 'simple-locator'); ?> <a href="https://developers.google.com/maps/documentation/javascript/reference#MapOptions"><?php _e('(More information)', 'simple-locator'); ?></a></p>
		</div>
		<div class="field align-top">
			<label><input type="checkbox" name="wpsl_custom_map_options" value="1" id="wpsl_custom_map_options" data-simple-locator-custom-map-options-checkbox <?php if ( $this->settings_repo->customMapOptions() ) echo 'checked'; ?>><?php _e('Customize Javscript Map Options', 'simple-locator');?></label>
			<textarea id="wpsl_map_options" name="wpsl_map_options" data-simple-locator-custom-map-options style="width:100%;height:200px;margin-top:20px;<?php if ( !$this->settings_repo->customMapOptions() ) echo 'display:none;' ?>"><?php echo $this->settings_repo->mapOptions(); ?></textarea>
		</div>
	</div><!-- .row -->

	<?php if ( $this->settings_repo->autocomplete() ) : ?>
	<div class="row">
		<div class="label align-top">
			<h4><?php _e('Customize Autocomplete Options', 'simple-locator'); ?></h4>
			<p><?php _e('Pass custom options object into the Google Places Autocomplete object on creation.', 'simple-locator'); ?> <a href="https://developers.google.com/places/web-service/autocomplete" target="_blank"><?php _e('Documentation', 'simple-locator'); ?></a></p>
		</div>
		<div class="field align-top">
			<label><input type="checkbox" name="wpsl_custom_autocomplete_options" value="1" data-simple-locator-custom-autocomplete-option <?php if ( $this->settings_repo->customAutocompleteOptions() ) echo 'checked'; ?> /><?php _e('Customize Google Maps Autocomplete Options', 'simple-locator'); ?> </label>
			<textarea name="wpsl_autocomplete_options" data-simple-locator-custom-autocomplete style="width:100%;height:200px;margin-top:20px;<?php if ( !$this->settings_repo->customAutocompleteOptions() ) echo 'display:none;' ?>"><?php echo $this->settings_repo->autocompleteOptions(); ?></textarea>
		</div>
	</div>
	<?php endif; ?>

</div><!-- .wpsl-settings -->