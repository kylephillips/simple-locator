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
		new SimpleLocatorAdmin.MapDisplay;
		new SimpleLocatorAdmin.DefaultMap;
		new SimpleLocatorAdmin.SearchHistory;
	}

	return self.init();
}