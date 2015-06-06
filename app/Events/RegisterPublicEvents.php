<?php namespace SimpleLocator\Events;

use SimpleLocator\Listeners\AJAXLocationSearch;
use SimpleLocator\Listeners\GenerateAndSendNonce;

/**
* Register Events
*/
class RegisterPublicEvents {

	public function __construct()
	{
		// Front End Map AJAX Search Form
		add_action( 'wp_ajax_nopriv_locate', array($this, 'JSMapFormWasSubmitted' ));
		add_action( 'wp_ajax_locate', array($this, 'JSMapFormWasSubmitted' ));

		// AJAX Nonce Generation
		add_action( 'wp_ajax_nopriv_locatornonce', array($this, 'JSNonceWasRequested' ));
		add_action( 'wp_ajax_locatornonce', array($this, 'JSNonceWasRequested' ));
	}

	/**
	* An AJAX locator form was submitted
	*/
	public function JSMapFormWasSubmitted()
	{
		new AJAXLocationSearch;
	}

	/**
	* A nonce was requested via AJAX
	*/
	public function JSNonceWasRequested()
	{
		new GenerateAndSendNonce;
	}

}