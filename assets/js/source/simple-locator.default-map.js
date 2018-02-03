/**
* Show a Default Map if the option is selected
* @package simple-locator
*/
var SimpleLocator = SimpleLocator || {};
SimpleLocator.DefaultMap = function()
{
	var self = this;
	var $ = jQuery;

	self.selectors = {
		map : 'data-simple-locator-default-enabled'	
	}

	self.bindEvents = function()
	{
		$(document).ready(function(){
			self.queueDefaultMaps();
		});
	}

	/**
	* Queue the default map
	* @param boolean errors (geolocation errors) - if true, show default map (not user centered)
	*/
	self.queueDefaultMaps = function(errors)
	{
		var maps = $('[' + self.selectors.map + ']');
		$.each(maps, function(){
			var map = $(this);
			if ( wpsl_locator.default_user_center !== 'true' && !errors ){
				self.loadDefault(map);
				return;
			}
			// var userLocation = new SimpleLocator.UserLocation;
			// userLocation = userLocation.getLocation();
			// console.log(userLocation);
		});
		
			// if ( wpsl_locator.default_user_center === 'true' && navigator.geolocation && !errors ){
			// 	var forms = $('.simple-locator-form');
			// 	$.each(forms, function(i, v){
			// 		var formelements = setFormElements($(this));
			// 		$(formelements.results).empty().addClass('loading').show();
			// 		navigator.geolocation.getCurrentPosition(function(position){
			// 			process_geo_button(position, formelements);
			// 		}, function(error){
			// 			queue_default_map(true);
			// 			$(formelements.results).empty().removeClass('loading').hide();
			// 		});
			// 	});
			// } else {
			// 	self.loadDefault();
			// }
	}

	self.loadDefault = function(map)
	{
		var latitude = wpsl_locator.default_latitude;
		var longitude = wpsl_locator.default_longitude;
		$(map).removeClass('loading');
			
		var center = new google.maps.LatLng(latitude, longitude);
		var mapOptions = {
			center: center,
			zoom: parseInt(wpsl_locator.default_zoom),
			mapTypeControl: false,
			streetViewControl: false,
			styles: wpsl_locator.mapstyles
		}
		// Override options if custom options are set
		if ( wpsl_locator.custom_map_options === '1' )	mapOptions = wpsl_locator.map_options;
		mapOptions.center = center;
		var map = new google.maps.Map(map[0],mapOptions);
	}
	
	return self.bindEvents();
}