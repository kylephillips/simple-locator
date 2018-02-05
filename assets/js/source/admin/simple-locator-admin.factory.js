/**
* Admin Functionality for Simple Locator
* @package Simple Locator
* @author Kyle Phillips - https://github.com/kylephillips
*
*/

jQuery(document).ready(function(){
	new SimpleLocatorAdmin.Factory;
});

var SimpleLocatorAdmin = SimpleLocatorAdmin || {};

// API Endpoints
SimpleLocatorAdmin.endpoints = {

}

/**
* Primary Simple Locator Class
*/
SimpleLocatorAdmin.Factory = function()
{
	var self = this;
	var $ = jQuery;

	self.init = function()
	{
		new SimpleLocatorAdmin.Modals;
		new SimpleLocatorAdmin.PostEdit;
		new SimpleLocatorAdmin.MapDisplay;
		new SimpleLocatorAdmin.ResultsDisplay;
		new SimpleLocatorAdmin.DefaultMap;
		new SimpleLocatorAdmin.SearchHistory;
	}

	return self.init();
}

/**
* Global function catches Google API Errors
* @link https://developers.google.com/maps/documentation/javascript/events#auth-errors
*/
var editScreenGoogleApiError = false;
function gm_authFailure(e){
	editScreenGoogleApiError = true;
}
