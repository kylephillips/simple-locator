<?php 

namespace SimpleLocator\Repositories;

use SimpleLocator\Repositories\SettingsRepository;

class PostRepository 
{

	/**
	* Settings Repo
	*/
	private $settings_repo;

	public function __construct()
	{
		$this->settings_repo = new SettingsRepository;
	}
	
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

	/**
	* Get all locations
	* @since 1.1.0
	* @param int limit
	* @return array of object
	*/
	public function allLocations($limit = '-1')
	{
		$args = array(
			'post_type'=> $this->settings_repo->getLocationPostType(),
			'posts_per_page' => $limit
		);
		/**
		* @filter simple_locator_all_locations
		*/
		$location_query = new \WP_Query(apply_filters('simple_locator_all_locations', $args));
		if ( $location_query->have_posts() ) : $c = 0;
			while ( $location_query->have_posts() ) : $location_query->the_post();
				$locations[$c] = new \stdClass();
				$locations[$c]->id = get_the_id();
				$locations[$c]->title = get_the_title();
				$locations[$c]->permalink = get_the_permalink();
				$locations[$c]->latitude = get_post_meta(get_the_id(), $this->settings_repo->getGeoField('lat'), true);
				$locations[$c]->longitude = get_post_meta(get_the_id(), $this->settings_repo->getGeoField('lng'), true);
			$c++;
			endwhile; 
		else : return false;
		endif; wp_reset_postdata();
		return $locations;
	}

	/**
	* Check if a post exists
	* @param string $post_title
	* @since 1.5.3
	* @return boolean
	*/
	public function postExists($post_title)
	{
		if ( !$post_title ) return false;
		$post_type = $this->settings_repo->getLocationPostType();
		$post = get_page_by_title($post_title, OBJECT, $post_type);
		if ( !$post ) return false;
		return true;
	}

}