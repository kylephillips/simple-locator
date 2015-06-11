<h3 class="wpsl-step-title"><?php _e('Step 1: Upload CSV File', 'wpsimplelocator'); ?></h3>

<div class="error">
	<p><strong><?php _e('Important', 'wpsimplelocator'); ?>:</strong> <?php _e('Before running an import, make a complete backup of your database.', 'wpsimplelocator'); ?></p>
</div>

<?php 
//var_dump(get_post_meta(12046, 'wpsl_import_data', true));

// Form Errors
if ( isset($_GET['error']) ) echo '<div class="error"><p>' . $_GET['error'] . '</p></div>';
?>

<div class="wpsl-import-instructions">
	<h4><?php _e('File Format', 'wpsimplelocator'); ?></h4>
	<p><?php _e('File must be properly formatted CSV', 'wpsimplelocator'); ?>. <a href="<?php echo plugins_url(); ?>/simple-locator/assets/csv_template.csv"><?php _e('View a Template', 'wpsimplelocator'); ?></a></p>

	<h4><?php _e('Required Columns', 'wpsimplelocator'); ?></h4>
	<p><?php _e('2 columns are required: a title and at least one address column. Addresses may be saved across multiple columns (street address, city, etc…), or in one column.'); ?></p>

	<h4><?php _e('Import Limits', 'wpsimplelocator'); ?></h4>
	<p><?php _e('The Google Maps Geocoding API limits request to 2500 per 24 hour period & 5 requests per second. If your file contains over 2500 records, it may take multiple days to import. If the limit is reached, progress will be saved, and you may continue your import later.', 'wpsimplelocator'); ?></p>

	<h4><?php _e('Latitude & Longitude Data', 'wpsimplelocator'); ?></h4>
	<p><?php _e('Geocoded latitude and longitude values will be saved in the fields selected under the "Post Type & Geocode Fields" tab.', 'wpsimplelocator'); ?></p>
</div>
<?php
$incomplete = false;
$transient = get_transient('wpsl_import_file');
if ( isset($transient['row_count']) ){
	$remaining = $transient['row_count'] - $transient['complete_rows'] - count($transient['error_rows']);
}
if ( isset($remaining) && $remaining > 0 && !isset($_GET['error'])) :
	$incomplete = true;
?>
<div class="wpsl-import-instructions" style="padding-bottom:10px;">
	<h4 style="color:#d54e21;margin-bottom:15px;">
		<?php _e('You have an incomplete import. Would you like to continue the import?', 'wpsimplelocator'); ?>
	</h4>
	<p>
		<?php 
			$out = __('File Name', 'wpsimplelocator') . ': ' . $transient['filename']; 
			if ( $transient['mac'] ) $out .= ' <em>(' . __('Mac Formatted', 'wpsimplelocator') . ')</em>';
			$out .= '<br>';
			$out .= __('Total Records', 'wpsimplelocator') . ': ' . $transient['row_count'] . '<br>';
			$out .= __('Completed Records', 'wpsimplelocator') . ': ' . $transient['complete_rows'] . '<br>';
			$out .= __('Import Errors', 'wpsimplelocator') . ': ' . count($transient['error_rows']) . '<br>';
			$out .= ( isset($transient['last_import_date']) ) ? __('Last Run', 'wpsimplelocator') . ': ' . $transient['last_import_date'] : __('No Imports Yet', 'wpsimplelocator');
			echo $out;
		?>
	</p>
	<a href="options-general.php?page=wp_simple_locator&amp;tab=import&amp;step=3" class="button">
		<?php _e('Continue Import', 'wpsimplelocator'); ?>
	</a>
	<a href="#" class="wpsl-new-import button button-primary">
		<?php _e('New Import', 'wpsimplelocator'); ?>
	</a>
</div>
<?php endif; ?>

<form action="<?php echo admin_url('admin-post.php'); ?>" method="post" enctype="multipart/form-data" class="wpsl-upload-form"<?php if ( $incomplete ) echo ' style="display:none;"';?>>
	<p>
		<?php
		if ( $incomplete ){
			echo '<h4 style="color:#d54e21;margin-bottom:15px;font-size:15px;">' . __('New Import', 'wpsimplelocator') . '</h4>';
		}
		?>
		<h4><?php _e('Import Post Type', 'wpsimplelocator'); ?></h4>
		<select name="import_post_type" style="margin-top: 10px;width:250px;">
		<?php
		foreach ( $this->field_repo->getPostTypes() as $type ){
			echo '<option value="' . $type['name'] . '">' . $type['label'] . '</option>';
		}
		?>
		</select>
	</p>
	<input type="hidden" name="action" value="wpslimportupload">
	<?php wp_nonce_field( 'wpsl-import-nonce', 'nonce' ) ?>
	
	<h4><?php _e('Choose File', 'wpsimplelocator'); ?></h4>
	<input type="file" name="file">
	
	<p style="background-color:#f2f2f2;padding:8px;">
		<label>
			<input type="checkbox" name="mac_formatted" value="true">
			<?php _e('CSV file created on Mac', 'wpsimplelocator'); ?>
		</label>
	</p>
	<input type="submit" class="button" value="<?php _e('Upload File', 'wpsimplelocator'); ?>">
</form>