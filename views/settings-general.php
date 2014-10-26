<?php settings_fields( 'wpsimplelocator-general' ); ?>
<tr valign="top">
	<td colspan="2" style="padding:0;">
		<h3><?php _e('Google Maps API Key', 'wpsimplelocator'); ?></h3>
		<p><?php _e('While not required, it\'s a good idea to sign up for a Google Maps API key. If your website receives a spike in traffic, this will allow you to monitor the usage of the API and possibly prevent the cutoff of access.', 'wpsimplelocator'); ?> <a href="https://developers.google.com/maps/documentation/javascript/tutorial#api_key" target="_blank"><?php _e('How to obtain an API key', 'wpsimplelocator'); ?></a></p>
	</td>
</tr>
<tr valign="top">
	<th scope="row"><?php _e('API Key', 'wpsimplelocator'); ?></th>
	<td>
		<input type="text" name="wpsl_google_api_key" value="<?php echo get_option('wpsl_google_api_key'); ?>" />
	</td>
</tr>
<tr>
	<td height="1" bgcolor="#ccc" colspan="2" style="padding:0;"></td>
</tr>
<tr valign="top">
	<th scope="row"><?php _e('Measurement Unit', 'wpsimplelocator'); ?></th>
	<td>
		<select name="wpsl_measurement_unit">
			<option value="miles" <?php if ( $this->unit == "miles") echo ' selected'; ?>><?php _e('Miles', 'wpsimplelocator'); ?></option>
			<option value="kilometers" <?php if ( $this->unit == "kilometers") echo ' selected'; ?>><?php _e('Kilometers', 'wpsimplelocator'); ?></option>
		</select>
	</td>
</tr>
<tr valign="top">
	<th scope="row"><?php _e('Custom Map Pin', 'wpsimplelocator'); ?></th>
	<td>
		<div id="map-pin-image-cont" style="float:left;">
			<?php 
			if ( get_option('wpsl_map_pin') ){
				echo '<img src="' . get_option('wpsl_map_pin') . '" id="map-pin-image" />';
			}
			?>
		</div>
		<?php 
		if ( get_option('wpsl_map_pin') ){
			echo '<input id="remove_map_pin" type="button" value="Remove" class="button action" style="margin-right:5px;margin-left:10px;" />';
		} else {
			echo '<input id="upload_image_button" type="button" value="Upload" class="button action" />';
		} ?>
		<input id="wpsl_map_pin" type="text" size="36" name="wpsl_map_pin" value="<?php echo get_option('wpsl_map_pin'); ?>" style="display:none;" />
	</td>
</tr>
<tr valign="top">
	<th scope="row"><?php _e('Output Nested Pages CSS', 'wpsimplelocator'); ?></th>
	<td>
		<label>
			<input type="radio" value="true" name="wpsl_output_css" <?php if ( get_option('wpsl_output_css') == 'true') echo 'checked'; ?> />
			<?php _e('Yes', 'wpsimplelocator'); ?>
		</label><br />
		<label>
			<input type="radio" value="false" name="wpsl_output_css" <?php if ( get_option('wpsl_output_css') !== 'true') echo 'checked'; ?> />
			<?php _e('No', 'wpsimplelocator'); ?>
		</label>
	</td>
</tr>
<script language="JavaScript">
jQuery(function($){
	$(document).ready(function() {
		$(document).on('click', '#upload_image_button', function() {
			formfield = $('#upload_image').attr('name');
			tb_show('', 'media-upload.php?type=image&TB_iframe=true');
			return false;
		});

		window.send_to_editor = function(html) {
			imgurl = $('img',html).attr('src');
			var imagehtml = '<img src="' + imgurl + '" id="map-pin-image" />';
			imagehtml += '<input id="remove_map_pin" type="button" value="Remove" class="button action" style="margin-right:5px;margin-left:10px;" />';
			$('#map-pin-image-cont').append(imagehtml);
			$('#upload_image_button').remove();
			$('#wpsl_map_pin').val(imgurl);
			tb_remove();
		}

	});

	$(document).on('click', '#remove_map_pin', function(e){
		e.preventDefault();
		$('#map-pin-image').remove();
		$('#wpsl_map_pin').prop('value', '');
		$('#map-pin-image-cont').append('<input id="upload_image_button" type="button" value="Upload" class="button action" />');
		$(this).remove();
	});
});
</script>