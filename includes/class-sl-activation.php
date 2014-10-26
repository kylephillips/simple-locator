<?php

class WPSL_Activation {

	/**
	* Plugin Version
	*/
	private $version;

	public function __construct()
	{
		register_activation_hook( dirname( dirname(__FILE__) ) . '/simplelocator.php', array($this, 'install') );
		$this->version = 1.0;
		$this->setVersion();
	}


	/**
	* Activation Hook
	*/
	public function install()
	{
		$this->checkVersions();
		$this->setOptions();
	}


	/**
	* Check Wordpress and PHP versions
	*/
	private function checkVersions( $wp = '3.8', $php = '5.3.0' ) {
		global $wp_version;
		if ( version_compare( PHP_VERSION, $php, '<' ) )
			$flag = 'PHP';
		elseif ( version_compare( $wp_version, $wp, '<' ) )
			$flag = 'WordPress';
		else 
			return;
		$version = 'PHP' == $flag ? $php : $wp;
		deactivate_plugins( basename( __FILE__ ) );
		
		wp_die('<p><strong>Simple Locator</strong> plugin requires'.$flag.'  version '.$version.' or greater.</p>','Plugin Activation Error',  array( 'response'=>200, 'back_link'=>TRUE ) );
	}



	/**
	* Set the Plugin Version
	*/
	private function setVersion()
	{
		if ( !get_option('wpsl_version') ){
			update_option('wpsl_version', $this->version);
		}
		elseif ( get_option('wpsl_version') < $this->version ){
			update_option('wpsl_version', $this->version);	
		}
	}


	/**
	* Set Default Options
	*/
	private function setOptions()
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
		if ( !get_option('wpsl_output_css') ){
			update_option('wpsl_output_css', 'true');
		}
		if ( !get_option('wpsl_map_pin') ){
			update_option('wpsl_map_pin', '');
		}
	}


}