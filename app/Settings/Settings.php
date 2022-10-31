<?php 
namespace SimpleLocator\Settings;

use SimpleLocator\Repositories\FieldRepository;
use SimpleLocator\Repositories\SettingsRepository;
use SimpleLocator\Repositories\ImportRepository;
use SimpleLocator\Repositories\SearchHistoryRepository;
use SimpleLocator\Services\Import\CSV\Row;
use SimpleLocator\Services\Helpers\RemoteAddress;

/**
* Settings page
*/
class Settings 
{
	/**
	* Selected Unit of Measurement
	*/
	private $unit;

	/**
	* Selected Field Type (Custom vs Provided)
	*/
	private $field_type;

	/**
	* Currently Selected Post Type
	*/
	private $post_type;

	/**
	* Map Options
	* @var array
	*/
	private $map_options;

	/**
	* Field Repository
	*/
	private $field_repo;

	/**
	* Settings Repository
	*/
	private $settings_repo;

	/**
	* CSV Row Fetcher
	*/
	private $row;

	/**
	* Import Repository
	*/
	private $import_repo;

	/**
	* Search History Repository
	*/
	private $search_repo;

	/**
	* Remote Address Helper
	*/
	private $remote_address;
	

	public function __construct()
	{
		$this->field_repo = new FieldRepository;
		$this->settings_repo = new SettingsRepository;
		$this->import_repo = new ImportRepository;
		$this->search_repo = new SearchHistoryRepository;
		$this->remote_address = new RemoteAddress;
		$this->row = new Row;
		$this->setUnit();
		$this->setFieldType();
		$this->setPostType();
		$this->setMapOptions();
		add_action( 'admin_menu', [$this, 'registerPage'] );
		add_action( 'admin_init', [$this, 'registerSettings'] );
		add_action( 'updated_option', [$this, 'flushRewrites'], 10, 3);
	}

	/**
	* Set the Unit
	*/
	private function setUnit()
	{
		$this->unit = get_option('wpsl_measurement_unit');
	}

	/**
	* Set the Field Type
	*/
	private function setFieldType()
	{
		$this->field_type = $field_type = get_option('wpsl_field_type');
	}

	/**
	* Set the Selected Post Type
	*/
	private function setPostType()
	{
		$this->post_type = $this->settings_repo->getPostType();
	}

	/**
	* Set the Map Options
	*/
	private function setMapOptions()
	{
		$this->map_options['type'] = get_option('wpsl_map_styles_type');
		$this->map_options['choice'] = get_option('wpsl_map_styles_choice');
	}

	/**
	* Add the admin menu item
	*/
	public function registerPage()
	{
		add_options_page( 
			'WP Simple Locator',
			'Simple Locator',
			'manage_options',
			'wp_simple_locator', 
			[$this, 'settingsPage']
		);
	}

	/**
	* Register the settings
	*/
	public function registerSettings()
	{
		register_setting( 'wpsimplelocator-general', 'wpsl_map_service' );
		register_setting( 'wpsimplelocator-general', 'wpsl_google_api_key' );
		register_setting( 'wpsimplelocator-general', 'wpsl_google_geocode_api_key');
		register_setting( 'wpsimplelocator-general', 'wpsl_measurement_unit' );
		register_setting( 'wpsimplelocator-general', 'wpsl_output_css' );
		register_setting( 'wpsimplelocator-general', 'wpsl_include_user_pin' );
		register_setting( 'wpsimplelocator-general', 'wpsl_map_pin' );
		register_setting( 'wpsimplelocator-general', 'wpsl_map_pin_user' );
		register_setting( 'wpsimplelocator-general', 'wpsl_marker_clusters' );
		register_setting( 'wpsimplelocator-general', 'wpsl_singular_data' );
		register_setting( 'wpsimplelocator-general', 'wpsl_geo_button' );
		register_setting( 'wpsimplelocator-general', 'wpsl_gmaps_api' );
		register_setting( 'wpsimplelocator-general', 'wpsl_gmaps_api_admin');
		register_setting( 'wpsimplelocator-general', 'wpsl_enable_autocomplete' );
		register_setting( 'wpsimplelocator-general', 'wpsl_js_debug' );
		register_setting( 'wpsimplelocator-general', 'wpsl_save_searches' );
		register_setting( 'wpsimplelocator-general', 'wpsl_results_content_disabled' );
		register_setting( 'wpsimplelocator-general', 'wpsl_display_admin_table_map' );
		register_setting( 'wpsimplelocator-posttype', 'wpsl_post_type' );
		register_setting( 'wpsimplelocator-posttype', 'wpsl_show_hidden' );
		register_setting( 'wpsimplelocator-posttype', 'wpsl_hide_default_fields' );
		register_setting( 'wpsimplelocator-posttype', 'wpsl_field_type' );
		register_setting( 'wpsimplelocator-posttype', 'wpsl_lat_field' );
		register_setting( 'wpsimplelocator-posttype', 'wpsl_lng_field' );
		register_setting( 'wpsimplelocator-posttype', 'wpsl_acf_map_field' );
		register_setting( 'wpsimplelocator-posttype', 'wpsl_posttype_labels');
		register_setting( 'wpsimplelocator-posttype', 'wpsl_hide_default');
		register_setting( 'wpsimplelocator-posttype', 'wpsl_acf_tab');
		register_setting( 'wpsimplelocator-map', 'wpsl_map_styles_type' );
		register_setting( 'wpsimplelocator-map', 'wpsl_map_styles' );
		register_setting( 'wpsimplelocator-map', 'wpsl_map_styles_choice' );
		register_setting( 'wpsimplelocator-map', 'wpsl_custom_map_options' );
		register_setting( 'wpsimplelocator-map', 'wpsl_map_options' );
		register_setting( 'wpsimplelocator-map', 'wpsl_custom_autocomplete_options' );
		register_setting( 'wpsimplelocator-map', 'wpsl_autocomplete_options' );
		register_setting( 'wpsimplelocator-map', 'wpsl_marker_cluster_renderer_enabled');
		register_setting( 'wpsimplelocator-map', 'wpsl_marker_cluster_renderer');
		register_setting( 'wpsimplelocator-default', 'wpsl_default_map' );
		register_setting( 'wpsimplelocator-results', 'wpsl_results_options' );
		register_setting( 'wpsimplelocator-results', 'wpsl_results_fields_formatted' );
		register_setting( 'wpsimplelocator-results', 'wpsl_results_fields_formatted_default' );
	}

	/**
	* Get the Location PT Labels
	* @since 1.0.6
	*/
	private function getLabel($field, $default = "")
	{
		$labels = get_option('wpsl_posttype_labels');
		return ( isset($labels[$field]) && $labels[$field] !== "" ) ? $labels[$field] : $default;
	}

	/**
	* Display the Settings Page
	*/
	public function settingsPage()
	{
		$tab = ( isset($_GET['tab']) ) ? sanitize_text_field($_GET['tab']) : 'general';
		include( \SimpleLocator\Helpers::view('settings/settings') );
	}

	/**
	* Flush the rewrite rules when saving post type
	*/
	public function flushRewrites($option, $oldvalue, $_newvalue)
	{
		if ( $option == 'wpsl_post_type' || $option == 'wpsl_posttype_labels'){
			flush_rewrite_rules(false);
		}
	}

	/**
	* CSV Row Getter
	*/
	private function getCsvRow($row)
	{
		try {
			$result = $this->row->getRow($row);
		} catch ( \Exception $e ){
			return false;
		}
		return $result;
	}

}