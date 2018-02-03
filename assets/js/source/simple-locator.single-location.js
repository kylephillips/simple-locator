/**
* Display a single location map
* @package simple-locator
*/
var SimpleLocator = SimpleLocator || {};
SimpleLocator.SingleLocation = function()
{
	var self = this;
	var $ = jQuery;

	self.bindEvents = function()
	{
		if ( typeof wpsl_locator_single === 'undefined' || wpsl_locator_single == '' ) return;
		$(document).ready(function(){
			self.loadMap();
		});
	}

	/**
	* Load the map
	*/
	self.loadMap = function()
	{
		var mappin = ( wpsl_locator.mappin ) ? wpsl_locator.mappin : '';
		var position = new google.maps.LatLng( parseFloat(wpsl_locator_single.latitude), parseFloat(wpsl_locator_single.longitude) );
		var options = {
			zoom: 12,
			styles: wpsl_locator.mapstyles
		};
		
		// Override options if custom options are set
		if ( wpsl_locator.custom_map_options === '1' ) options = wpsl_locator.map_options;
		options.center = position;

		var map = new google.maps.Map( document.getElementById('locationmap') , options );
		var marker = new google.maps.Marker({
			position: position,
			map: map,
			icon: mappin,
			title: wpsl_locator_single.title
		});
	}

	return self.bindEvents();
}