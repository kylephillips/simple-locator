<?php 
$transient = $this->import_repo->transient();
$post_type = $transient['post_type'];

// Check that the columns have been mapped
if ( isset($transient['file']) && isset($transient['post_type']) ) :
?>
<h3 class="wpsl-step-title">
	<?php _e('Step 2: Map Columns', 'simple-locator'); ?> (<?php echo $transient['filename']; ?>)
</h3>

<?php 
// Form Errors
if ( isset($_GET['error']) ) echo '<div class="error"><p>' . $_GET['error'] . '</p></div>';
?>

<div class="wpsl-loading-settings" data-simple-locator-import-loading>
	<div class="wpsl-icon-spinner"><div class="wpsl-icon-spinner-image"><img src="<?php echo \SimpleLocator\Helpers::plugin_url() . '/assets/images/loading-spinner.svg'; ?>" class="wpsl-spinner-image" /></div></div>
	</div>
</div>

<div class="error wpsl-form-error" style="display:none;" data-simple-locator-form-error></div>

<div class="wpsl-required-key">
	<p><?php _e('Two fields are required: a title field and at least one address field. If your address data is saved in one column, choose the "Full Address" field type.', 'simple-locator'); ?></p>
</div>

<div class="wpsl-settings">
	<form action="<?php echo admin_url('admin-post.php'); ?>" method="post" enctype="multipart/form-data" data-simple-locator-import-column-form>
		<input type="hidden" name="action" value="wpslmapcolumns">
		<div class="wpsl-column-selection" style="display:none;" data-simple-locator-import-column-selection>
			<div class="column-selection-inner">
				<div class="wpsl-row-selection">
					<button data-simple-locator-import-row-selection-button="back" class="button" disabled><span class="dashicons dashicons-arrow-left"></span></button>
					<button data-simple-locator-import-row-selection-button="next" class="button"><span class="dashicons dashicons-arrow-right"></span></button>
					<span class="wpsl-current-row" data-simple-locator-import-current-row><?php _e('Showing Row', 'simple-locator'); ?> 1</span>
				</div>
				<ul class="wpsl-column-fields">

					<li class="wpsl-import-header">
						<span><?php _e('Column', 'simple-locator'); ?></span>
						<span><?php _e('WordPress Field', 'simple-locator'); ?></span>
						<span><?php _e('Field Type', 'simple-locator'); ?></span>
					</li>
					
					<li class="row-template wpsl-field" data-simple-locator-import-field>
						<div class="wpsl-column-error" data-simple-locator-column-error></div>
						<select name="wpsl_import_field[0][csv_column]" data-simple-locator-import-column-select>
							<option value=""><?php _e('Choose Column', 'simple-locator'); ?></option>
						</select>
						<select name="wpsl_import_field[0][field]" data-simple-locator-import-field-select>
							<option value=""><?php _e('Choose Field', 'simple-locator'); ?></option>
						</select>
						<select name="wpsl_import_field[0][type]" data-simple-locator-import-type-select>
							<option value="other"><?php _e('Other', 'simple-locator'); ?></option>
							<optgroup label="<?php _e('Address Fields', 'simple-locator'); ?>">
								<option value="address"><?php _e('Street Address (Primary)', 'simple-locator'); ?></option>
								<option value="city"><?php _e('City', 'simple-locator'); ?></option>
								<option value="state"><?php _e('State/Province', 'simple-locator'); ?></option>
								<option value="zip"><?php _e('Zip/Postal Code', 'simple-locator'); ?></option>
								<option value="full_address"><?php _e('Full Address', 'simple-locator'); ?></option>
							</optgroup>
							<optgroup label="<?php _e('Formatted Fields', 'simple-locator'); ?>">
								<option value="website"><?php _e('Website', 'simple-locator'); ?></option>
							</optgroup>
						</select>
						<div class="unique-field">
							<label><input type="checkbox" name="wpsl_import_field[0][unique]" value="1" data-simple-locator-import-unique-identifier />
								<?php _e('Unique Identifier', 'simple-locator'); ?></label>
						</div>
						<button class="button" style="display:none;" data-simple-locator-import-remove-field>-</button>
					</li>
				</ul>
				
				<div class="wpsl-import-add-field">
					<a href="#" class="button" data-simple-locator-import-add-field><?php _e('Add Field', 'simple-locator'); ?></a>
				</div>
			</div><!-- .column-selection-inner -->
		</div><!-- .wpsl-column-selection -->

		<div class="row">
			<div class="label">
				<h4><?php _e('Skip First Row', 'simple-locator'); ?></h4>
				<p><?php _e('If your CSV\'s first row contains field/column names, check here to exclude it from being imported.', 'simple-locator'); ?></p>
			</div>
			<div class="field">
				<label><input type="checkbox" name="wpsl_first_row_header" value="1" /><?php _e('Skip First Row', 'simple-locator'); ?></label>
			</div>
		</div>

		<div class="row" data-import-post-status>
			<div class="label">
				<h4><?php _e('Post Status', 'simple-locator'); ?></h4>
				<p><?php _e('Set the post status to draft or published.', 'simple-locator'); ?></p>
			</div>
			<div class="field">
				<select name="wpsl_import_status" style="clear:both;float:none;">
					<option value="draft"><?php _e('Draft', 'simple-locator'); ?></option>
					<option value="publish"><?php _e('Published', 'simple-locator'); ?></option>
				</select>
			</div>
		</div>

		<div class="row" style="display:none;" data-import-taxonomy-separator>
			<div class="label">
				<h4><?php _e('Taxonomy Separator', 'simple-locator'); ?></h4>
				<p><?php _e('If a column(s) containing taxonomies are being imported, what separates the terms within the column?.', 'simple-locator'); ?></p>
			</div>
			<div class="field">
				<select name="wpsl_import_taxonomy_separator" style="clear:both;float:none;">
					<option value="comma"><?php _e('Comma', 'simple-locator'); ?></option>
					<option value="pipe"><?php _e('Pipe', 'simple-locator'); ?></option>
				</select>
			</div>
		</div>

		<div class="row" style="display:none;" data-import-unique-action>
			<div class="label">
				<h4><?php _e('Duplicate Handling', 'simple-locator'); ?></h4>
				<p><?php _e('If a row with a matching unique identifier is found, how should it be handled?.', 'simple-locator'); ?></p>
			</div>
			<div class="field">
				<select name="wpsl_import_duplicate_handling" style="clear:both;float:none;">
					<option value="skip"><?php _e('Skip', 'simple-locator'); ?></option>
					<option value="update"><?php _e('Update Post', 'simple-locator'); ?></option>
				</select>
			</div>
		</div>

		<div class="wpsl-columns-submit">
			<?php wp_nonce_field( 'wpsl-import-nonce', 'nonce' ) ?>
			<input type="hidden" id="wpsl-import-post-type" data-simple-locator-import-post-type value="<?php echo $post_type; ?>">
			<input type="submit" class="button button-primary" data-simple-locator-import-save value="<?php _e('Save Columns', 'simple-locator'); ?>">
		</div>
		
	</form>
</div><!-- .wpsl-settings -->

<?php else : // Transient not yet set, no file uploaded ?>

<div class="wpsl-error"><p><?php _e('File not set.', 'simple-locator'); ?></p></div>
<a href="<?php echo admin_url('options-general.php?page=wp_simple_locator&tab=import'); ?>" class="button"><?php _e('Upload CSV File', 'simple-locator'); ?></a>

<?php endif; ?>