<h3 class="wpsl-step-title"><?php _e('Step 1: Upload File', 'wpsimplelocator'); ?></h3>

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
	
	<div class="wpsl-import-column-description">
		<h5><strong><?php _e('Required Columns', 'wpsimplelocator'); ?></strong></h5>
		<ul>
			<li><?php _e('Post Title', 'wpsimplelocator'); ?></li>
			<li><?php _e('Street Address', 'wpsimplelocator'); ?></li>
			<li><?php _e('City', 'wpsimplelocator'); ?></li>
			<li><?php _e('State/Province', 'wpsimplelocator'); ?></li>
		</ul>
	</div>

	<div class="wpsl-import-column-description right">
		<h5><strong><?php _e('Optional Columns', 'wpsimplelocator'); ?></strong></h5>
		<ul>
			<li><?php _e('Zip/Postal Code', 'wpsimplelocator'); ?></li>
			<li><?php _e('Telephone', 'wpsimplelocator'); ?></li>
			<li><?php _e('Additional Information', 'wpsimplelocator'); ?></li>
			<li><?php _e('Post Content', 'wpsimplelocator'); ?></li>
		</ul>
	</div>

	<h4 style="clear:both;"><?php _e('Post Type', 'wpsimplelocator'); ?></h4>
	<p><?php _e('Data will be imported to the included "locations" post type. If you have set another custom post type in the plugin settings, the imported posts will not be visible or available. To reset the post type, select the "Post Type & Geocode Fields" tab and click the "Reset to Default" button.', 'wpsimplelocator'); ?></p>

	<h4><?php _e('Additional Information', 'wpsimplelocator'); ?></h4>
	<p><?php _e('The Google Maps Geocoding API limits request to 2500 per 24 hour period & 5 requests per second. If your file contains over 2500 records, it may take multiple days to import. If the limit is reached, progress will be saved, and you may continue your import at any time.', 'wpsimplelocator'); ?></p>
</div>

<form action="<?php echo admin_url('admin-post.php'); ?>" method="post" enctype="multipart/form-data" class="wpsl-upload-form">
	<input type="hidden" name="action" value="wpslimportupload">
	<input type="hidden" name="nonce" value="<?php echo wp_create_nonce('wpsl-import-nonce'); ?>">
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