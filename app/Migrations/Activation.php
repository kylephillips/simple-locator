<?php namespace SimpleLocator\Migrations;

/**
* Plugin Activation
*/
class Activation {

	/**
	* Plugin Version
	*/
	private $version;


	public function __construct()
	{
		$this->version = 1.0;
		$this->setVersion();
		register_activation_hook( dirname(dirname( dirname(__FILE__) )) . '/simplelocator.php', array($this, 'install') );
	}


	/**
	* Activation Hook
	*/
	public function install()
	{
		new DefaultOptions;
		$this->migrateMaps();
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
	* Migrate Map Styles
	*/
	private function migrateMaps()
	{
		new MapStyles;
	}

}