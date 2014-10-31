<?php namespace SimpleLocator;
/**
* Primary Plugin Class
*/
class Bootstrap {

	function __construct()
	{
		$this->init();
		$this->formActions();
		add_filter( 'plugin_action_links_' . 'wp-simple-locator/simplelocator.php', array($this, 'settingsLink' ) );
		add_action( 'init', array($this, 'localize') );
	}


	/**
	* Initialize
	*/
	public function init()
	{
		new \SimpleLocator\Migrations\Activation;
		new Dependencies;
		new \SimpleLocator\WPData\PostTypes;
		new \SimpleLocator\WPData\MetaFields;
		new Settings;
		new Shortcode;
		add_action( 'widgets_init', array($this, 'registerWidget'));
	}


	/**
	* Set Form Actions & Handlers
	*/
	public function formActions()
	{
		if ( is_admin() ) {

			// Front End Form
			add_action( 'wp_ajax_nopriv_locate', array($this, 'wpsl_form_handler' ));
			add_action( 'wp_ajax_locate', array($this, 'wpsl_form_handler' ));

			// Admin Settings Post Type Select
			add_action( 'wp_ajax_wpslposttype', array($this, 'wpsl_posttype_handler' ));
		}
	}


	/**
	* Add a link to the settings on the plugin page
	*/
	public function settingsLink($links)
	{ 
  		$settings_link = '<a href="options-general.php?page=wp_simple_locator">Settings</a>'; 
  		array_unshift($links, $settings_link); 
  		return $links; 
	}


	/**
	* Register the Widget
	*/
	public function registerWidget()
	{
		register_widget( 'SimpleLocator\Widget' );
	}


	/**
	* Localization Text Domain
	*/
	public function localize()
	{
		load_plugin_textdomain('wpsimplelocator', false, 'wp-simple-locator' . '/languages' );
	}

	/**
	* Map Form Handler
	*/
	public function wpsl_form_handler()
	{
		new Forms\Map;
	}

	/**
	* Post Type Form Handler (Settings)
	*/
	public function wpsl_posttype_handler()
	{
		new Forms\PostType;
	}

}