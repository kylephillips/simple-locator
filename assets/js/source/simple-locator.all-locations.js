/**
* Display a single location map
*/
var SimpleLocator = SimpleLocator || {};
SimpleLocator.AllLocations = function()
{
	var self = this;
	var $ = jQuery;

	self.locations = [];

	self.selectors = {
		map : 'data-simple-locator-all-locations-map'
	}

	self.bindEvents = function()
	{
		if ( $('[' + self.selectors.map + ']').length < 1 ) return;
		$(document).ready(function(){
			self.getData();
		});
	}

	self.getData = function()
	{
		var limit = $('[' + self.selectors.map + ']').attr('data-limit');
		if ( typeof limit === 'undefined' || limit === '' ) limit = '-1';

		$.ajax({
			url : SimpleLocator.endpoints.locations,
			type: 'GET',
			datatype: 'jsonp',
			data : {
				limit : limit
			},
			success: function(data){
				self.locations = data;
				self.loadMaps();
			},
			error: function(data){
				if ( wpsl_locator.jsdebug === '1' ){
					console.log('All Locations Error');
				}
			}
		});
	}

	self.loadMaps = function()
	{
		var maps = $('[' + self.selectors.map + ']');
		
		$.each(maps, function(){
			
			var container = $(this);
			var locations = self.locations;
			var mapstyles = wpsl_locator.mapstyles;	
			var bounds = new google.maps.LatLngBounds();
			var mapOptions = {
				mapTypeId: 'roadmap',
				mapTypeControl: false,
				zoom: 8,
				styles: mapstyles,
				panControl : false
			}
			if ( wpsl_locator.custom_map_options === '1' )	mapOptions = wpsl_locator.map_options;
				
			var infoWindow = new google.maps.InfoWindow(), marker, i;
			var map = new google.maps.Map( container[0], mapOptions );
			
			// Loop through array of markers & place each one on the map  
			for( i = 0; i < locations.length; i++ ) {
				var position = new google.maps.LatLng(locations[i].latitude, locations[i].longitude);
				bounds.extend(position);
				
				var marker = new google.maps.Marker({
					position: position,
					map: map,
					title: locations[i].title,
					icon: locations[i].mappin
				});	

				// Info window for each marker 
				google.maps.event.addListener(marker, 'click', (function(marker, i){
					return function() {
						infoWindow.setContent(locations[i].infowindow);
						infoWindow.open(map, marker);
						$(document).trigger('simple-locator-all-locations-marker-clicked', [marker, infoWindow]);
						wpsl_all_locations_marker_clicked(marker, infoWindow); // Deprecated
					}
				})(marker, i));
				
				// Center the Map
				map.fitBounds(bounds);
				var listener = google.maps.event.addListener(map, "idle", function() { 
						if ( locations.length < 2 ) {
						map.setZoom(13);
					}
					google.maps.event.removeListener(listener); 
				});
			}

			$(document).trigger('simple-locator-all-locations-rendered', [map]);
			wpsl_all_locations_rendered(map); // Deprecated
		});
	}

	return self.bindEvents();
}