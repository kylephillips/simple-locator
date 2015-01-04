<?php
/**
* Map Style
* @link http://snazzymaps.com/style/15/subtle-grayscale
*/
$map = array(
	'post_title' => __('Subtle Gray', 'wpsimplelocator'),
	'post_status' => 'publish',
	'post_type' => 'wpslmaps',
	'ping_status' => 'closed',
	'comment_status' => 'closed',
	'post_content' => '[{"featureType":"landscape","stylers":[{"saturation":-100},{"lightness":65},{"visibility":"on"}]},{"featureType":"poi","stylers":[{"saturation":-100},{"lightness":51},{"visibility":"simplified"}]},{"featureType":"road.highway","stylers":[{"saturation":-100},{"visibility":"simplified"}]},{"featureType":"road.arterial","stylers":[{"saturation":-100},{"lightness":30},{"visibility":"on"}]},{"featureType":"road.local","stylers":[{"saturation":-100},{"lightness":40},{"visibility":"on"}]},{"featureType":"transit","stylers":[{"saturation":-100},{"visibility":"simplified"}]},{"featureType":"administrative.province","stylers":[{"visibility":"off"}]},{"featureType":"water","elementType":"labels","stylers":[{"visibility":"on"},{"lightness":-25},{"saturation":-100}]},{"featureType":"water","elementType":"geometry","stylers":[{"hue":"#ffff00"},{"lightness":-25},{"saturation":-97}]}]'
);
$post = wp_insert_post($map);
add_post_meta($post, 'wpsl_map_id', $this->file_id);
?>