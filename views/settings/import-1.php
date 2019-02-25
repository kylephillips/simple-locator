<h3 class="wpsl-step-title"><?php _e('Step 1: Upload CSV File', 'simple-locator'); ?></h3>

<?php 
// Form Notifications
if ( isset($_GET['success']) ) echo '<div class="updated"><p>' . $_GET['success'] . '</p></div>';
if ( isset($_GET['error']) ) echo '<div class="error"><p>' . $_GET['error'] . '</p></div>';
$all_templates = $this->import_repo->getAllTemplates();
?>

<div class="wpsl-import-general-information">
	<h4><?php _e('Important: File Details and Import Limitations', 'simple-locator'); ?></h4>
	<div class="wpsl-info"><?php _e('Before running an import, make a complete backup of your database.', 'simple-locator'); ?></div>
	<ul>
		<li>
			<h5><?php _e('File Format', 'simple-locator'); ?></h5>
			<p><?php _e('File must be properly formatted CSV', 'simple-locator'); ?>. <a href="<?php echo plugins_url(); ?>/simple-locator/assets/csv_template.csv"><?php _e('View an Example Template', 'simple-locator'); ?></a></p>
		</li>
		<li>
			<h5><?php _e('Required Columns', 'simple-locator'); ?></h5>
			<p><?php _e('2 columns are required: a title and at least one address column. Addresses may be saved across multiple columns (street address, city, etc…), or in one column. You may specify a column as a unique ID to prevent duplicates.'); ?></a></p>
		</li>
		<li>
			<h5><?php _e('Geocoding Pricing', 'simple-locator'); ?></h5>
			<p><?php _e('Effective June 11, 2018, Google charges for each geocoding request. Please review their pricing for Geocoding services ', 'simple-locator'); ?><a href="https://developers.google.com/maps/billing/understanding-cost-of-use?hl=en_US#geocoding" target="_blank"><?php _e('here', 'simple-locator'); ?></a>.</p>
		</li>
	</ul>
</div><!-- .wpsl-import-general-information -->

