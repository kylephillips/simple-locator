<?php
/**
* Map Style
* @link http://snazzymaps.com/style/25/blue-water
*/
$map = array(
	'post_title' => __('Blue Water', 'wpsimplelocator'),
	'post_status' => 'publish',
	'post_type' => 'wpslmaps',
	'ping_status' => 'closed',
	'comment_status' => 'closed',
	'post_content' => '[{"featureType":"water","stylers":[{"color":"#46bcec"},{"visibility":"on"}]},{"featureType":"landscape","stylers":[{"color":"#f2f2f2"}]},{"featureType":"road","stylers":[{"saturation":-100},{"lightness":45}]},{"featureType":"road.highway","stylers":[{"visibility":"simplified"}]},{"featureType":"road.arterial","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"administrative","elementType":"labels.text.fill","stylers":[{"color":"#444444"}]},{"featureType":"transit","stylers":[{"visibility":"off"}]},{"featureType":"poi","stylers":[{"visibility":"off"}]}]'
);
$post = wp_insert_post($map);
add_post_meta($post, 'wpsl_map_id', $this->file_id);
?>