<h3 class="wpsl-step-title"><?php _e('Step 1: Upload File', 'wpsimplelocator'); ?></h3>

<?php 
// Form Errors
if ( isset($_GET['error']) ) echo '<div class="error"><p>' . $_GET['error'] . '</p></div>';
?>

<div class="wpsl-import-instructions">
	<h4><?php _e('File Format', 'wpsimplelocator'); ?></h4>
	<p><?php _e('File must be properly formatted CSV', 'wpsimplelocator'); ?>. <a href="<?php echo plugins_url(); ?>/simple-locator/assets/csv_template.csv"><?php _e('View a Template', 'wpsimplelocator'); ?></a></p>
	
	<div class="wpsl-import-column-description">
		<h5><strong><?php _e('Required Columns', 'wpsimplelocator'); ?></strong></h5>
		<ul>
			<li><?php _e('Street Address', 'wpsimplelocator'); ?></li>
			<li><?php _e('City', 'wpsimplelocator'); ?></li>
			<li><?php _e('State/Province', 'wpsimplelocator'); ?></li>
			<li><?php _e('Zip/Postal Code', 'wpsimplelocator'); ?></li>
		</ul>
	</div>

	<div class="wpsl-import-column-description right">
		<h5><strong><?php _e('Optional Columns', 'wpsimplelocator'); ?></strong></h5>
		<ul>
			<li><?php _e('Telephone', 'wpsimplelocator'); ?></li>
			<li><?php _e('Additional Information', 'wpsimplelocator'); ?></li>
			<li><?php _e('Post Content', 'wpsimplelocator'); ?></li>
		</ul>
	</div>

	<h4 style="clear:both;"><?php _e('Post Type', 'wpsimplelocator'); ?></h4>
	<p><?php _e('Imported data will be posted to the included "locations" post type. If you have set another custom post type in the plugin settings, the imported data will not be visible or available. To reset the post type settings, select the "Post Type & Geocode Fields" tab and click the "Reset to Default" button.', 'wpsimplelocator'); ?></p>

	<h4><?php _e('Additional Information', 'wpsimplelocator'); ?></h4>
	<p><?php _e('The Google Maps Geocoding API limits request to 2500 per 24 hour period & 5 requests per second. Only the first 2500 records will be read from the uploaded.', 'wpsimplelocator'); ?></p>
</div>

<form action="<?php echo admin_url('admin-post.php'); ?>" method="post" enctype="multipart/form-data" class="wpsl-upload-form">
	<input type="hidden" name="action" value="wpslimportupload">
	<h4><?php _e('Choose File', 'wpsimplelocator'); ?></h4>
	<p>
		<input type="file" name="file">
	</p>
	<p>
		<label>
			<input type="checkbox" name="mac_formatted" value="true">
			<?php _e('CSV file created on Mac', 'wpsimplelocator'); ?>
		</label>
	</p>
	<input type="submit" class="button" value="<?php _e('Upload File', 'wpsimplelocator'); ?>">
</form>