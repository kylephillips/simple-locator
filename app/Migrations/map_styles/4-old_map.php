<?php
/**
* Map Style
* @link http://snazzymaps.com/style/99/old-map
*/
$map = array(
	'post_title' => __('Old Map', 'wpsimplelocator'),
	'post_status' => 'publish',
	'post_type' => 'wpslmaps',
	'ping_status' => 'closed',
	'comment_status' => 'closed',
	'post_content' => '[{"featureType":"administrative","stylers":[{"visibility":"off"}]},{"featureType":"poi","stylers":[{"visibility":"simplified"}]},{"featureType":"road","elementType":"labels","stylers":[{"visibility":"simplified"}]},{"featureType":"water","stylers":[{"visibility":"simplified"}]},{"featureType":"transit","stylers":[{"visibility":"simplified"}]},{"featureType":"landscape","stylers":[{"visibility":"simplified"}]},{"featureType":"road.highway","stylers":[{"visibility":"off"}]},{"featureType":"road.local","stylers":[{"visibility":"on"}]},{"featureType":"road.highway","elementType":"geometry","stylers":[{"visibility":"on"}]},{"featureType":"water","stylers":[{"color":"#abbaa4"}]},{"featureType":"transit.line","elementType":"geometry","stylers":[{"color":"#3f518c"}]},{"featureType":"road.highway","stylers":[{"color":"#ad9b8d"}]}]'
);
$post = wp_insert_post($map);
add_post_meta($post, 'wpsl_map_id', $this->file_id);
?>