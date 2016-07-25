<?php 
settings_fields( 'wpsimplelocator-results' ); 
$post_type_fields = $this->field_repo->getFieldsForPostType($this->post_type);
$post_type = get_post_type_object($this->post_type);
$resultoutput = get_option('wpsl_results_fields_formatted');
$resultoutput = $resultoutput['output'];
$image_sizes = get_intermediate_image_sizes();
?>
<div class="wpsl-results-fields">

	<div class="wpsl-results-fields-selection">
		<div class="wpsl-results-field-selector">
			<div class="left">
				<label for="wpsl-fields"><?php echo $post_type->labels->name; ?> <?php _e('Fields', 'wpsimplelocator'); ?></label>
				<select id="wpsl-fields">
					<option value="distance"><?php _e('Distance', 'wpsimplelocator'); ?></option>
					<option value="show_on_map"><?php _e('Show on Map', 'wpsimplelocator'); ?></option>
					<?php 
						foreach($post_type_fields as $field) {
							echo '<option value="' . $field . '">' . $field . '</option>';
						}
					?>
				</select>
				<button class="wpsl-field-add button"><?php _e('Add', 'wpsimplelocator');?></button>
			</div>
			<div class="right">
				<label for="wpsl-post-fields"><?php _e('Post Data', 'wpsimplelocator'); ?></label>
				<select id="wpsl-post-fields">
					<option value="post_title"><?php _e('Title', 'wpsimplelocator'); ?></option>
					<option value="post_excerpt"><?php _e('Excerpt', 'wpsimplelocator'); ?></option>
					<option value="post_permalink"><?php _e('Permalink', 'wpsimplelocator'); ?></option>
					<?php foreach($image_sizes as $size) : ?>
					<option value="post_thumbnail_<?php echo $size; ?>"><?php echo __('Thumbnail', 'wpsimplelocator') . ' - ' . $size; ?></option>
					<?php endforeach; ?>
				</select>
				<button class="wpsl-post-field-add button"><?php _e('Add', 'wpsimplelocator');?></button>
			</div>
		</div>
		<?php 
			wp_editor($resultoutput, 'wpsl_results_fields_formatted', 
				array(
					'media_buttons' => false,
					'textarea_name' => 'wpsl_results_fields_formatted[output]',
					'tabindex' => 1,
					'teeny' => true,
					'wpautop' => true
					)
				); 
			?>

		<p class="wpsl-limit-setting">
			<label for="wpsl_results_fields_formatted"><?php _e('Number of Results to Show (-1 for unlimited results)', 'wpsimplelocator'); ?></label>
			<input type="text" name="wpsl_results_fields_formatted[limit]" id="wpsl_results_fields_formatted" value="<?php echo $this->settings_repo->resultsLimit(); ?>" />
		</p>
	</div>

</div><!-- .wpsl-results-fields -->