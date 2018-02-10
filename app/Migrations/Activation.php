<?php 
namespace SimpleLocator\Migrations;

use SimpleLocator\Migrations\DefaultOptions;
use SimpleLocator\Migrations\RequiredOptions;
use SimpleLocator\Migrations\CreateTables;
use SimpleLocator\WPData\PostTypes;

/**
* Plugin Activation
*/
class Activation 
{
	/**
	* Plugin Version
	*/
	private $version;

	public function __construct()
	{
		global $simple_locator_version;
		$this->version = $simple_locator_version;
		$this->setVersion();
		$this->migrateTables();
		new RequiredOptions;
		register_activation_hook( dirname(dirname( dirname(__FILE__) )) . '/simplelocator.php', [$this, 'install'] );
	}

	/**
	* Activation Hook
	*/
	public function install()
	{
		$types = new PostTypes;
		$options = new DefaultOptions;
		$types->register();
		flush_rewrite_rules();
		$this->migrateMaps();
	}

	/**
	* Set the Plugin Version
	*/
	private function setVersion()
	{
		update_option('wpsl_version', $this->version);
	}

	/**
	* Migrate Map Styles
	*/
	private function migrateMaps()
	{
		new MapStyles;
	}

	/**
	* Table Migration
	*/
	private function migrateTables()
	{
		new CreateTables;
	}
}