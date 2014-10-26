<?php settings_fields( 'wpsimplelocator-map' ); // wpsl_map_styles?>
<tr valign="top">
	<td colspan="2" style="padding:0;">
		<h3><?php _e('Map Styles', 'wpsimplelocator'); ?></h3>
		<p><?php _e('If you have custom Google Maps JSON styles for your map, paste them here. The format should be a javascript array containing the Google Maps style objects. Visit the <a href="http://gmaps-samples-v3.googlecode.com/svn/trunk/styledmaps/wizard/index.html">Google Maps API Style Wizard</a> for an easy to use interface for setting custom styles.', 'wpsimplelocator'); ?></p>
	</td>
</tr>
<tr valign="top">
	<td colspan="2" style="padding:0;">
		<textarea name="wpsl_map_styles" id="wpsl_map_styles" class="widefat" style="height:200px;margin-top:20px;"><?php echo get_option('wpsl_map_styles'); ?></textarea>
	</td>
</tr>