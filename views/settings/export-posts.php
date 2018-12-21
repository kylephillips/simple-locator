<?php
$templates = get_option('wpsl_export_templates');
if ( $templates ) : ?>
<div class="wpsl-settings">
	<div class="row">
		<div class="label"><h4><?php _e('Load Template', 'simple-locator'); ?></h4></div>
		<div class="field">
			<ul class="export-template-list">
				<?php foreach ( $templates as $key => $template ) : ?>
				<li>
					<p><?php echo $template['name']; ?></p>
					<a href="#" class="button-primary" data-wpsl-export-template-load="<?php echo $key; ?>"><?php _e('Load'); ?></a>
					<a href="#" class="button" data-wpsl-export-template-delete="<?php echo $key; ?>"><?php _e('Delete'); ?></a>
				<?php endforeach; ?>
			</ul>
		</div><!-- .field -->
	</div>
</div>
<?php endif; ?>

<form action="<?php echo admin_url('admin-post.php'); ?>" method="post">
<input type="hidden" name="action" value="wpslexport">
<div class="wpsl-settings">
	<div class="row">
		<div class="label"><h4><?php _e('Select Post Columns', 'simple-locator'); ?></h4></div>
		<div class="field">
			<ul class="checkbox-list min-height">
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
			<ul class="checkbox-list  min-height">
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

	<?php
	$taxonomies = get_object_taxonomies($this->post_type, 'object9s');
	if ( $taxonomies ) :
	?>
	<div class="row">
		<div class="label"><h4><?php _e('Select Taxonomies', 'simple-locator'); ?></h4></div>
		<div class="field">
			<ul class="checkbox-list min-height">
				<?php 
				foreach ( $taxonomies as $taxonomy ) :
					$out = '<li><label class="block">' . $taxonomy->label . '</label><input type="checkbox" name="taxonomies[]" value="' . $taxonomy->name . '" />';
					$out .= '<div class="column-name"><div class="inner"><label>' . __('Column Name', 'simple-locator') . '</label>';
					$out .= '<input type="text" name="column_name[' . $taxonomy->name . ']" value="' . $taxonomy->label . '" placeholder="' . $taxonomy->label . '" /></div></div>';
					$out .= '</li>';
					echo $out;
				endforeach;
				?>
			</ul>
		</div>
	</div>
	<?php endif; ?>

	<div class="row">
		<div class="label"><h4><?php _e('Additional Options', 'simple-locator'); ?></h4></div>
		<div class="field">
			<label class="block"><input type="checkbox" name="include_header_row" value="true" checked /><?php _e('Include header row.', 'simple-locator'); ?></label>
			<p>
				<label class="block"><?php _e('File Name', 'simple-locator'); ?></label>
				<input type="text" name="file_name" value="<?php _e('location-export', 'simple-locator'); ?>" placeholder="<?php _e('location-export', 'simple-locator'); ?>" />
			</p>
			<?php if ( $taxonomies ) : ?>
			<p>
				<label class="block"><?php _e('Taxonomy Term Separator', 'simple-locator'); ?></label>
				<select name="taxonomy_separator" style="width: 100%;"> 
					<option value="comma"><?php _e('Comma', 'simple-locator'); ?></option>
					<option value="pipe"><?php _e('Pipe', 'simple-locator'); ?></option>
				</select>
			</p>
			<?php endif; ?>
		</div>
	</div>
	<div class="row">
		<div class="label"><h4><?php _e('Save as Template', 'simple-locator'); ?></h4></div>
		<div class="field">
			<div class="checkbox-with-input">
				<label class="block"><?php _e('Save as Template.', 'simple-locator'); ?></label>
				<input type="checkbox" name="save_template" value="true" />
				<div class="input">
					<div class="inner">
						<label><?php _e('Template Name', 'simple-locator'); ?></label>
						<input type="text" name="save_template_name" value="" />
					</div>
				</div><!-- .input -->
			</div><!-- .checkbox-with-input -->
		</div><!-- .field -->
	</div>
	<div class="row">
		<div class="label"><h4><?php _e('Export CSV', 'simple-locator'); ?></h4></div>
		<div class="field">
			<input type="submit" value="Export" class="button-primary">
		</div>
	</div>
</div><!-- .wpsl-settings -->
</form>