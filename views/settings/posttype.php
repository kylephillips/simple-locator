<?php settings_fields( 'wpsimplelocator-posttype' ); ?>
<div class="wpsl-settings">
	<div class="row subhead"><h4><?php _e('Post Type', 'simple-locator'); ?></h4></div>

	<div class="row">
		<div class="label align-top">
			<h4><?php _e('Location Post Type', 'simple-locator'); ?></h4>
			<p><?php _e('Simple Locator comes with a post type of "Locations" with all the fields you need. If you\'d like to use a custom post type, select that post type. If you have existing fields for latitude and longitude, select those.', 'simple-locator'); ?></p>
		</div>
		<div class="field align-top">
			<label for="wpsl_post_type" class="block" style="display:none;"><?php _e('Select Post Type', 'simple-locator'); ?></label>
			<select name="wpsl_post_type" id="wpsl_post_type" style="width:100%;max-width:540px;" data-simple-locator-post-type-field>
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
		</div>
	</div><!-- .row -->

	<div class="row">
		<div class="label">
			<h4><?php _e('Hide Included Post Type', 'simple-locator'); ?></h4>
			<p><?php _e('You have selected a post type other than the included Simple Locator "Locations" type. To disable that type, select this option.', 'simple-locator'); ?></p>
		</div>
		<div class="field">
			<label for="wpsl_hide_default">
				<input type="checkbox" name="wpsl_hide_default" value="true" id="wpsl_hide_default" <?php if ( get_option('wpsl_hide_default') == 'true') echo 'checked'; ?> data-simple-locator-hide-post-type />
				<?php _e('Hide Simple Locator Post Type', 'simple-locator'); ?>
			</label>
		</div>
	</div>

	<div class="row subhead"><h4><?php _e('Geolocation Custom Fields', 'simple-locator'); ?></h4></div>

	<div class="row">
		<div class="label align-top">
			<h4><?php _e('Latitude & Longitude Fields', 'simple-locator'); ?></h4>
			<p><?php _e('Simple Locator includes a custom meta section with fields that automatically store latitude and longitude once a post has been saved with address information. If your database includes other meta fields that store this information, select them.', 'simple-locator'); ?></p>
			<?php 
			if ( class_exists('acf_field_google_map') ) : 
				$map_fields = $this->field_repo->getAcfMapFields();
				echo '<p><strong>' . __('Advanced Custom Fields Users:', 'simple-locator') . '</strong> ' . __('You may specify an ACF map field to find your locations. If you select a map field, you must add 2 new text fields to store latitude and longitude and select them below. If you do not see your fields, save a post with values for them to appear.', 'simple-locator') . '</p>';
			endif;
			?>
		</div>
		<div class="field align-top">
			<p class="no-margin">
				<label for="field_wpsl" class="wpsl-field-type">
				<input type="radio" name="wpsl_field_type" id="field_wpsl" value="wpsl" <?php if ( $this->field_type == 'wpsl' ) echo ' checked'; ?> data-simple-locator-use-included-fields><?php _e('Use Simple Locator Fields', 'simple-locator'); ?></label>
			</p>
			<p class="no-margin">
				<label for="field_custom" class="wpsl-field-type">
				<input type="radio" name="wpsl_field_type" id="field_custom" value="custom" <?php if ( $this->field_type == 'custom' ) echo ' checked'; ?> data-simple-locator-use-custom-fields><?php _e('Use Other Custom Fields', 'simple-locator'); ?></label>
			</p>
			<p>
				<label for="wpsl_hide_default_fields">
				<input type="checkbox" name="wpsl_hide_default_fields" value="true" id="wpsl_hide_default_fields" <?php if ( get_option('wpsl_hide_default_fields') == 'true') echo 'checked'; ?> data-simple-locator-hide-included-fields /><?php _e('Hide Included Location Fields', 'simple-locator'); ?></label>
			</p>

			<?php 
			// ACF Map Fields
			if ( class_exists('acf_field_google_map') && !empty($map_fields) ) : 
			?>
			<div class="wpsl-acf-map-setting" data-simple-locator-acf-map-field>
				<label><?php _e('Select an optional ACF map field', 'simple-locator'); ?></label>
				<select name="wpsl_acf_map_field">
					<option value=""><?php _e('None', 'simple-locator'); ?></option>
					<?php 
						foreach ( $map_fields as $key => $label ){
							$out = '<option value="' . $key . '"';
							if ( $key == $this->settings_repo->acfMapField() ) $out .= ' selected';
							$out .= '>' . $label . '</option>';
							echo $out;
						}
					?>
				</select>
			</div>
			<?php endif; ?>

			<div class="latlng" data-simple-locator-lat-lng-options>
				<label class="wpsl-show-hidden">
					<input type="checkbox" name="wpsl_show_hidden" value="true" id="wpsl_show_hidden" <?php if ( get_option('wpsl_show_hidden') == 'true' ) echo 'checked'; ?> data-simple-locator-show-hidden> 
					<?php _e('Show Hidden Fields', 'simple-locator'); ?>
				</label>

				<div class="wpsl-left-field">
					<label class="wpsl-block-label"><?php _e('Latitude Field', 'simple-locator'); ?></label>
					<select id="lat_select" data-simple-locator-latitude-select></select>
				</div>

				<div class="wpsl-right-field">
					<label class="wpsl-block-label"><?php _e('Longitude Field', 'simple-locator'); ?></label>
					<select id="lng_select" data-simple-locator-longitude-select></select>
				</div>

				<p class="wpsl-degree-info"><?php _e('Latitude and Longitude fields must be separate fields. Entries must be formatted in decimal degree format (not DMS format).', 'simple-locator'); ?></p>

				<input type="hidden" id="wpsl_lat_field" name="wpsl_lat_field" 
				value="<?php echo ( get_option('wpsl_lat_field') ) ? get_option('wpsl_lat_field') : 'wpsl_latitude'; ?>"  data-simple-locator-latitude-field />

				<input type="hidden" id="wpsl_lng_field" name="wpsl_lng_field"
				value="<?php echo ( get_option('wpsl_lng_field') ) ? get_option('wpsl_lng_field') : 'wpsl_longitude'; ?>"  data-simple-locator-longitude-field />
					
			</div><!-- .latlng -->
		</div><!-- .field -->
	</div><!-- .row -->

	<div data-simple-locator-post-type-labels>
	<div class="row subhead"><h4><?php _e('Location Post Type Options', 'simple-locator'); ?></h4></div>

	<div class="row">
		<div class="label align-top">
			<p><strong class="wpsl-red"><?php _e('Important:', 'simple-locator'); ?></strong> <?php _e('Changing the name or slug will remove content already published under existing post type from view. For more information, visit', 'simple-locator'); ?><a href="http://codex.wordpress.org/Function_Reference/register_post_type" target="_blank"> wordpress.org</a>. <?php _e('If you change settings inadvertently and lose access to your location entries, you can reset to the plugin defaults using the "Reset to Default" button above.', 'simple-locator'); ?></p>
			<p style="font-style:oblique">*<?php _e('Post type & slug must be all lower case, with no special characters or spaces.', 'simple-locator');?></p>
		</div>
		<div class="field no-padding align-top">
			<div class="wpsl-posttype-settings-labels">
				<div class="post-type-row">
					<label><?php _e('Post Type', 'simple-locator');?>*</label>
					<div class="post-type-field">
						<input type="text" name="wpsl_posttype_labels[name]" value="<?php echo $this->getLabel('name', 'location'); ?>">
					</div>
				</div>
				<div class="post-type-row">
					<label><?php _e('Post Type Label', 'simple-locator');?></label>
					<div class="post-type-field">
						<input type="text" name="wpsl_posttype_labels[label]" value="<?php echo $this->getLabel('label', 'Locations'); ?>">
					</div>
				</div>
				<div class="post-type-row">
					<label><?php _e('Post Type Singular Label', 'simple-locator');?></label>
					<div class="post-type-field">
						<input type="text" name="wpsl_posttype_labels[singular]" value="<?php echo $this->getLabel('singular', 'Location'); ?>">
					</div>
				</div>
				<div class="post-type-row">
					<label><?php _e('Add New Label', 'simple-locator');?></label>
					<div class="post-type-field">
						<input type="text" name="wpsl_posttype_labels[add_new_item]" value="<?php echo $this->getLabel('add_new_item', 'New Location'); ?>">
					</div>
				</div>
				<div class="post-type-row">
					<label><?php _e('Edit Label', 'simple-locator');?></label>
					<div class="post-type-field">
						<input type="text" name="wpsl_posttype_labels[edit_item]" value="<?php echo $this->getLabel('edit_item', 'Edit Location'); ?>">
					</div>
				</div>
				<div class="post-type-row">
					<label><?php _e('View Label', 'simple-locator');?></label>
					<div class="post-type-field">
						<input type="text" name="wpsl_posttype_labels[view_item]" value="<?php echo $this->getLabel('view_item', 'View Location'); ?>">
					</div>
				</div>
				<div class="post-type-row">
					<label><?php _e('Search Label', 'simple-locator');?></label>
					<div class="post-type-field">
						<input type="text" name="wpsl_posttype_labels[search_item]" value="<?php echo $this->getLabel('search_item', 'Search Locations'); ?>">
					</div>
				</div>
				<div class="post-type-row">
					<label><?php _e('Slug', 'simple-locator');?>*</label>
					<div class="post-type-field">
						<input type="text" name="wpsl_posttype_labels[slug]" value="<?php echo $this->getLabel('slug', 'location'); ?>">
					</div>
				</div>
				<div class="post-type-row">
					<label><?php _e('Menu Icon', 'simple-locator');?></label>
					<div class="post-type-field">
						<input type="text" name="wpsl_posttype_labels[menu_icon]" value="<?php echo $this->getLabel('menu_icon', 'dashicons-post-status'); ?>">
					</div>
				</div>
				<div class="post-type-row">
					<label><?php _e('Menu Position', 'simple-locator');?></label>
					<div class="post-type-field">
						<input type="text" name="wpsl_posttype_labels[menu_position]" value="<?php echo $this->getLabel('menu_position', '6'); ?>">
					</div>
				</div>
			</div><!-- .wpsl-posttype-settings-labels -->
		</div><!-- .field -->
	</div><!-- .row -->

	</div><!-- .post type labels -->

	<?php 
	if ( class_exists('acf_field_tab') ) : 
	$tab_fields = $this->field_repo->getAcfTabFields();
	if ( $tab_fields && is_array($tab_fields) && !empty($tab_fields) ) :
	?>
	<div class="row subhead"><h4><?php _e('ACF Location Fields Placement', 'simple-locator'); ?></h4></div>
	<div class="row">
		<div class="label">
			<h4><?php _e('Organize Fields Into ACF Tab', 'simple-locator'); ?></h4>
			<?php
				echo '<p><strong>' . __('Advanced Custom Fields Users:', 'simple-locator') . '</strong> ' . __('You may select a tab to place the included location fields under if any tabs are available.', 'simple-locator') . '</p>';
			?>
		</div>
		<div class="field">
			<p><label for="wpsl_acf_tab"><?php _e('Select a tab to place location fields in.', 'simple-locator'); ?></label></p>
			<select name="wpsl_acf_tab" id="wpsl_acf_tab" style="width:100%;max-width:540px;" >
				<option value=""><?php _e('None', 'simple-locator'); ?></option>
				<?php 
				$selected = $this->settings_repo->acfTab();
				foreach ( $tab_fields as $key => $field ) {
					$out = '<option value="' . $key . '"';
					if ( $key == $selected ) $out .= ' selected';
					$out .= '>' . $field . '</option>';
					echo $out;
				}
				?>
			</select>
		</div>
	</div><!-- .row -->
	<?php endif; endif; ?>

	<div class="row subhead"><h4><?php _e('Reset Settings', 'simple-locator'); ?></h4></div>
	<div class="row">
		<div class="label">
			<h4><?php _e('Reset Post Type Settings', 'simple-locator'); ?></h4>
			<p><?php _e('This will reset the post type and location fields to the defaults included with the plugin.', 'simple-locator'); ?></p>
		</div>
		<div class="field">
			<p class="no-margin"><label><input type="checkbox" data-simple-locator-resest-post-type-checkbox><?php _e('Reset to Default Post Type Settings', 'simple-locator'); ?></label></p>
			<button class="button-danger wpsl-reset-posttype" data-simple-locator-reset-post-type style="display:none;"><?php _e('Reset to Default', 'simple-locator'); ?></button>
		</div>
	</div>
</div><!-- .wpsl-settings -->