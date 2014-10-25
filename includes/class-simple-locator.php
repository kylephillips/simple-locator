<?php
require_once('class-sl-activation.php');
require_once('class-sl-shortcode.php');
require_once('class-sl-formhandler.php');
require_once('class-sl-settings.php');
require_once('class-sl-metafields.php');
require_once('class-sl-posttype.php');
require_once('class-sl-dependencies.php');

/**
* Primary Plugin Class
*/
class WPSL_SimpleLocator {

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
		new WPSL_Activation;
		new WPSL_Dependencies;
		new WPSL_PostType;
		new WPSL_MetaFields;
		new WPSL_Settings;
		new WPSL_Shortcode;
	}


	/**
	* Set Form Actions & Handlers
	*/
	public function formActions()
	{
		if ( is_admin() ) {
			add_action( 'wp_ajax_nopriv_locate', 'wpsl_form_handler' );
			add_action( 'wp_ajax_locate', 'wpsl_form_handler' );
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
	* Localization Text Domain
	*/
	public function localize()
	{
		load_plugin_textdomain('wpsimplelocator', false, 'wp-simple-locator' . '/languages' );
	}

}