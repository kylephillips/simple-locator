/**
* Show a Default Map if the option is selected
* @package simple-locator
*/
var SimpleLocator = SimpleLocator || {};
SimpleLocator.DefaultMap = function()
{
	var self = this;
	var $ = jQuery;

	self.mapIndex;

	self.selectors = {
		map : 'data-simple-locator-default-enabled'	
	}

	self.bindEvents = function()
	{
		$(document).ready(function(){
			if ( wpsl_locator.default_user_center === 'true' ) return; // See geolocation for user-centered automatic maps
			self.queueDefaultMaps();
		});
	}

	/**
	* Queue the default map
	*/
	self.queueDefaultMaps = function(errors)
	{
		var maps = $('[' + self.selectors.map + ']');
		$.each(maps, function(){
			var map = $(this);
				self.setMapIndex(map);
				self.loadDefault(map);
				return;
		});
	}

	/**
	* Set the map index for a map
	*/
	self.setMapIndex = function(map)
	{
		var wrappers = $('[' + SimpleLocator.selectors.resultsWrapper + ']');
		self.mapIndex = $(map).parents('[' + SimpleLocator.selectors.resultsWrapper + ']').index(wrappers);
	}

	/**
	* Load the default map
	*/ 
	self.loadDefault = function(map)
	{
		var latitude = wpsl_locator.default_latitude;
		var longitude = wpsl_locator.default_longitude;
		$(map).removeClass('loading');
			
		var center = new google.maps.LatLng(latitude, longitude);
		var mapOptions = {
			center: center,
			zoom: parseInt(wpsl_locator.default_zoom),
			styles: wpsl_locator.mapstyles
		}
		// Override options if custom options are set
		if ( wpsl_locator.custom_map_options === '1' )	mapOptions = wpsl_locator.map_options;
		mapOptions.center = center;
		SimpleLocator.maps[self.mapIndex] = new google.maps.Map(map[0],mapOptions);
	}
	
	return self.bindEvents();
}