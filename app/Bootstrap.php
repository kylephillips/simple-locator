<?php namespace SimpleLocator;
/**
* Plugin Bootstrap
*/
class Bootstrap {

	function __construct()
	{
		$this->init();
		$this->setFormActions();
		add_filter( 'plugin_action_links_' . 'simple-locator/simplelocator.php', array($this, 'settingsLink' ) );
		add_action( 'plugins_loaded', array($this, 'addLocalization') );
	}


	/**
	* Initialize
	*/
	public function init()
	{
		new \SimpleLocator\Migrations\Activation;
		new \SimpleLocator\Dependencies\Dependencies;
		new \SimpleLocator\WPData\PostTypes;
		new \SimpleLocator\WPData\MetaFields;
		new \SimpleLocator\Settings\Settings;
		new \SimpleLocator\API\FormShortcode;
		new \SimpleLocator\API\MapShortcode;
		new \SimpleLocator\Post\Singular;
		add_action( 'widgets_init', array($this, 'formWidget'));
	}


	/**
	* Set Form Actions & Handlers
	*/
	public function setFormActions()
	{
		if ( is_admin() ) new Forms\Handlers;
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