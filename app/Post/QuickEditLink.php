<?php
namespace SimpleLocator\Post;

use SimpleLocator\Repositories\SettingsRepository;

/**
* Add a location quick edit link to the admin post listing
* @see Listeners\QuickEdit
*/
class QuickEditLink
{
	/**
	* Settings Repository
	*/
	private $settings;

	/**
	* The Post Type for Locations
	* @var str
	*/
	private $post_type;

	/**
	* Include Fields?
	*/
	private $hide_locator_fields = false;

	/**
	* Lat/Lng Fields
	*/
	private $geocode_fields = [];

	public function __construct()
	{
		$this->settings = new SettingsRepository;
		$this->getSettings();
		add_filter('post_row_actions', [$this, 'addLink'], 10, 2);
	}

	/**
	* Set Object Vars (called in constructor to limit DB queries)
	*/
	private function getSettings()
	{
		$this->post_type = $this->settings->getLocationPostType();
		$hide_locator_fields = get_option('wpsl_hide_default_fields');
		if ( $hide_locator_fields == 'true' ) $this->hide_locator_fields = true;
		$this->geocode_fields['lat'] = $this->settings->getGeoField('lat');
		$this->geocode_fields['lng'] = $this->settings->getGeoField('lng');
	}

	/**
	* Add the quick edit link to the post row actions
	*/
	public function addLink($actions, $post)
	{
		if ( $post->post_type !== $this->post_type ) return $actions;
		if ( $this->hide_locator_fields ) return $actions;
		$post_type_object = get_post_type_object($this->post_type);

		$meta = get_post_meta($post->ID, '', true);
		$address = ( isset($meta['wpsl_address']) && $meta['wpsl_address'] !== '' ) ? $meta['wpsl_address'][0] : '';
		$address_two = ( isset($meta['wpsl_address_two']) && $meta['wpsl_address_two'] !== '' ) ? $meta['wpsl_address_two'][0] : '';
		$city = ( isset($meta['wpsl_city']) && $meta['wpsl_city'] !== '' ) ? $meta['wpsl_city'][0] : '';
		$state = ( isset($meta['wpsl_state']) && $meta['wpsl_state'] !== '' ) ? $meta['wpsl_state'][0] : '';
		$country = ( isset($meta['wpsl_country']) && $meta['wpsl_country'] !== '' ) ? $meta['wpsl_country'][0] : '';
		$zip = ( isset($meta['wpsl_zip']) && $meta['wpsl_zip'] !== '' ) ? $meta['wpsl_zip'][0] : '';
		$phone = ( isset($meta['wpsl_phone']) && $meta['wpsl_phone'] !== '' ) ? $meta['wpsl_phone'][0] : '';
		$latitude = ( isset($meta[$this->geocode_fields['lat']]) && $meta[$this->geocode_fields['lat']] !== '' )  ? $meta[$this->geocode_fields['lat']][0] : '';
		$longitude = ( isset($meta[$this->geocode_fields['lng']]) && $meta[$this->geocode_fields['lng']] !== '' )  ? $meta[$this->geocode_fields['lng']][0] : '';
		$website = ( isset($meta['wpsl_website']) && $meta['wpsl_website'] !== '' ) ? $meta['wpsl_website'][0] : '';

		$link = '<a href="#" class="simple-locator-quick-edit-link" data-simple-locator-quick-edit="' . $post->ID . '" data-address="' . $address . '" data-address_two="' . $address_two . '" data-city="' . $city . '" data-state="' . $state . '" data-zip="' . $zip . '" data-country="' . $country . '" data-phone="' . $phone . '" data-longitude="' . $longitude . '" data-latitude="' . $latitude . '" data-title="' . $post->post_title . '" data-website="' . $website . '">';
		$title = apply_filters('simple_locator_quick_edit_title', __('Edit Location', 'simple-locator'), $post);
		$actions['location-quick-edit'] = $link . $title  . '</a>';
		return $actions;
	}
}