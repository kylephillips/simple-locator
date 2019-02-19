/**
* Display a single location map
* @package simple-locator
*/
var SimpleLocator = SimpleLocator || {};
SimpleLocator.SingleLocation = function()
{
	var self = this;
	var $ = jQuery;

	self.selectors = {
		mapContainer : 'data-simple-locator-single-map',
		latitude : 'data-latitude',
		longitude : 'data-longitude',
		title : 'data-title'
	}

	self.bindEvents = function()
	{
		$(document).ready(function(){
			self.loadAllMaps();
		});
	}

	self.loadAllMaps = function()
	{
		var maps = $('[' + self.selectors.mapContainer + ']');
		$.each(maps, function(){
			self.loadMap($(this));
		});
	}

	/**
	* Load the map
	*/
	self.loadMap = function(container)
	{
		var latitude = parseFloat($(container).attr(self.selectors.latitude));
		var longitude = parseFloat($(container).attr(self.selectors.longitude));
		var title = $(container).attr(self.selectors.title);
		var mappin = ( wpsl_locator.mappinsingle ) ? wpsl_locator.mappinsingle : '';
		var position = new google.maps.LatLng( latitude, longitude );
		var options = {
			zoom: 12,
			styles: wpsl_locator.mapstyles
		};
		
		// Override options if custom options are set
		if ( wpsl_locator.custom_map_options === '1' ) options = wpsl_locator.map_options;
		options.center = position;

		var map = new google.maps.Map(container[0], options);
		var marker = new google.maps.Marker({
			position: position,
			map: map,
			icon: mappin,
			title: title
		});
		$(container).removeClass('loading');
		$(document).trigger('simple-locator-single-map-rendered', [map, marker, container]);
	}

	return self.bindEvents();
}