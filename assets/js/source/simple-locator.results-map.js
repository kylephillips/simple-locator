/**
* Display results on a map
* @package simple-locator
*/
var SimpleLocator = SimpleLocator || {};
SimpleLocator.ResultsMap = function()
{
	var self = this;
	var $ = jQuery;

	self.activeForm;
	self.activeFormContainer;
	self.activeMap;
	self.mapContainer;
	self.data;
	self.mapIndex;

	self.bindEvents = function()
	{
		$(document).on('simple-locator-form-success', function(e, data, form){
			self.activeForm = $(form);
			self.activeFormContainer = $(form).parents('[' + SimpleLocator.selectors.formContainer + ']');
			self.data = data;
			self.setMapContainer();
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
		self.mapIndex = $(wrappers).index(self.activeFormContainer);
		if ( self.mapIndex === -1 ) self.mapIndex = 0;
	}

	/**
	* Set the map container
	*/
	self.setMapContainer = function()
	{
		var container = $(self.activeForm).attr('data-simple-locator-map-container');
		if ( typeof container === 'undefined' || container === ''){
			self.activeMap = $(self.activeFormContainer).find('[' + SimpleLocator.selectors.map + ']');
			return;
		}
		self.activeMap = $(container);
	}

	self.loadMap = function()
	{
		self.removeMapMarkers();
		var bounds = new google.maps.LatLngBounds();
		var infoWindow = new google.maps.InfoWindow();
		SimpleLocator.infoWindow = infoWindow;

		if ( !SimpleLocator.maps[self.mapIndex] ){
			// Map Controls
			var disablecontrols = $(self.activeForm).attr('data-simple-locator-hide-map-controls');
			disablecontrols = ( typeof disablecontrols === 'undefined' || disablecontrols === '' ) ? false : true;

			// Control Position
			var controlposition = $(self.activeForm).attr('data-simple-locator-map-control-position');
			controlposition = ( typeof controlposition === 'undefined' || controlposition === '' ) ? 'TOP_LEFT' : controlposition;
	
			var mapOptions = {
				mapTypeId: 'roadmap',
				mapTypeControl: false,
				zoom: 8,
				styles: wpsl_locator.mapstyles,
				panControl : false,
				disableDefaultUI: disablecontrols,
				zoomControlOptions : {
					position : google.maps.ControlPosition[controlposition]
				}
			}

			// Override options if custom options are set
			if ( wpsl_locator.custom_map_options === '1' ) mapOptions = wpsl_locator.map_options;
			SimpleLocator.maps[self.mapIndex] = new google.maps.Map( self.activeMap[0], mapOptions );
		}

		// Array of locations
		var locations = [];
		for (var i = 0, length = self.data.results.length; i < length; i++) {
			var location = {
				title: self.data.results[i].title,
				lat: self.data.results[i].latitude,
				lng: self.data.results[i].longitude,
				id: self.data.results[i].id,
				mappin : self.data.results[i].mappin,
				infowindow: self.data.results[i].infowindow
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
				icon: locations[i].mappin
			});	

			// Info window for each marker 
			google.maps.event.addListener(marker, 'click', (function(marker, i) {
				return function() {
					infoWindow.setContent(locations[i].infowindow);
					infoWindow.open(SimpleLocator.maps[self.mapIndex], marker);

					// Simple Locator Callback function for click event
					$(document).trigger('simple-locator-marker-clicked', [marker, i, self.activeForm, locations[i].id]);
					wpsl_click_marker(marker, i, self.activeForm, locations[i].id); // Deprecated
				}
			})(marker, i));

			 // Push the marker to the global 'markers' array
	        SimpleLocator.markers[self.mapIndex].push(marker);
		}

		if ( wpsl_locator.includeuserpin !== '' ){
			var userposition = self.addUserPin();
			if ( userposition ) bounds.extend(userposition);
		}

		SimpleLocator.maps[self.mapIndex].fitBounds(bounds);

		// Zoom in if there is only one result and user location isnt enabled
		var listener = google.maps.event.addListener(SimpleLocator.maps[self.mapIndex], "idle", function() { 
			if ( self.data.results.length < 2 && wpsl_locator.includeuserpin === '' ) {
				SimpleLocator.maps[self.mapIndex].setZoom(13);
			}
			google.maps.event.removeListener(listener); 
		});
		self.toggleLoading(false);
		
		SimpleLocator.utilities.clusterMarkers(SimpleLocator.maps[self.mapIndex], SimpleLocator.markers[self.mapIndex]);
		$(document).trigger('simple-locator-map-rendered', [self.mapIndex, self.activeForm]);
	}

	/**
	* Add the user map pin
	*/
	self.addUserPin = function()
	{
		if ( SimpleLocator.userPin[self.mapIndex] ) {
			SimpleLocator.userPin[self.mapIndex].setMap(null);
			SimpleLocator.userPin[self.mapIndex] = null;
		}
		if ( self.data.allow_empty_address && self.data.address === '') {
			return false;
		}
		var mappin = ( wpsl_locator.mappinuser ) ? wpsl_locator.mappinuser : '';
		var position = new google.maps.LatLng(self.data.latitude, self.data.longitude);
		marker = new google.maps.Marker({
			position: position,
			map: SimpleLocator.maps[self.mapIndex],
			icon: mappin
		});	
		SimpleLocator.userPin[self.mapIndex] = marker;
		return position;
	}

	/**
	* Remove all markers from the map
	*/
	self.removeMapMarkers = function()
	{
		if ( !SimpleLocator.markers[self.mapIndex] ) {
			SimpleLocator.markers[self.mapIndex] = [];
			return;	
		}
		for (var i = 0; i < SimpleLocator.markers[self.mapIndex].length; i++){
			SimpleLocator.markers[self.mapIndex][i].setMap(null);
		}
		SimpleLocator.markers[self.mapIndex] = [];
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