<h3 class="wpsl-step-title"><?php _e('Step 1: Upload CSV File', 'wpsimplelocator'); ?></h3>

<div class="error">
	<p><strong><?php _e('Important', 'wpsimplelocator'); ?>:</strong> <?php _e('Before running an import, make a complete backup of your database.', 'wpsimplelocator'); ?></p>
</div>

<?php 
// Form Errors
if ( isset($_GET['error']) ) echo '<div class="error"><p>' . $_GET['error'] . '</p></div>';
?>

<div class="wpsl-import-instructions">
	<h4><?php _e('File Format', 'wpsimplelocator'); ?></h4>
	<p><?php _e('File must be properly formatted CSV', 'wpsimplelocator'); ?>. <a href="<?php echo plugins_url(); ?>/simple-locator/assets/csv_template.csv"><?php _e('View a Template', 'wpsimplelocator'); ?></a></p>

	<h4><?php _e('Required Columns', 'wpsimplelocator'); ?></h4>
	<p><?php _e('2 columns are rerquired for import: a title and at least one address column. Addresses may be saved across multiple columns (street address, city, etc…), or in one column.'); ?></p>

	<h4><?php _e('Import Limits', 'wpsimplelocator'); ?></h4>
	<p><?php _e('The Google Maps Geocoding API limits request to 2500 per 24 hour period & 5 requests per second. If your file contains over 2500 records, it may take multiple days to import. If the limit is reached, progress will be saved, and you may continue your import later.', 'wpsimplelocator'); ?></p>

	<h4><?php _e('Latitude & Longitude Data', 'wpsimplelocator'); ?></h4>
	<p><?php _e('Geocoded latitude and longitude values will be saved in the fields selected under the "Post Type & Geocode Fields" tab.', 'wpsimplelocator'); ?></p>
</div>

<form action="<?php echo admin_url('admin-post.php'); ?>" method="post" enctype="multipart/form-data" class="wpsl-upload-form">
	<p>
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