<div class="wpsl-settings margin-bottom">
	<?php
		$incomplete = $this->import_repo->incomplete();
		if ( $incomplete && !isset($_GET['error']) ) :
		$transient = $this->import_repo->transient();
		if ( isset($transient['filename']) ) :
	?>
	<div class="row wpsl-previous-import-message" data-simple-locator-import-previous-message>
		<div class="wpsl-alert"><?php _e('You have an incomplete import. Would you like to continue the import?', 'simple-locator'); ?></div>
		<p>
			<?php 
				$out = __('File Name', 'simple-locator') . ': ' . $transient['filename']; 
				if ( $transient['mac'] ) $out .= ' <em>(' . __('Mac Formatted', 'simple-locator') . ')</em>';
				$out .= '<br>';
				$out .= __('Total Records', 'simple-locator') . ': ' . $transient['row_count'] . '<br>';
				$out .= __('Completed Records', 'simple-locator') . ': ' . $transient['complete_rows'] . '<br>';
				$out .= __('Import Errors', 'simple-locator') . ': ' . count($transient['error_rows']) . '<br>';
				echo $out;
			?>
		</p>
		<a href="options-general.php?page=wp_simple_locator&amp;tab=import&amp;step=3" class="button">
			<?php _e('Continue Import', 'simple-locator'); ?>
		</a>
		<a href="#" class="button button-primary" data-simple-locator-import-start-new>
			<?php _e('Abort and Start New Import', 'simple-locator'); ?>
		</a>
	</div><!-- .row -->
	<?php endif; endif; ?>

	<form action="<?php echo admin_url('admin-post.php'); ?>" method="post" enctype="multipart/form-data" class="wpsl-upload-form row"<?php if ( $incomplete ) echo ' style="display:none;"';?> data-simple-locator-import-upload-form>
		<?php if ( $all_templates ) : ?>
		<div class="import-type-selection">
			<div class="type-radios">
				<div class="choice">
					<input type="radio" name="import_type" data-simple-locator-import-type-radio value="new" id="import_type_new" checked /><label for="import_type_new"><?php _e('New Import', 'simple-locator'); ?></label>
				</div>
				<div class="choice">
					<input type="radio" name="import_type" data-simple-locator-import-type-radio value="template" id="import_type_template" /><label for="import_type_template"><?php _e('Use Template', 'simple-locator'); ?></label>
				</div>
			</div>
		</div><!-- .import-type-selection -->
		<?php endif; ?>
		<div data-import-type="new">
			<label><?php _e('Import to Post Type', 'simple-locator'); ?></label>
			<select name="import_post_type" data-simple-locator-import-post-type-input>
			<?php 
			foreach ( $this->field_repo->getPostTypes(false) as $type ){
				echo '<option value="' . $type['name'] . '"';
				if ( !$type['public'] ) echo ' data-non-public-post-type';
				echo '>' . $type['label'] . '</option>';
			} ?>
			</select>
			<label class="post-type-public"><input type="checkbox" data-simple-locator-show-non-public-types><?php _e('Show non-public post types', 'simple-locator'); ?></label>
		</div><!-- .import_type_new -->
		<?php if ( $all_templates ) : ?>
		<div data-import-type="template" style="display:none;">
			<label><?php _e('Import Template', 'simple-locator'); ?></label>
			<select name="import_template">
				<?php foreach ( $all_templates as $template ) echo '<option value="' . $template->ID . '">' . $template->title . '</option>'; ?>
			</select>
		</div>
		<?php endif; ?>
		<input type="hidden" name="action" value="wpslimportupload">
		<?php wp_nonce_field( 'wpsl-import-nonce', 'nonce' ) ?>
		
		<div class="file">
			<label><?php _e('Choose CSV File', 'simple-locator'); ?></label>
			<input type="file" name="file">
			<label class="inline"><input type="checkbox" name="mac_formatted" value="true"><?php _e('CSV file created on Mac', 'simple-locator'); ?></label>
		</div>
		<input type="submit" class="button" value="<?php _e('Start New Import', 'simple-locator'); ?>">
	</form>

</div><!-- .wpsl-settings -->


