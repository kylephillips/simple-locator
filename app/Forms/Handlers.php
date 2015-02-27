<?php namespace SimpleLocator\Forms;

use SimpleLocator\Forms\MapHandler;
use SimpleLocator\Forms\PostTypeFieldsHandler;
use SimpleLocator\Forms\NonceHandler;

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

}