<?php 
$file = get_transient('wpsl_import_file'); 

// Check that the columns have been mapped
if ( isset($file['file']) ) :
?>
<h3 class="wpsl-step-title"><?php _e('Step 2: Map Columns', 'wpsimplelocator'); ?> (<?php echo $file['filename']; ?>)</h3>

<?php 
// Form Errors
if ( isset($_GET['error']) ) echo '<div class="error"><p>' . $_GET['error'] . '</p></div>';
?>

<div class="wpsl-loading-settings">
	<img src="<?php echo plugins_url(); ?>/simple-locator/assets/images/loading-settings.gif" />
</div>

<form action="<?php echo admin_url('admin-post.php'); ?>" method="post" enctype="multipart/form-data" class="wpsl-upload-form">
	<input type="hidden" name="action" value="wpslmapcolumns">
	<div class="wpsl-column-selection" style="display:none;">
		<div class="wpsl-row-selection">
			<button data-direction="back" class="button" disabled><span class="dashicons dashicons-arrow-left"></span></button>
			<button data-direction="next" class="button"><span class="dashicons dashicons-arrow-right"></span></button>
			<span class="wpsl-current-row"><?php _e('Showing Row', 'wpsimplelocator'); ?> 1</span>
		</div>
		<ul>
			<li>
				<div class="wpsl-column-error">This column is required.</div>
				<span><?php _e('Title', 'wpsimplelocator'); ?>:*</span>
				<select name="wpsl_import_column_title" class="wpsl-import-column-selection">
					<option value="">--</option>
				</select>
			</li>
			<li>
				<div class="wpsl-column-error">This column is required.</div>
				<span><?php _e('Street Address', 'wpsimplelocator'); ?>*</span>
				<select name="wpsl_import_column_address" class="wpsl-import-column-selection">
					<option value="">--</option>
				</select>
			</li>
			<li>
				<div class="wpsl-column-error">This column is required.</div>
				<span><?php _e('City', 'wpsimplelocator'); ?>*</span>
				<select name="wpsl_import_column_city" class="wpsl-import-column-selection">
					<option value="">--</option>
				</select>
			</li>
			<li>
				<div class="wpsl-column-error">This column is required.</div>
				<span><?php _e('State/Province', 'wpsimplelocator'); ?>*</span>
				<select name="wpsl_import_column_state" class="wpsl-import-column-selection">
					<option value="">--</option>
				</select>
			</li>
			<li>
				<span><?php _e('Zip/Postal Code', 'wpsimplelocator'); ?></span>
				<select name="wpsl_import_column_zip" class="wpsl-import-column-selection">
					<option value="">--</option>
				</select>
			</li>
			<li>
				<span><?php _e('Telephone', 'wpsimplelocator'); ?>:</span>
				<select name="wpsl_import_column_phone" class="wpsl-import-column-selection">
					<option value="">--</option>
				</select>
			</li>
			<li>
				<span><?php _e('Website', 'wpsimplelocator'); ?>:</span>
				<select name="wpsl_import_column_website" class="wpsl-import-column-selection">
					<option value="">--</option>
				</select>
			</li>
			<li>
				<span><?php _e('Additional Information', 'wpsimplelocator'); ?>:</span>
				<select name="wpsl_import_column_additional" class="wpsl-import-column-selection">
					<option value="">--</option>
				</select>
			</li>
			<li>
				<span><?php _e('Post Content', 'wpsimplelocator'); ?>:</span>
				<select name="wpsl_import_column_content" class="wpsl-import-column-selection">
					<option value="">--</option>
				</select>
			</li>
		</ul>
		<div style="background-color:#e4e4e4;padding:8px;margin-bottom:20px;">
		<label style="clear:both;display:block;"><strong><?php _e('Import Status', 'wpsimplelocator'); ?></strong></label>
		<select name="wpsl_import_status" style="clear:both;float:none;">
			<option value="draft"><?php _e('Draft', 'wpsimplelocator'); ?></option>
			<option value="publish"><?php _e('Published', 'wpsimplelocator'); ?></option>
		</select>
		</div>
		<?php wp_nonce_field( 'wpsl-import-nonce', 'nonce' ) ?>
		<input type="submit" class="button wpsl_save_columns" value="<?php _e('Save Columns', 'wpsimplelocator'); ?>">
	</div>
</form>

<?php else : // Transient not yet set, no file uploaded ?>

<div class="error"><p><?php _e('CSV File not set.', 'wpsimplelocator'); ?></p></div>
<a href="<?php echo admin_url('options-general.php?page=wp_simple_locator&tab=import'); ?>" class="button"><?php _e('Upload CSV File', 'wpsimplelocator'); ?></a>

<?php endif; ?>