<?php
// Display Previous Imports with options to redo, undo, and remove
$iq = new WP_Query([
	'post_type' => 'wpslimport',
	'posts_per_page' => -1
]);
if ( $iq->have_posts() ) : $c = 1;
?>
<div class="wpsl-settings wpsl-previous-imports">
	<div class="row subhead"><h4><?php _e('Complete Imports', 'simple-locator'); ?></h4></div>
	<?php 
	while ( $iq->have_posts() ) : $iq->the_post(); 
		$data = get_post_meta(get_the_id(), 'wpsl_import_data', true); ?>
		<div class="import<?php if ( $c == 1) echo ' first';?>">
			<div class="import-title">
				<a href="#" class="button" data-import-toggle-details><?php _e('Details', 'simple-locator'); ?></a>
				<h4><?php echo get_the_title(get_the_id()) . ' ' . __('from', 'simple-locator') . ' ' . $data['filename']; ?></h4>
			</div><!-- .import-title -->
			<div class="import-body">
				<div class="import-meta">
					<p>
						<strong><?php _e('File', 'simple-locator'); ?>:</strong> <?php echo $data['filename']; ?><br>
						<strong><?php _e('Total Posts Imported', 'simple-locator'); ?>:</strong> <?php echo $data['complete_rows']; ?><br>
						<strong><?php _e('Post Type', 'simple-locator'); ?>:</strong> <?php echo $data['post_type']; ?><br>
						<strong><?php _e('Errors', 'simple-locator'); ?>:</strong> <?php echo count($data['error_rows']); ?>
					</p>
					<p>
						<?php if ( file_exists($data['file']) ) : ?>
						<a href="#" class="button" data-simple-locator-import-redo-button="<?php echo get_the_id(); ?>">
							<?php _e('Re-Run Import', 'simple-locator'); ?>
						</a>
						<?php 
							else : 
								echo '<p>' . __('The original file has been removed. This import cannot be run again automatically.', 'simple-locator') . '</p>';
							endif; 
						?>
						<a href="#" class="button" data-simple-locator-import-remove-button="<?php echo get_the_id(); ?>">
							<?php _e('Remove Import Record', 'simple-locator'); ?>
						</a>
					</p>
				</div>
				<div class="wpsl-import-save-template">
					<form action="<?php echo admin_url('admin-post.php'); ?>" method="post" data-save-import-template>
						<input type="hidden" name="action" value="wpslsaveimporttemplate">
						<input type="hidden" name="template_id" value="<?php echo get_the_id(); ?>">
						<label for="template_name"><?php _e('Template Name', 'simple-locator'); ?></label>
						<input type="text" name="template_name" id="template_name" value="<?php echo get_the_title(get_the_id()) . ' ' . __('from', 'simple-locator') . ' ' . $data['filename']; ?>" />
						<button class="button" type="submit"><?php _e('Save Import Template', 'simple-locator'); ?></button>
						<?php wp_nonce_field( 'wpsl-import-nonce', 'nonce' ) ?>
					</form>
				</div><!-- .wpsl-import-save-template -->
				<?php if ( count($data['error_rows']) > 0 ) : ?>
				<div class="wpsl-import-details">
				<h4><?php _e('Error Log', 'simple-locator'); ?></h4>
				<table>
					<thead>
						<tr>
							<th><?php _e('Row Number', 'simple-locator'); ?></th>
							<th><?php _e('Error', 'simple-locator'); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php 
						foreach($data['error_rows'] as $row){
							$out = '<tr>';
							$out .= '<td>' . $row['row'] . '</td>';
							$out .= '<td>' . $row['error'] . '</td>';
							$out .= '</tr>';
							echo $out;
						}
						?>
					</tbody>
				</table>
				</div>
				<?php endif; ?>
				<div class="import-footer">
					<?php if ( file_exists($data['file']) ) : ?>
					<p>
						<a href="#" data-simple-locator-import-undo-button="<?php echo get_the_id(); ?>" class="button-danger">
							<?php _e('Undo Import', 'simple-locator'); ?>
						</a>
						<strong><?php _e('Warning', 'simple-locator'); ?></strong>: <?php _e('Undoing an import will erase all post data created during the import.', 'simple-locator'); ?>
					</p>
					<?php endif; ?>
				</div>
			</div><!-- .import-body -->
		</div><!-- .import -->
	<?php $c++; endwhile; ?>

	<form action="<?php echo admin_url('admin-post.php'); ?>" method="post" data-remove-import-form style="display:none;">
		<input type="hidden" name="action" value="wpslremoveimport">
		<input type="hidden" name="remove_import_id" id="remove_import_id">
		<?php wp_nonce_field( 'wpsl-import-nonce', 'nonce' ) ?>
	</form>

	<form action="<?php echo admin_url('admin-post.php'); ?>" method="post" data-redo-import-form style="display:none;">
		<input type="hidden" name="action" value="wpslredoimport">
		<input type="hidden" name="redo_import_id" id="redo_import_id">
		<?php wp_nonce_field( 'wpsl-import-nonce', 'nonce' ) ?>
	</form>

	<form action="<?php echo admin_url('admin-post.php'); ?>" method="post" data-undo-import-form style="display:none;">
		<input type="hidden" name="action" value="wpslundoimport">
		<input type="hidden" name="undo_import_id" id="undo_import_id">
		<?php wp_nonce_field( 'wpsl-import-nonce', 'nonce' ) ?>
	</form>

	<form action="<?php echo admin_url('admin-post.php'); ?>" method="post" data-remove-import-template style="display:none;">
		<input type="hidden" name="action" value="wpslremoveimporttemplate">
		<input type="hidden" name="template_remove_id" id="template_remove_id">
		<?php wp_nonce_field( 'wpsl-import-nonce', 'nonce' ) ?>
	</form>

