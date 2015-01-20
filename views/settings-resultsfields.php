<?php 
settings_fields( 'wpsimplelocator-results' ); 
$post_type_fields = $this->field_repo->getFieldsForPostType($this->post_type);
$limit = ( $this->settings_repo->resultsOption('limit') !== "" ) ? $this->settings_repo->resultsOption('limit') : '-1';
?>
<div class="wpsl-results-fields">

	<div class="wpsl-results-fields-selection">
		<h4><?php _e('Drag and drop to configure results display below.', 'wpsimplelocator'); ?></h4>
		<ul class="wpsl-results-selection">
			<?php foreach($this->settings_repo->resultsFieldsArray() as $key => $chosen_field) : ?>
			<li class="field">
				<i class="sl-icon-menu handle"></i>
				<select name="wpsl_results_fields[fields][<?php echo $key; ?>][field]">
					<?php 
						foreach($post_type_fields as $field){
							$out = '<option value="' . $field . '"';
							if ( $field == $chosen_field['field'] ) $out .= ' selected';
							$out .= '>' . $field . '</option>';
							echo $out;
						}
					?>
				</select>
				<a href="#" class="wpsl-remove-field button"><i class="sl-icon-minus"></i></a>
				<a href="#" class="wpsl-toggle-code button"><i class="sl-icon-embed2"></i></a>
				<div class="wpsl-before-after">
					<p class="wpsl-before-text">
						<label for="wpsl_results_before"><?php _e('Before', 'wpsimplelocator'); ?></label>
						<input type="text" id="wpsl_results_before" name="wpsl_results_fields[fields][<?php echo $key; ?>][before]" value="<?php echo esc_html($chosen_field['before']); ?>">
					</p>
					<p class="wpsl-after-text">
						<label for="wpsl_results_after"><?php _e('After', 'wpsimplelocator'); ?></label>
						<input type="text" id="wpsl_results_after" name="wpsl_results_fields[fields][<?php echo $key; ?>][after]" value="<?php echo esc_html($chosen_field['after']); ?>" />
					</p>
					<p class="wpsl-field-type">
						<label for="wpsl_field_type"><?php _e('Field Type', 'wpsimplelocator'); ?></label>
						<select id="wpsl_field_type" name="wpsl_results_fields[fields][<?php echo $key; ?>][type]">
							<option value="text" <?php if ( $chosen_field['type'] == 'text' ) echo ' selected'; ?> ><?php _e('Text', 'wpsimplelocator'); ?></option>
							<option value="url" <?php if ( $chosen_field['type'] == 'url' ) echo ' selected'; ?>><?php _e('URL', 'wpsimplelocator'); ?></option>
						</select>
					</p>
				</div>
			</li>
			<?php endforeach; ?>
		</ul><!-- .wpsl-results-selection -->
		
		<a href="#" class="wpsl-add-field button-primary"><?php _e('Add Field', 'wpsimplelocator');?></a>

		<p class="wpsl-limit-setting">
			<label for="wpsl_results_fields_limit"><?php _e('Number of Results to Show (-1 for unlimited results)', 'wpsimplelocator'); ?></label>
			<input type="text" name="wpsl_results_fields[limit]" id="wpsl_results_fields_limit" value="<?php echo $limit; ?>" />
		</p>
		<p class="wpsl-limit-setting">
			<label for="wpsl_results_fields_distance">
				<input type="checkbox" name="wpsl_results_fields[show_distance]" value="true" <?php if ( $this->settings_repo->resultsOption('show_distance') == 'true' ) echo ' checked';?>>
				<?php _e('Show the distance in results', 'wpsimplelocator'); ?>
			</label>
		</p>

		<p><?php _e('Text before and after fields can be used for formatting display (HTML is permitted)', 'wpsimplelocator'); ?></p>
	</div>

</div><!-- .wpsl-results-fields -->