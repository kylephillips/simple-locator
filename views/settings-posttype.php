<?php settings_fields( 'wpsimplelocator-posttype' ); ?>
<tr valign="top">
	<td colspan="2" style="padding:0;">
		<h3><?php _e('Location Post Type', 'wpsimplelocator'); ?></h3>
		<p><?php _e('Simple Locator comes with a post type of "Locations" with all the fields you need. If you\'d like to use a custom post type, select it below. If you have existing fields for latitude and longitude, select those.', 'wpsimplelocator'); ?></p>
	</td>
</tr>
<tr valign="top">
	<th scope="row"><?php _e('Post Type for locations', 'wpsimplelocator'); ?></th>
	<td>
		<label style="display:block;margin-bottom:5px;">
			<input type="checkbox" name="wpsl_show_hidden" value="true" id="wpsl_show_hidden" <?php if ( get_option('wpsl_show_hidden') == 'true' ) echo 'checked'; ?>> 
			<?php _e('Show Hidden Fields', 'wpsimplelocator'); ?>
		</label>
		
		<select name="wpsl_post_type" id="wpsl_post_type">
		<?php
		foreach ( $this->field_repo->getPostTypes() as $type ){
			$out = '<option value="' . $type['name'] . '"';
			if ( $type['name'] == $this->post_type ) $out .= ' selected';
			$out .= '>';
			$out .= ( $type['name'] == $this->getLabel('name', 'location') ) ? $this->getLabel('label', 'Locations') : $type['label'];
			$out .= '</option>';
			echo $out;
		}
		?>
		</select>
	</td>
</tr>
<tr valign="top">
	<th scope="row"><?php _e('Latitude & Longitude Fields', 'wpsimplelocator'); ?></th>
	<td>
		<p>
			<label for="field_wpsl" class="wpsl-field-type">
				<input type="radio" name="wpsl_field_type" id="field_wpsl" value="wpsl" <?php if ( $this->field_type == 'wpsl' ) echo ' checked'; ?>>
				<?php _e('Use Simple Locator Fields', 'wpsimplelocator'); ?>
			</label>
		</p>
		<p>
			<label for="field_custom" class="wpsl-field-type">
				<input type="radio" name="wpsl_field_type" id="field_custom" value="custom" <?php if ( $this->field_type == 'custom' ) echo ' checked'; ?>>
				<?php _e('Use Custom Fields', 'wpsimplelocator'); ?>
			</label>
		</p>
	</td>
</tr>
<tr valign="top" class="latlng">
	<th scope="row"><?php _e('Latitude Field', 'wpsimplelocator'); ?></th>
	<td>
		<select id="lat_select">';
			<?php 
				$show_hidden = ( get_option('wpsl_show_hidden') == 'true' ) ? true : false;
				$fields = $this->field_repo->getFieldsForPostType($this->post_type, $show_hidden);
				foreach ( $fields as $field ){
					$out = '<option value="' . $field . '"';
					if ( $field == get_option('wpsl_lat_field') ) $out .= ' selected';
					$out .= '>' . $field . '</option>';
					echo $out;
				}
			?>
		</select>
	</td>
</tr>
<tr valign="top" class="latlng">
	<th scope="row"><?php _e('Longitude Field', 'wpsimplelocator'); ?></th>
	<td>
		<select id="lng_select">
			<?php 
				$fields = $this->field_repo->getFieldsForPostType($this->post_type, $show_hidden);
				foreach ( $fields as $field ){
					$out = '<option value="' . $field . '"';
					if ( $field == get_option('wpsl_lng_field') ) $out .= ' selected';
					$out .= '>' . $field . '</option>';
					echo $out;
				}
			?>
		</select>

		<input type="hidden" id="wpsl_lat_field" name="wpsl_lat_field" 
		value="<?php echo ( get_option('wpsl_lat_field') ) ? get_option('wpsl_lat_field') : 'wpsl_latitude'; ?>" />

		<input type="hidden" id="wpsl_lng_field" name="wpsl_lng_field"
		value="<?php echo ( get_option('wpsl_lng_field') ) ? get_option('wpsl_lng_field') : 'wpsl_longitude'; ?>" />
	</td>
