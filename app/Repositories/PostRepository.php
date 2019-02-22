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
		$args = $this->addTaxonomyArgs($request, $args);
		$args = $this->addIdArgs($request, $args);

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
				$locations[$c] = $this->location_presenter->present($location, $c, ['results' => 'default']);
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
		if (!isset($request['taxfilter'])) return $args;
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
	* Add posts__in args if they exist
	*/
	private function addIdArgs($request, $args)
	{
		if ( !isset($request['ids']) ) return $args;
		$ids = explode(',', $request['ids']);
		$args['post__in'] = $ids;
		return $args;
	}

	/**
	* Check if a post exists
	* @param array $post_data
	* @param array $transient
	* @since 1.5.3
	* @return boolean
	* @todo add getByWordpressField
	*/
	public function postExists($post_data, $transient)
	{
		$columns = $transient['columns'];
		$unique_field = false;
		foreach ( $columns as $column ){
			if ( $column->unique ){
				$unique_field = $column->field;
				$unique_value = $post_data[$column->csv_column];
				$unique_type = $column->field_type;
			}
		}
		if ( !$unique_field ) return false;
		if ( $unique_type == 'post_meta' ) return $this->getByMeta($unique_field, $unique_value, $transient['post_type']);
	}

	/**
	* Get field by post meta
	*/
	public function getByMeta($meta_key, $meta_value, $post_type)
	{
		$posts = false;
		$q = new \WP_Query([
			'post_type' => $post_type,
			'posts_per_page' => -1,
			'meta_key' => $meta_key,
			'meta_value' => $meta_value
		]);
		if ( $q->have_posts() ) $posts = $q->posts;
		wp_reset_postdata();
		return $posts;
	}

	
}