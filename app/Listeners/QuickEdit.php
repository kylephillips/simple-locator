<?php
namespace SimpleLocator\Listeners;

use SimpleLocator\Repositories\SettingsRepository;

class QuickEdit extends AJAXListenerBase
{
	/**
	* Settings Repository
	*/
	private $settings;

	public function __construct()
	{
		$this->settings = new SettingsRepository;
		$this->validate();
		$this->save();
	}

	/**
	* Validate the Data
	*/
	private function validate()
	{
		if ( !isset($_GET['id']) || $_GET['id'] == '' ) return $this->error(__('A post ID was not provided.', 'simple-locator'));
	}

	/**
	* Save the Data
	*/
	private function save()
	{
		$latitude_field = $this->settings->getGeoField('lat');
		$longitude_field = $this->settings->getGeoField('lng');
		$fields = $_GET;
		$post_id = intval($_GET['id']);
		$allowed_fields = ['wpsl_address', 'wpsl_address_two', 'wpsl_city', 'wpsl_state', 'wpsl_zip', 'wpsl_country', 'wpsl_phone', 'wpsl_custom_geo', 'wpsl_website', $latitude_field, $longitude_field];
		foreach ( $fields as $key => $value ){
			if ( $key == 'action' || $key == 'id' ) continue;
			if ( $key == 'custom_geo' && $value == 'false' ) $value = 'false';
			if ( $key == 'custom_geo' && $value == 'true' ) $value = 'true';
			$meta_key = 'wpsl_' . $key;
			if ( !in_array($meta_key, $allowed_fields) ) continue;
			$meta_value = sanitize_text_field($value);
			update_post_meta($post_id, $meta_key, $meta_value);
		}
		$this->success(__('The location was successfully saved.', 'simple-locator'));
	}
}