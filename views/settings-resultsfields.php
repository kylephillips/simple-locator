<?php 
settings_fields( 'wpsimplelocator-results' ); 
$post_type_fields = $this->field_repo->getFieldsForPostType($this->post_type);
//var_dump($this->settings_repo->resultsFieldsArray());
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
					
					<input type="text" name="wpsl_results_fields[fields][<?php echo $key; ?>][before]" class="wpsl-before-text" placeholder="<?php _e('Before Field', 'wpsimplelocator'); ?>*" value="<?php echo esc_html($chosen_field['before']); ?>">

					<input type="text" name="wpsl_results_fields[fields][<?php echo $key; ?>][after]" class="wpsl-after-text" placeholder="<?php _e('After Field', 'wpsimplelocator'); ?>*" value="<?php echo esc_html($chosen_field['after']); ?>" />
				</div>
			</li>
			<?php endforeach; ?>
		</ul><!-- .wpsl-results-selection -->
		
		<a href="#" class="wpsl-add-field button-primary"><?php _e('Add Field', 'wpsimplelocator');?></a>

		<p>*<?php _e('Text before and after fields can be used for formatting display (HTML is permitted)', 'wpsimplelocator'); ?></p>
	</div>

</div><!-- .wpsl-results-fields -->