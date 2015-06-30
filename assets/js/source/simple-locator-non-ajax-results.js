/**
* Non-Ajax Search Results map
*/
var NonAjaxResults = function()
{
	var plugin = this;
	var $ = jQuery;

	plugin.results = $('*[data-wpsl-result]');
	plugin.resultData = [];

	plugin.init = function(){
		plugin.setResultData();
	}

	plugin.setResultData = function(){
		$.each(plugin.results, function(i, v){
			plugin.resultData[i] = {
				latitude : $(this).attr('data-lat'),
				longitude : $(this).attr('data-lng'),
				permalink : $(this).attr('data-permalink'),
				title : $(this).attr('data-title')
			};
		});
		plugin.loadMap();
	}

	plugin.loadMap = function(){
		markers = [];
		var locations = plugin.resultData;
		var mapstyles = wpsl_locator.mapstyles;	
		var mappin = ( wpsl_locator.mappin ) ? wpsl_locator.mappin : '';
		var bounds = new google.maps.LatLngBounds();
		var mapOptions = {
				mapTypeId: 'roadmap',
				mapTypeControl: false,
				zoom: 8,
				styles: mapstyles,
				panControl : false
			}
		var infoWindow = new google.maps.InfoWindow(), marker, i;
		var mapContainer = $(wpsl_locator_options.mapcont)[0];
		$(mapContainer).show();

		var map = new google.maps.Map( mapContainer, mapOptions );
		
		// Loop through array of markers & place each one on the map  
		for( i = 0; i < locations.length; i++ ) {
			var position = new google.maps.LatLng(locations[i].latitude, locations[i].longitude);
			bounds.extend(position);
			
			var marker = new google.maps.Marker({
				position: position,
				map: map,
				title: locations[i].title,
				icon: mappin
			});	

			// Info window for each marker 
			google.maps.event.addListener(marker, 'click', (function(marker, i){
				return function() {
					infoWindow.setContent('<h4>' + locations[i].title + '</h4><p><a href="' + locations[i].permalink + '">' + wpsl_locator.viewlocation + '</a></p>');
					infoWindow.open(map, marker);
				}
			})(marker, i));
			
			// Center the Map
			markers.push(marker);
			map.fitBounds(bounds);
			var listener = google.maps.event.addListener(map, "idle", function() { 
					if ( locations.length < 2 ) {
					map.setZoom(13);
				}
				google.maps.event.removeListener(listener); 
			});
		}

		// Fit the map bounds to all the pins
		var boundsListener = google.maps.event.addListener((map), 'bounds_changed', function(event) {
			google.maps.event.removeListener(boundsListener);
		});

		googlemap = map;
	}

	return plugin.init();
}