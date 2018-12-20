<?php

?>
<form action="<?php echo admin_url('admin-post.php'); ?>" method="post">
<input type="hidden" name="action" value="wpslexport">
<div class="wpsl-settings">
	<div class="row">
		<div class="label"><h4><?php _e('Select Post Columns', 'simple-locator'); ?></h4></div>
		<div class="field">
			<ul class="checkbox-list">
				<?php 
				foreach ( $this->field_repo->getStandardPostColumns() as $key => $field ) :
					$out = '<li><label class="block">' . $field['name'] . '</label><input type="checkbox" name="standard_columns[]" value="' . $key . '"';
					if ( $field['default'] ) $out .= ' checked';
					$out .= ' />';
					$out .= '<div class="column-name"><div class="inner"><label>' . __('Column Name', 'simple-locator') . '</label>';
					$out .= '<input type="text" name="column_name[' . $key . ']" value="' . $field['name'] . '" placeholder="' . $field['name'] . '" /></div></div>';
					$out .= '</li>';
					echo $out;
				endforeach;
				?>
			</ul>
		</div>
	</div>
	<div class="row">
		<div class="label"><h4><?php _e('Select Custom Fields', 'simple-locator'); ?></h4></div>
		<div class="field">
			<ul class="checkbox-list">
				<?php 
				$custom_fields = $this->field_repo->getFieldsForPostType($this->post_type);
				foreach ( $custom_fields as $field ) :
					$out = '<li><label class="block">' . $field . '</label><input type="checkbox" name="custom_columns[]" value="' . $field . '" />';
					$out .= '<div class="column-name"><div class="inner"><label>' . __('Column Name', 'simple-locator') . '</label>';
					$out .= '<input type="text" name="column_name[' . $field . ']" value="' . $field . '" placeholder="' . $field . '" /></div></div>';
					$out .= '</li>';
					echo $out;
				endforeach;
				?>
			</ul>
		</div>
	</div>
	<div class="row">
		<div class="label"><h4><?php _e('Additional Options', 'simple-locator'); ?></h4></div>
		<div class="field">
			<label class="block"><input type="checkbox" name="include_header_row" value="true" checked /><?php _e('Include header row.', 'simple-locator'); ?></label>
		</div>
	</div>
	<div class="row">
		<div class="label"><h4><?php _e('Export CSV', 'simple-locator'); ?></h4></div>
		<div class="field">
			<input type="submit" value="Export" class="button-primary">
		</div>
	</div>
</div><!-- .wpsl-settings -->
</form>