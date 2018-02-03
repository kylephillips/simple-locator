<?php 

namespace SimpleLocator\Events;

use SimpleLocator\Listeners\AJAXLocationSearch;
use SimpleLocator\Listeners\LocationSearch;

/**
* Register Events
*/
class RegisterPublicEvents 
{

	public function __construct()
	{
		// Front End Map AJAX Search Form
		add_action( 'wp_ajax_nopriv_locate', array($this, 'JSMapFormWasSubmitted' ));
		add_action( 'wp_ajax_locate', array($this, 'JSMapFormWasSubmitted' ));

		// Non-AJAX Search Form
		add_action( 'admin_post_locatorsearch', array($this, 'searchWasPerformed' ));
		add_action( 'admin_post_nopriv_locatorsearch', 'searchWasPerformed' );
	}

	/**
	* An AJAX locator form was submitted
	*/
	public function JSMapFormWasSubmitted()
	{
		new AJAXLocationSearch;
	}

	/**
	* A non-AJAX search was performed
	*/
	public function searchWasPerformed()
	{
		new LocationSearch;
	}

}