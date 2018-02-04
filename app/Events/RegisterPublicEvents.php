<?php 
namespace SimpleLocator\Events;

use SimpleLocator\Listeners\LocationSearch;

/**
* Register Events
*/
class RegisterPublicEvents 
{
	public function __construct()
	{
		// Non-AJAX Search Form
		add_action( 'admin_post_locatorsearch', [$this, 'searchWasPerformed']);
		add_action( 'admin_post_nopriv_locatorsearch', [$this, 'searchWasPerformed']);
	}

	/**
	* A non-AJAX search was performed
	*/
	public function searchWasPerformed()
	{
		new LocationSearch;
	}
}