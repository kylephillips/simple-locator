<?php 

namespace SimpleLocator;

/**
* Plugin Bootstrap
*/
class Bootstrap 
{

	function __construct()
	{
		$this->init();
		$this->registerPublicEvents();
		$this->registerAdminEvents();
		add_filter( 'plugin_action_links_' . 'simple-locator/simplelocator.php', array($this, 'settingsLink' ) );
		add_action( 'plugins_loaded', array($this, 'addLocalization') );
	}

	/**
	* Initialize
	*/
	public function init()
	{
		new \SimpleLocator\Migrations\Activation;
		new \SimpleLocator\Dependencies\DependencyFactory;
		new \SimpleLocator\WPData\PostTypes;
		new \SimpleLocator\WPData\MetaFields;
		new \SimpleLocator\WPData\UploadFilter;
		new \SimpleLocator\Settings\Settings;
		new \SimpleLocator\API\FormShortcode;
		new \SimpleLocator\API\SingleLocationShortcode;
		new \SimpleLocator\API\AllLocationsShortcode;
		new \SimpleLocator\Post\Singular;
		new \SimpleLocator\Integrations\IntegrationFactory;
		add_action( 'widgets_init', array($this, 'formWidget'));
	}

	/**
	* Register Public Application Events
	*/
	private function registerPublicEvents()
	{
		new \SimpleLocator\Events\RegisterPublicEvents;
	}

	/**
	* Register Admin Application Events
	*/
	private function registerAdminEvents()
	{
		if ( !is_admin() ) return;
		new \SimpleLocator\Events\RegisterAdminEvents;
		new \SimpleLocator\Services\Import\Events\RegisterImportEvents;
	}

	/**
	* Add a link to the settings on the plugin page
	*/
	public function settingsLink($links)
	{ 
  		$settings_link = '<a href="options-general.php?page=wp_simple_locator">' . __('Settings','wpsimplelocator') . '</a>'; 
  		$help_link = '<a href="http://locatewp.com">' . __('FAQ','wpsimplelocator') . '</a>'; 
  		array_unshift($links, $help_link); 
  		array_unshift($links, $settings_link);
  		return $links; 
	}

	/**
	* Register the Widget
	*/
	public function formWidget()
	{
		register_widget( 'SimpleLocator\API\FormWidget' );
	}

	/**
	* Localization Domain
	*/
	public function addLocalization()
	{
		load_plugin_textdomain(
			'wpsimplelocator', 
			false, 
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages' );
	}

}