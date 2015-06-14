<?php

namespace SimpleLocator\Integrations\ACF;

use SimpleLocator\Repositories\SettingsRepository;

class AdvancedCustomFields
{
	/**
	* Settings Repository
	* @var SimpleLocator\Repositories\SettingsRepository
	*/
	private $settings_repo;

	public function __construct()
	{
		if ( !function_exists('acf_field_type_exists') ) return;
		$this->settings_repo = new SettingsRepository;
		add_action( 'acf/save_post', array($this, 'saveMapFieldCoordinates'), 10 );
	}

	/**
	* Save map field coordinates to the selected latitude/longitude fields
	* @param int $post_id
	* @see https://wordpress.org/support/topic/possible-to-search-distance-between-posts-user
	*/
	public function saveMapFieldCoordinates($post_id)
	{
		if ( empty( $_POST['acf']) ) return;
		$option = get_option('wpsl_acf_map_field');
		if ( !$option ) return;
		if ( !isset($_POST['acf'][$option]) ) return;
		$map_field = $_POST['acf'][$option];
		if ( isset($map_field['lat']) && isset($map_field['lng']) ){
			update_post_meta($post_id, $this->settings_repo->getGeoField('lat'), $map_field['lat']);
			update_post_meta($post_id, $this->settings_repo->getGeoField('lng'), $map_field['lng']);
		}
	}
}