</tr>
</table>
<div class="wpsl-posttype-fields wpsl-label-row">
	<h3><?php _e('Location Post Type Name & Labels', 'wpsimplelocator'); ?></h3>
	<p><?php _e('Important: Changing the name or slug will remove content already published under existing post type from view. For more information, visit', 'wpsimplelocator'); ?><a href="http://codex.wordpress.org/Function_Reference/register_post_type" target="_blank"> wordpress.org</a></p>
	<table class="form-table">
	<tr valign="top" class="wpsl-label-row">
		<th><?php _e('Post Type', 'wpsimplelocator');?>*</th>
		<td>
			<input type="text" name="wpsl_posttype_labels[name]" value="<?php echo $this->getLabel('name', 'location'); ?>">
		</td>
	</tr>
	<tr valign="top" class="wpsl-label-row">
		<th><?php _e('Post Type Label', 'wpsimplelocator');?></th>
		<td>
			<input type="text" name="wpsl_posttype_labels[label]" value="<?php echo $this->getLabel('label', 'Locations'); ?>">
		</td>
	</tr>
	<tr valign="top" class="wpsl-label-row">
		<th><?php _e('Post Type Singular Label', 'wpsimplelocator');?></th>
		<td>
			<input type="text" name="wpsl_posttype_labels[singular]" value="<?php echo $this->getLabel('singular', 'Location'); ?>">
		</td>
	</tr>
	<tr valign="top" class="wpsl-label-row">
		<th><?php _e('Add New Label', 'wpsimplelocator');?></th>
		<td>
			<input type="text" name="wpsl_posttype_labels[add_new_item]" value="<?php echo $this->getLabel('add_new_item', 'New Location'); ?>">
		</td>
	</tr>
	<tr valign="top" class="wpsl-label-row">
		<th><?php _e('Edit Label', 'wpsimplelocator');?></th>
		<td>
			<input type="text" name="wpsl_posttype_labels[edit_item]" value="<?php echo $this->getLabel('edit_item', 'Edit Location'); ?>">
		</td>
	</tr>
	<tr valign="top" class="wpsl-label-row">
		<th><?php _e('View Label', 'wpsimplelocator');?></th>
		<td>
			<input type="text" name="wpsl_posttype_labels[view_item]" value="<?php echo $this->getLabel('view_item', 'View Location'); ?>">
		</td>
	</tr>
	<tr valign="top" class="wpsl-label-row">
		<th><?php _e('Slug', 'wpsimplelocator');?>*</th>
		<td>
			<input type="text" name="wpsl_posttype_labels[slug]" value="<?php echo $this->getLabel('slug', 'location'); ?>">
		</td>
	</tr>
	<tr valign="top" class="wpsl-label-row">
		<th><?php _e('Menu Icon', 'wpsimplelocator');?></th>
		<td>
			<input type="text" name="wpsl_posttype_labels[menu_icon]" value="<?php echo $this->getLabel('menu_icon', 'dashicons-post-status'); ?>">
		</td>
	</tr>
	<tr valign="top" class="wpsl-label-row">
		<th><?php _e('Menu Position', 'wpsimplelocator');?></th>
		<td>
			<input type="text" name="wpsl_posttype_labels[menu_position]" value="<?php echo $this->getLabel('menu_position', '6'); ?>">
		</td>
	</tr>
	<tr valign="top" class="wpsl-label-row">
		<td colspan="2" style="padding:0;">
			
		</td>
	</tr>
	</table>
	<p style="font-style:oblique">*<?php _e('Post type & slug must be all lower case, with no special characters or spaces.', 'wpsimplelocator');?></p>
</div>
<table class="form-table">



