<div class="simple-locator-import-tab">
	<h3><?php _e('Import Addresses', 'wpsimplelocator'); ?></h3>
	<p><?php _e('The Google Maps Geocoding API limits request to 2500 per 24 hour period & 5 requests per second. If your file contains more than 2500 records, it will not be accepted.', 'wpsimplelocator'); ?></p>
	<form action="<?php echo admin_url('admin-post.php'); ?>" method="post" enctype="multipart/form-data">
		<input type="hidden" name="action" value="wpslimportupload">
		<input type="file" name="file">
		<input type="submit" class="button" value="<?php _e('Upload File', 'wpsimplelocator'); ?>">
	</form>
</div><!-- .simple-locator-import-tab" -->