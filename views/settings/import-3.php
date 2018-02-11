<?php
$transient = $this->import_repo->transient();

// Check that the columns have been mapped
if ( !isset($transient['columns']) || $transient['complete'] ) : ?>

<div class="error"><p><?php _e('Column data is not yet mapped.', 'simple-locator'); ?></p></div>
<a href="<?php echo admin_url('options-general.php?page=wp_simple_locator&tab=import&step=2'); ?>" class="button"><?php _e('Map Column Data', 'simple-locator'); ?></a>


<?php else : ?>
<h3 class="wpsl-step-title"><?php _e('Step 3: Import and Geocode', 'simple-locator'); ?></h3>

<div class="error wpsl-import-error" style="display:none;"><p></p></div>

<?php if ( !$transient['last_imported'] ) : // New Import ?>

<div class="wpsl-settings wpsl-import-indicator-intro">
	<div class="row subhead"><h4><?php _e('Import Ready', 'simple-locator'); ?></h4></div>
	<div class="row">
		<div class="label">
			<h4><?php _e('Row Count', 'simple-locator'); ?></h4>
		</div>
		<div class="field">
			<?php echo $transient['row_count']; ?> <?php _e('rows from', 'simple-locator'); ?> <?php echo $transient['filename']; ?>
		</div>
	</div>
	<div class="row">
		<div class="label">
			<h4><?php _e('Start Import', 'simple-locator'); ?></h4>
			<p><?php _e('Once the import has begun, do not close or refresh the page until complete.', 'simple-locator'); ?></p>
		</div>
		<div class="field">
			<button class="wpsl-start-import button button-primary">
				<?php _e('Start Import', 'simple-locator'); ?>
			</button>
		</div>
	</div>
</div><!-- .wpsl-settings -->

<?php else : // Continuing Previous Import ?>
<div class="wpsl-import-indicator-intro">
	<p><strong><?php _e('Remaining Rows to Import'); ?>:</strong><br>
		<?php echo $transient['row_count'] - $transient['last_imported'] . ' ' . __('rows from', 'simple-locator') . ' ' . $transient['filename']; ?></p>

	<?php
		/*
		* Display the last row imported if it is available
		*/
		if ( $transient['skip_first'] ) $header_row = $this->getCsvRow(0);
		$last_imported = ( isset($header_row) ) ? $transient['last_imported'] - 1 : $transient['last_imported'];
		$row = $this->getCsvRow($last_imported);
		if ( isset($header_row) && $transient['last_imported'] == 1 ) $row = false;
		if ( $row ) : $out = "";
	?>
	<div class="wpsl-last-row-imported">
		<h4><?php echo __('Last Row Imported', 'simple-locator') . ' (' .  date_i18n( 'Y-m-d H:m:s', $transient['last_imported_time'] ) . ')'; ?></h4>
		<table class="">
		<?php 
			if ( isset($header_row) ){
				$out .= '<thead><tr>';
				foreach( $header_row as $th ){
					$out .= '<th>' . $th . '</th>';
				}
				$out .= '</tr></thead>';
			}
			$out .= '<tbody><tr>';
			foreach( $row as $column ){
				if ( $column == "" ) $column = '&nbsp;';
				$out .= '<td>' . $column . '</td>';
			}
			$out .= '</tr></tbody></table>';
			echo $out;
		?>
	</div>
	<?php endif; ?>
	<p><button class="wpsl-start-import button"><?php _e('Continue Import', 'simple-locator'); ?></button></p>
</div>
<?php endif; ?>

<input type="hidden" name="last_imported" value="<?php echo $transient['last_imported']; ?>">

<!-- Progress Indicator -->
<div class="wpsl-import-indicator">
	<div class="wpsl-alert"><?php _e('Import is in progress. Closing this page will stop the import.', 'simple-locator'); ?></div>

	<div class="wpsl-import-progress">
		<span class="progress-bar"></span>
		<span class="progress-bar-bg" data-total="<?php echo $transient['row_count']; ?>"></span>
		<p><span class="progress-count">0</span> <?php _e('of', 'simple-locator'); echo ' ' . $transient['row_count']; ?> <?php _e('Rows Imported', 'simple-locator'); ?> (<span class="error-count">0</span> <?php _e('Errors', 'simple-locator'); ?>)</p>
		<p>
			<button class="button wpsl-pause-import"><?php _e('Pause Import', 'simple-locator'); ?></button>
		</p>
	</div>
</div>

<!-- Import Complete Alert -->
<div class="wpsl-import-complete updated" style="display:none;">
	<p><?php _e('The import is complete.', 'simple-locator'); ?> <span class="progress-count">0</span> <?php _e('of', 'simple-locator'); echo ' ' . $transient['row_count']; ?> <?php _e('Rows Imported', 'simple-locator'); ?> (<span class="error-count">0</span> <?php _e('Errors', 'simple-locator'); ?>)</p>
</div>


<!-- Import Details Display after import is complete -->
<div class="wpsl-import-details" style="display:none;">
	<p><strong><?php _e('Total Posts Imported:', 'simple-locator'); ?> <span class="wpsl-total-import-count"></span></strong></p>
	<h4><?php _e('Error Log', 'simple-locator'); ?> (<span class="wpsl-total-error-count"></span> <?php _e('Errors', 'simple-locator'); ?>)</h4>
	<table>
		<tr>
			<th><?php _e('Row Number', 'simple-locator'); ?></th>
			<th><?php _e('Error', 'simple-locator'); ?></th>
		</tr>
	</table>
</div>
<?php endif; // column mapping check 