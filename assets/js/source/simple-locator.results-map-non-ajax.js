/**
* Display results on a map (non-ajax results)
* @package simple-locator
*/
var SimpleLocator = SimpleLocator || {};
SimpleLocator.ResultsMapNonAjax = function()
{
	var self = this;
	var $ = jQuery;

	self.activeMap;
	self.activeWrapper;
	self.mapIndex;

	self.selectors = {
		latitude : 'data-latitude',
		longitude : 'data-longitude',
	}

	self.bindEvents = function()
	{
		$(document).ready(function(){
			self.activeMap = $('[' + SimpleLocator.selectors.mapNonAjax + ']');
			if ( self.activeMap.length < 1 ) return;
			self.toggleLoading(true);
			self.setMapIndex();
			self.loadMap();
		});
	}

	/**
	* Set the map index
	*/
	self.setMapIndex = function()
	{
		var wrappers = $('[' + SimpleLocator.selectors.resultsWrapper + ']');
		self.activeWrapper = $(self.activeMap).parents('[' + SimpleLocator.selectors.resultsWrapper + ']');
		self.mapIndex = $(self.activeWrapper).index(wrappers);
	}

	self.loadMap = function()
	{
		SimpleLocator.markers[self.mapIndex] = [];
		
		var mappin = ( wpsl_locator.mappin ) ? wpsl_locator.mappin : '';
		var bounds = new google.maps.LatLngBounds();
		var mapOptions = {
			mapTypeId: 'roadmap',
			mapTypeControl: false,
			zoom: 8,
			styles: wpsl_locator.mapstyles,
			panControl : false
		}

		// Override options if custom options are set
		if ( wpsl_locator.custom_map_options === '1' ) mapOptions = wpsl_locator.map_options;
		var locations = [];
		var infoWindow = new google.maps.InfoWindow(), marker, i;
		
		SimpleLocator.maps[self.mapIndex] = new google.maps.Map( self.activeMap[0], mapOptions );
		self.addUserPin();
		
		// Array of locations
		for (var i = 0; i < simple_locator_results.length; i++) {
			var location = {
				title: simple_locator_results[i].title,
				lat: simple_locator_results[i].lat,
				lng: simple_locator_results[i].lng,
				id: simple_locator_results[i].id,
				infowindow: simple_locator_results[i].infowindow
			};
			locations.push(location);
		}

		// Loop through array of markers & place each one on the map  
		for( i = 0; i < locations.length; i++ ) {
			var position = new google.maps.LatLng(locations[i].lat, locations[i].lng);
			bounds.extend(position);			
			marker = new google.maps.Marker({
				position: position,
				map: SimpleLocator.maps[self.mapIndex],
				title: locations[i].title,
				icon: mappin
			});	

			// Info window for each marker 
			google.maps.event.addListener(marker, 'click', (function(marker, i) {
				return function() {
					infoWindow.setContent(locations[i].infowindow);
					infoWindow.open(SimpleLocator.maps[self.mapIndex], marker);

					// Simple Locator Callback function for click event
					$(document).trigger('simple-locator-marker-clicked', [marker, i, self.activeWrapper, locations[i].id]);
					wpsl_click_marker(marker, i, self.activeWrapper, locations[i].id); // Deprecated
				}
			})(marker, i));

			 // Push the marker to the global 'markers' array
	        SimpleLocator.markers[self.mapIndex].push(marker);
			
			// Center the Map
			SimpleLocator.maps[self.mapIndex].fitBounds(bounds);
			var listener = google.maps.event.addListener(SimpleLocator.maps[self.mapIndex], "idle", function() { 
					if ( simple_locator_results.length < 2 ) {
					SimpleLocator.maps[self.mapIndex].setZoom(13);
				}
				google.maps.event.removeListener(listener); 
			});
		}

		self.toggleLoading(false);
		$(document).trigger('simple-locator-map-rendered', [self.mapIndex, self.activeWrapper]);
	}

	/**
	* Add the user map pin
	*/
	self.addUserPin = function()
	{
		var mappin = ( wpsl_locator.mappinuser ) ? wpsl_locator.mappinuser : '';
		var latitude = parseFloat($(self.activeMap).attr(self.selectors.latitude));
		var longitude = parseFloat($(self.activeMap).attr(self.selectors.longitude));
		var position = new google.maps.LatLng(latitude, longitude);
		marker = new google.maps.Marker({
			position: position,
			map: SimpleLocator.maps[self.mapIndex],
			icon: mappin
		});	
	}

	/**
	* Toggle the loading state on the map
	*/
	self.toggleLoading = function(loading)
	{
		if ( loading ){
			$(self.activeMap).addClass('loading');
			return;
		}
		$(self.activeMap).show();
		$(self.activeMap).removeClass('loading');
	}

	return self.bindEvents();
}