</div><!-- .wpsl-previous-imports -->


<?php if ( $all_templates ) : ?>
<div class="wpsl-import-template-list wpsl-settings">
	<div class="row subhead"><h4><?php _e('Import Templates', 'simple-locator'); ?></h4></div>
	<?php foreach ( $all_templates as $c => $template ) : ?>
	<div class="row import-template <?php if ( $c == 0 ) echo 'first'; ?>">
		<div class="title"><p><strong><?php echo $template->title; ?></strong> <a href="#" class="button" data-import-template-toggle-details><?php _e('Details', 'simple-locator'); ?></a></p></div>
		<div class="details">
			<p class="remove"><a href="#" data-simple-locator-remove-import-template="<?php echo $template->ID; ?>" class="button-danger"><?php _e('Remove Template', 'simple-locator'); ?></a></p>
			<table class="columns">
				<thead>
					<tr>
						<th><?php _e('CSV Column Number', 'simple-locator'); ?></th>
						<th><?php _e('Import To', 'simple-locator'); ?></th>
						<th><?php _e('Type', 'simple-locator'); ?></th>
						<th><?php _e('Unique ID', 'simple-locator'); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php 
					$has_taxonomy = false;
					$has_unique = false;
					foreach ( $template->import_columns as $column ) : 
					if ( $column->unique ) $has_unique = true;
					if ( $column->field_type == 'taxonomy' ) $has_taxonomy = true;
					?>
					<tr>
						<td><?php echo $column->csv_column + 1; ?></td>
						<td><?php echo $column->field; ?></td>
						<td>
							<?php 
							$type = $column->field_type;
							if ( $type == 'post_meta' ) $type = __('Custom Field', 'simple-locator');
							if ( $type == 'post_field' ) $type = __('Post Field', 'simple-locator');
							if ( $type == 'taxonomy' ) $type = __('Taxonomy ', 'simple-locator');
							echo $type; ?>
						</td>
						<td><?php echo ( $column->unique ) ? '<span class="dashicons dashicons-yes"></span>' : ''; ?></td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			<ul class="summary">
				<li><strong><?php _e('Post Type: ', 'simple-locator'); ?></strong><?php echo $template->import_post_type; ?></li>
				<li><strong><?php _e('Status: ', 'simple-locator'); ?></strong><?php echo $template->import_status; ?></li>
				<li><strong><?php _e('Skip First? ', 'simple-locator'); ?></strong><?php echo ( $template->import_skip_first ) ? __('Yes', 'simple-locator') : __('No', 'simple-locator'); ?></li>
				<li><strong><?php _e('Skip Geocoding? ', 'simple-locator'); ?></strong><?php echo ( $template->import_skip_geocode ) ? __('Yes', 'simple-locator') : __('No', 'simple-locator'); ?></li>
				<?php if ( $has_unique ) : ?>
				<li><strong><?php _e('Duplicate Handling: ', 'simple-locator'); ?></strong><?php echo ucfirst($template->import_duplicate_handling); ?></li>
				<?php endif; ?>
				<?php if ( $has_taxonomy ) : ?>
				<li><strong><?php _e('Taxonomy Separator: ', 'simple-locator'); ?></strong><?php echo ucfirst($template->import_taxonomy_separator); ?></li>
				<?php endif; ?>
			</ul>
		</div><!-- .details -->
	</div><!-- .import-template -->
	<?php endforeach; ?>
</div><!-- .wpsl-import-template-list -->
<?php endif; // templates ?>

<?php endif; wp_reset_postdata(); // Previous Import