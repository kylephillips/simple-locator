<?php
$transient = $this->import_repo->transient();

// Check that the columns have been mapped
if ( !isset($transient['columns']) || $transient['complete'] ) : ?>

<div class="error"><p><?php _e('Column data is not yet mapped.', 'wpsimplelocator'); ?></p></div>
<a href="<?php echo admin_url('options-general.php?page=wp_simple_locator&tab=import&step=2'); ?>" class="button"><?php _e('Map Column Data', 'wpsimplelocator'); ?></a>


<?php else : ?>
<h3 class="wpsl-step-title"><?php _e('Step 3: Import and Geocode', 'wpsimplelocator'); ?></h3>

<div class="error wpsl-import-error" style="display:none;"><p></p></div>

<?php if ( !$transient['last_imported'] ) : // New Import ?>
<div class="wpsl-import-indicator-intro">
	<p><strong><?php _e('Total Rows to Import'); ?>:</strong> <?php echo $transient['row_count']; ?> <?php _e('rows from', 'wpsimplelocator'); ?> <?php echo $transient['filename']; ?></p>
	<p><?php _e('Once the import has begun, do not close or refresh the page until complete.', 'wpsimplelocator'); ?></p>
	<p><button class="wpsl-start-import button"><?php _e('Start Import', 'wpsimplelocator'); ?></button></p>
</div>


<?php else : // Continuing Previous Import ?>
<div class="wpsl-import-indicator-intro">
	<p><strong><?php _e('Remaining Rows to Import'); ?>:</strong><br>
		<?php echo $transient['row_count'] - $transient['last_imported'] . ' ' . __('rows from', 'wpsimplelocator') . ' ' . $transient['filename']; ?></p>

	<?php
		/*
		* Display the last row imported if it is available
		*/
		if ( $transient['skip_first'] ) $header_row = $this->getCsvRow(0);
		$last_imported = ( isset($header_row) ) ? $transient['last_imported'] - 1 : $transient['last_imported'];
		$row = $this->getCsvRow($last_imported);
		if ( $row ) : $out = "";
	?>
	<div class="wpsl-last-row-imported">
		<h4><?php echo __('Last Row Imported', 'wpsimplelocator') . ' (' .  date_i18n( 'Y-m-d H:m:s', $transient['last_imported_time'] ) . ')'; ?></h4>
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
	<p><button class="wpsl-start-import button"><?php _e('Continue Import', 'wpsimplelocator'); ?></button></p>
</div>
<?php endif; ?>

<input type="hidden" name="last_imported" value="<?php echo $transient['last_imported']; ?>">

<!-- Progress Indicator -->
<div class="wpsl-import-indicator">
	<p><?php _e('Import is in progress. Closing this page will stop the import.', 'wpsimplelocator'); ?></p>

	<div class="wpsl-import-progress">
		<span class="progress-bar"></span>
		<span class="progress-bar-bg" data-total="<?php echo $transient['row_count']; ?>"></span>
		<p><span class="progress-count">0</span> <?php _e('of', 'wpsimplelocator'); echo ' ' . $transient['row_count']; ?> <?php _e('Rows Imported', 'wpsimplelocator'); ?> (<span class="error-count">0</span> <?php _e('Errors', 'wpsimplelocator'); ?>)</p>
		<p>
			<button class="button wpsl-pause-import"><?php _e('Pause Import', 'wpsimplelocator'); ?></button>
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