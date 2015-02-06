<?php namespace SimpleLocator\Repositories;

class PostRepository {
	
	/**
	* Get the Location Data for a Post
	* @since 1.1.0
	* @param int $post_id
	* @return array
	*/
	public function getLocationData($post_id)
	{
		$location_data['title'] = get_the_title($post_id);
		$location_data['latitude'] = get_post_meta( $post_id, get_option('wpsl_lat_field'), true );
		$location_data['longitude'] = get_post_meta( $post_id, get_option('wpsl_lng_field'), true );
		$location_data['address'] = get_post_meta( $post_id, 'wpsl_address', true);
		$location_data['city'] = get_post_meta( $post_id, 'wpsl_city', true);
		$location_data['state'] = get_post_meta( $post_id, 'wpsl_state', true);
		$location_data['zip'] = get_post_meta( $post_id, 'wpsl_zip', true);
		$location_data['phone'] = get_post_meta( $post_id, 'wpsl_phone', true);
		$location_data['website'] = get_post_meta( $post_id, 'wpsl_website', true);
		$location_data['additionalinfo'] = get_post_meta( $post_id, 'wpsl_additionalinfo', true);
		return $location_data;
	}

}