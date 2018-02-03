/**
* Primary Simple Locator Initialization
* @package Simple Locator
* @author Kyle Phillips - https://github.com/kylephillips
*/

jQuery(document).ready(function(){
	new SimpleLocator.Factory;
});

var SimpleLocator = SimpleLocator || {};

// DOM Selectors
SimpleLocator.selectors = {

}

// JS Data
SimpleLocator.jsData = {
	secure : false,
	// ajaxurl : ajaxurl,
	// nonce : wpsl_locator.np_nonce,
}

// Form Actions
SimpleLocator.formActions = {
	
}

// Map Objects
SimpleLocator.maps = {}

/**
* Primary Simple Locator Class
*/
SimpleLocator.Factory = function()
{
	var self = this;
	var $ = jQuery;

	self.init = function()
	{
		SimpleLocator.jsData.secure = ( location.protocol === 'https:' ) ? true : false;

		new SimpleLocator.PlacesAutocomplete;
		new SimpleLocator.DefaultMap;
		new SimpleLocator.SingleLocation;
		new SimpleLocator.AllLocations;
		self.bindEvents();
	}

	self.bindEvents = function()
	{
		
	}

	return self.init();
}