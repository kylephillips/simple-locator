<?php 
$transient = $this->import_repo->transient();
$post_type = $transient['post_type'];

// Check that the columns have been mapped
if ( isset($transient['file']) && isset($transient['post_type']) ) :
?>
<h3 class="wpsl-step-title"><?php _e('Step 2: Map Columns', 'wpsimplelocator'); ?> (<?php echo $transient['filename']; ?>)</h3>

<?php 
// Form Errors
if ( isset($_GET['error']) ) echo '<div class="error"><p>' . $_GET['error'] . '</p></div>';
?>

<div class="wpsl-loading-settings">
	<img src="<?php echo plugins_url(); ?>/simple-locator/assets/images/loading-settings.gif" />
</div>

<div class="error wpsl-form-error" style="display:none;"></div>

<form action="<?php echo admin_url('admin-post.php'); ?>" method="post" enctype="multipart/form-data" class="wpsl-upload-form">
	<input type="hidden" name="action" value="wpslmapcolumns">
	<div class="wpsl-column-selection" style="display:none;">
		<div class="wpsl-row-selection">
			<button data-direction="back" class="button" disabled><span class="dashicons dashicons-arrow-left"></span></button>
			<button data-direction="next" class="button"><span class="dashicons dashicons-arrow-right"></span></button>
			<span class="wpsl-current-row"><?php _e('Showing Row', 'wpsimplelocator'); ?> 1</span>
		</div>
		<ul class="wpsl-column-fields">

			<li class="wpsl-import-header">
				<span><?php _e('Column', 'wpsimplelocator'); ?></span>
				<span><?php _e('WordPress Field', 'wpsimplelocator'); ?></span>
				<span><?php _e('Field Type', 'wpsimplelocator'); ?></span>
			</li>
			
			<li class="row-template wpsl-field">
				<div class="wpsl-column-error"></div>
				<select name="wpsl_import_field[0][csv_column]" class="wpsl-import-column-selection">
					<option value=""><?php _e('Choose Column', 'wpsimplelocator'); ?></option>
				</select>
				<select name="wpsl_import_field[0][field]" class="wpsl-import-field-selection">
					<option value=""><?php _e('Choose Field', 'wpsimplelocator'); ?></option>
				</select>
				<select name="wpsl_import_field[0][type]" class="wpsl-import-type-selection">
					<option value="other"><?php _e('Other', 'wpsimplelocator'); ?></option>
					<optgroup label="<?php _e('Address Fields', 'wpsimplelocator'); ?>">
						<option value="address"><?php _e('Street Address', 'wpsimplelocator'); ?></option>
						<option value="city"><?php _e('City', 'wpsimplelocator'); ?></option>
						<option value="state"><?php _e('State/Province', 'wpsimplelocator'); ?></option>
						<option value="zip"><?php _e('Zip/Postal Code', 'wpsimplelocator'); ?></option>
						<option value="full_address"><?php _e('Full Address', 'wpsimplelocator'); ?></option>
					</optgroup>
					<optgroup label="<?php _e('Formatted Fields', 'wpsimplelocator'); ?>">
						<option value="website"><?php _e('Website', 'wpsimplelocator'); ?></option>
					</optgroup>
				</select>
				<button class="wpsl-import-remove-field button" style="display:none;">-</button>
			</li>
		</ul>
		
		<div class="wpsl-import-add-field">
			<a href="#" class="button"><?php _e('Add Field', 'wpsimplelocator'); ?></a>
		</div>

		<div style="background-color:#f2f2f2;padding:8px;margin-bottom:20px;">
		<label style="clear:both;display:block;"><strong><?php _e('Import Status', 'wpsimplelocator'); ?></strong></label>
		<select name="wpsl_import_status" style="clear:both;float:none;">
			<option value="draft"><?php _e('Draft', 'wpsimplelocator'); ?></option>
			<option value="publish"><?php _e('Published', 'wpsimplelocator'); ?></option>
		</select>
		</div>

		<div style="background-color:#f2f2f2;padding:8px;margin-bottom:20px;">
		<label style="clear:both;display:block;"><input type="checkbox" name="wpsl_first_row_header" value="1" /><?php _e('Skip first row (header row)', 'wpsimplelocator'); ?></label>
		</div>

		<div class="wpsl-required-key">
			<p><?php _e('Two fields are required: a title field and at least one address field. If your address data is saved in one column, choose the "Full Address" field type.', 'wpsimplelocator'); ?></p>
		</div>

		<?php wp_nonce_field( 'wpsl-import-nonce', 'nonce' ) ?>
		<input type="hidden" id="wpsl-import-post-type" value="<?php echo $post_type; ?>">
		<input type="submit" class="button button-primary wpsl_save_columns" value="<?php _e('Save Columns', 'wpsimplelocator'); ?>">
	</div>
</form>

<?php else : // Transient not yet set, no file uploaded ?>

<div class="error"><p><?php _e('File not set.', 'wpsimplelocator'); ?></p></div>
<a href="<?php echo admin_url('options-general.php?page=wp_simple_locator&tab=import'); ?>" class="button"><?php _e('Upload CSV File', 'wpsimplelocator'); ?></a>

<?php endif; ?>