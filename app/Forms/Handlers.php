<?php namespace SimpleLocator\Forms;

use SimpleLocator\Forms\MapHandler;
use SimpleLocator\Forms\PostTypeFieldsHandler;
use SimpleLocator\Forms\NonceHandler;
use SimpleLocator\Forms\PostTypeResetHandler;
use SimpleLocator\Forms\ImportFileHandler;
use SimpleLocator\Forms\ImportGetRowHandler;
use SimpleLocator\Forms\ImportMapColumnsHandler;
use SimpleLocator\Forms\ImportHandler;

/**
* Wordpress Form Handlers
*/
class Handlers {

	public function __construct()
	{
		// Front End Form
		add_action( 'wp_ajax_nopriv_locate', array($this, 'wpsl_form_handler' ));
		add_action( 'wp_ajax_locate', array($this, 'wpsl_form_handler' ));

		// Nonce Generation
		add_action( 'wp_ajax_nopriv_locatornonce', array($this, 'wpsl_nonce_handler' ));
		add_action( 'wp_ajax_locatornonce', array($this, 'wpsl_nonce_handler' ));

		// Admin Settings Post Type Select
		add_action( 'wp_ajax_wpslposttype', array($this, 'wpsl_posttype_handler' ));
		add_action( 'wp_ajax_wpslresetposttype', array($this, 'wpsl_reset_posttype' ));

		// Import Handlers
		add_action( 'admin_post_wpslimportupload', array($this, 'wpsl_import_file'));
		add_action( 'wp_ajax_wpslimportcolumns', array($this, 'wpsl_import_columns' ));
		add_action( 'admin_post_wpslmapcolumns', array($this, 'wpsl_map_columns'));
		add_action( 'wp_ajax_wpsldoimport', array($this, 'wpsl_do_import' ));
	}

	/**
	* Map Form Handler
	*/
	public function wpsl_form_handler()
	{
		new MapHandler;
	}

	/**
	* Post Type Form Handler (Settings)
	*/
	public function wpsl_posttype_handler()
	{
		new PostTypeFieldsHandler;
	}

	/**
	* Generate a Nonce
	*/
	public function wpsl_nonce_handler()
	{
		new NonceHandler;
	}

	/**
	* Reset Post Type to Default
	*/
	public function wpsl_reset_posttype()
	{
		new PostTypeResetHandler;
	}

	/**
	* Import File Handler
	*/
	public function wpsl_import_file()
	{
		new ImportFileHandler;
	}

	/**
	* Get the CSV columns for mapping
	*/
	public function wpsl_import_columns()
	{
		new ImportGetRowHandler;
	}

	/**
	* Map the columns for import
	*/
	public function wpsl_map_columns()
	{
		new ImportMapColumnsHandler;
	}

	/**
	* Do the import
	*/
	public function wpsl_do_import()
	{
		new ImportHandler;
	}

}