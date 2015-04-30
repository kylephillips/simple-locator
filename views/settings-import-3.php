<?php
$transient = get_transient('wpsl_import_file');
//var_dump($transient);
// Check that the columns have been mapped

/**
* @todo
* Check if last_impported !== 0. If not, display last row imported, import time, update # of rows to import
*/
if ( !isset($transient['columns']) ) :
?>

<div class="error"><p><?php _e('Column data is not yet mapped.', 'wpsimplelocator'); ?></p></div>
<a href="<?php echo admin_url('options-general.php?page=wp_simple_locator&tab=import&step=2'); ?>" class="button"><?php _e('Map Column Data', 'wpsimplelocator'); ?></a>


<?php else : ?>
<h3 class="wpsl-step-title"><?php _e('Step 3: Import and Geocode', 'wpsimplelocator'); ?></h3>

<div class="error wpsl-import-error" style="display:none;"><p></p></div>

<!-- Intro Message -->
<div class="wpsl-import-indicator-intro">
	<p><strong><?php _e('Total Rows to Import'); ?>:</strong> <?php echo $transient['row_count']; ?> <?php _e('rows from', 'wpsimplelocator'); ?> <?php echo $transient['filename']; ?></p>
	<p><?php _e('Once the import has begun, do not close or refresh the page until complete.', 'wpsimplelocator'); ?></p>
	<input type="hidden" name="last_imported" value="<?php echo $transient['last_imported']; ?>">
	<p><button class="wpsl-start-import button"><?php _e('Start Import', 'wpsimplelocator'); ?></button></p>
</div>

<!-- Progress Indicator -->
<div class="wpsl-import-indicator">
	<p><strong><?php _e('Important', 'wpsimplelocator'); ?>:</strong> <?php _e('Import is in progress. Do not close or refresh this page.', 'wpsimplelocator'); ?></p>

	<div class="wpsl-import-progress">
		<span class="progress-bar"></span>
		<span class="progress-bar-bg" data-total="<?php echo $transient['row_count']; ?>"></span>
		<p><span class="progress-count">0</span> <?php _e('of', 'wpsimplelocator'); echo ' ' . $transient['row_count']; ?> <?php _e('Rows Imported', 'wpsimplelocator'); ?> (<span class="error-count">0</span> <?php _e('Errors', 'wpsimplelocator'); ?>)</p>
		<p>
			<button class="button wpsl-pause-import"><?php _e('Pause', 'wpsimplelocator'); ?></button>
		</p>
	</div>
</div>

<!-- Import Complete Alert -->
<div class="wpsl-import-complete updated" style="display:none;">
	<p><?php _e('The import is complete.', 'wpsimplelocator'); ?> <span class="progress-count">0</span> <?php _e('of', 'wpsimplelocator'); echo ' ' . $transient['row_count']; ?> <?php _e('Rows Imported', 'wpsimplelocator'); ?> (<span class="error-count">0</span> <?php _e('Errors', 'wpsimplelocator'); ?>)</p>
</div>

<!-- Import Details Display after import is complete -->
<div class="wpsl-import-details" style="display:none;">
	<p><strong><?php _e('Total Posts Imported:', 'wpsimplelocator'); ?> <span class="wpsl-total-import-count"></span></strong></p>
	<h4><?php _e('Error Log', 'wpsimplelocator'); ?> (<span class="wpsl-total-error-count"></span> <?php _e('Errors', 'wpsimplelocator'); ?>)</h4>
	<table>
		<tr>
			<th><?php _e('Row Number', 'wpsimplelocator'); ?></th>
			<th><?php _e('Error', 'wpsimplelocator'); ?></th>
		</tr>
	</table>
</div>
<?php endif; // column mapping check 