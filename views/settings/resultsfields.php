<?php settings_fields( 'wpsimplelocator-results' ); ?>
<div class="wpsl-settings">
	<div class="row">
		<div class="label align-top">
			<h4><?php _e('Search Result List', 'simple-locator'); ?></h4>
			<p><?php _e('Customize the display of search result lists. This content is also customizable in a plugin filter.', 'simple-locator'); ?></p>
		</div>
		<div class="field align-top">
			<?php 
			include(\SimpleLocator\Helpers::view('settings/result-field-custom-selection'));
			wp_editor($this->settings_repo->resultsFormatting(), 'wpsl_results_fields_formatted', [
				'media_buttons' => false,
				'textarea_name' => 'wpsl_results_fields_formatted[output]',
				'tabindex' => 1,
				'textarea_rows' => 12,
				'teeny' => true,
				'wpautop' => true
			]); ?>			
		</div><!-- .field -->
	</div><!-- .row -->
	<div class="row">
		<div class="label">
			<h4><?php _e('Result Count', 'simple-locator'); ?></h4>
			<p><?php _e('Limit the number of results shown. Enter a value of -1 for unlimited results.', 'simple-locator'); ?></p>
		</div>
		<div class="field">
			<label for="wpsl_results_fields_formatted"><?php _e('Number of Results to Show', 'simple-locator'); ?></label>
			<input type="text" name="wpsl_results_fields_formatted[limit]" id="wpsl_results_fields_formatted" value="<?php echo $this->settings_repo->resultsLimit(); ?>" />
		</div>
	</div><!-- .row -->
</div><!-- .wpsl-settings -->