<?php
require_once('class-sl-shortcode.php');
require_once('class-sl-formhandler.php');
require_once('class-sl-settings.php');
require_once('class-sl-metafields.php');
require_once('class-sl-posttype.php');
require_once('class-sl-dependencies.php');

/**
* Primary Plugin Class
*/
class SL_SimpleLocator {

	/**
	* Plugin Version
	*/
	private $version;


	function __construct()
	{
		$this->version = '1.0';
		$this->set_version();
		$this->init();
		$this->form_action();
		$this->set_default_options();
		add_filter( 'plugin_action_links_' . 'wpsimplelocator/wpsimplelocator.php', array($this, 'settings_link' ) );
		add_action('init', array($this, 'add_localization') );
	}


	/**
	* Initialize
	*/
	public function init()
	{
		$dependencies = new SL_Dependencies;
		$post_type = new SL_PostType;
		$meta_fields = new SL_MetaFields;
		$settings = new SL_Settings;
		$shortcode = new SL_Shortcode;
	}


	/**
	* Check/update the version number in the DB
	*/
	public function set_version()
	{
		if ( !get_option('wpsl_version') ){
			update_option('wpsl_version', $this->version);
		}
		elseif ( get_option('wpsl_version') < $this->version ){
			update_option('wpsl_version', $this->version);	
		}
	}


	/**
	* Set Form Action & Handler
	*/
	public function form_action()
	{
		if ( is_admin() ) {
			add_action( 'wp_ajax_nopriv_locate', 'wpsl_form_handler' );
			add_action( 'wp_ajax_locate', 'wpsl_form_handler' );
		}
	}


	/**
	* Set Default Options
	*/
	public function set_default_options()
	{
		if ( !get_option('wpsl_post_type') ){
			update_option('wpsl_post_type', 'location');
		}
		if ( !get_option('wpsl_field_type') ){
			update_option('wpsl_field_type', 'wpsl');
		}
		if ( !get_option('wpsl_lat_field') ){
			update_option('wpsl_lat_field', 'wpsl_latitude');
		}
		if ( !get_option('wpsl_lng_field') ){
			update_option('wpsl_lng_field', 'wpsl_longitude');
		}
	}


	/**
	* Add a link to the settings on the plugin page
	*/
	public function settings_link($links)
	{ 
  		$settings_link = '<a href="options-general.php?page=wp_simple_locator">Settings</a>'; 
  		array_unshift($links, $settings_link); 
  		return $links; 
	}


	/**
	* Localization Domain
	*/
	public function add_localization()
	{
		load_plugin_textdomain('wpsimplelocator', false, 'wpsimplelocator' . '/languages' );
	}

}