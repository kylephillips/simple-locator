<?php 
namespace SimpleLocator\Repositories;

use SimpleLocator\Repositories\SettingsRepository;
use SimpleLocator\Services\LocationSearch\LocationResultPresenter;

class PostRepository 
{
	/**
	* Settings Repo
	*/
	private $settings_repo;

	/**
	* Location Result Presenter
	*/
	private $location_presenter;

	public function __construct()
	{
		$this->settings_repo = new SettingsRepository;
		$this->location_presenter = new LocationResultPresenter;
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
	public function allLocations($request = [])
	{
		$args = [];
		$args['post_type'] = $this->settings_repo->getLocationPostType();
		if ( isset($request['limit']) ) $args['posts_per_page'] = $request['limit'];
		if ( isset($request['taxfilter']) ) $args = $this->addTaxonomyArgs($request, $args);

		/**
		* @filter simple_locator_all_locations
		*/
		$location_query = new \WP_Query(apply_filters('simple_locator_all_locations', $args));
		if ( $location_query->have_posts() ) : 
			$c = 0;
			$custom_pin = $this->settings_repo->mapPin();
			while ( $location_query->have_posts() ) : $location_query->the_post();
				$location = new \StdClass;
				$location->title = get_the_title();
				$location->id = get_the_id();
				$location->latitude = get_post_meta(get_the_id(), $this->settings_repo->getGeoField('lat'), true);
				$location->longitude = get_post_meta(get_the_id(), $this->settings_repo->getGeoField('lng'), true);
				$location->wpsl_address = get_post_meta(get_the_id(), 'wpsl_address', true);
				$location->wpsl_city = get_post_meta(get_the_id(), 'wpsl_city', true);
				$location->wpsl_state = get_post_meta(get_the_id(), 'wpsl_state', true);
				$location->wpsl_zip = get_post_meta(get_the_id(), 'wpsl_zip', true);
				$location->wpsl_phone = get_post_meta(get_the_id(), 'wpsl_phone', true);
				$location->wpsl_website = get_post_meta(get_the_id(), 'wpsl_website', true);
				$locations[$c] = $this->location_presenter->present($location, $c, ['distance' => false]);
			$c++;
			endwhile; 
		else : return false;
		endif; wp_reset_postdata();
		return $locations;
	}

	/**
	* Add taxonomy args if they exist
	*/
	private function addTaxonomyArgs($request, $args)
	{
		foreach ( $request['taxfilter'] as $taxonomy => $terms ){
			$fields = ( is_numeric($terms[0]) ) ? 'term_id' : 'slug';
			$args['tax_query'][] = [
				'taxonomy' => $taxonomy,
				'fields' => $fields,
				'terms' => $terms
			];
		}
		return $args;
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