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
	self.data;
	self.mapIndex;

	self.bindEvents = function()
	{
		$(document).on('simple-locator-form-success', function(e, data, form){
			self.activeForm = $(form);
			self.activeFormContainer = $(form).parents('[' + SimpleLocator.selectors.formContainer + ']');
			self.data = data;
			self.setMapIndex();
			self.loadMap();
		});
	}

	/**
	* Set the map index
	*/
	self.setMapIndex = function()
	{
		var maps = $('[' + SimpleLocator.selectors.map + ']');
		self.activeMap = $(self.activeFormContainer).find('[' + SimpleLocator.selectors.map + ']');
		self.mapIndex = $(self.activeMap).index(maps);
	}

	self.loadMap = function()
	{
		SimpleLocator.markers[self.mapIndex] = [];
		
		// Map Controls
		var disablecontrols = $(self.activeForm).attr('data-simple-locator-hide-map-controls');
		disablecontrols = ( typeof disablecontrols === 'undefined' || disablecontrols === '' ) ? false : true;

		// Control Position
		var controlposition = $(self.activeForm).attr('data-simple-locator-map-control-position');
		controlposition = ( typeof controlposition === 'undefined' || controlposition === '' ) ? 'TOP_LEFT' : controlposition;
		
		var mappin = ( wpsl_locator.mappin ) ? wpsl_locator.mappin : '';
		var bounds = new google.maps.LatLngBounds();
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
		var locations = [];
		var infoWindow = new google.maps.InfoWindow(), marker, i;
		
		SimpleLocator.maps[self.mapIndex] = new google.maps.Map( self.activeMap[0], mapOptions );
		
		// Array of locations
		for (var i = 0, length = self.data.results.length; i < length; i++) {
			var location = {
				title: self.data.results[i].title,
				lat: self.data.results[i].latitude,
				lng: self.data.results[i].longitude,
				id: self.data.results[i].id,
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
				icon: mappin
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
			
			// Center the Map
			SimpleLocator.maps[self.mapIndex].fitBounds(bounds);
			var listener = google.maps.event.addListener(SimpleLocator.maps[self.mapIndex], "idle", function() { 
					if ( self.data.results.length < 2 ) {
					SimpleLocator.maps[self.mapIndex].setZoom(13);
				}
				google.maps.event.removeListener(listener); 
			});
		}

		self.toggleLoading(false);
		$(document).trigger('simple-locator-map-rendered', [self.mapIndex, self.activeForm]);
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