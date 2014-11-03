<?php namespace SimpleLocator\Forms;
/**
* Wordpress Form Handlers
*/
class Handlers {

	public function __construct()
	{
		// Front End Form
		add_action( 'wp_ajax_nopriv_locate', array($this, 'wpsl_form_handler' ));
		add_action( 'wp_ajax_locate', array($this, 'wpsl_form_handler' ));

		// Admin Settings Post Type Select
		add_action( 'wp_ajax_wpslposttype', array($this, 'wpsl_posttype_handler' ));
	}

	/**
	* Map Form Handler
	*/
	public function wpsl_form_handler()
	{
		new Map;
	}

	/**
	* Post Type Form Handler (Settings)
	*/
	public function wpsl_posttype_handler()
	{
		new PostType;
	}

}