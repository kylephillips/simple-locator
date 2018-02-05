/**
* Search History Map
*/
var SimpleLocatorAdmin = SimpleLocatorAdmin || {};
SimpleLocatorAdmin.SearchHistory = function()
{
	var self = this;
	var $ = jQuery;

	self.locations;

	self.selectors = {
		container : 'data-simple-locator-search-history-map',
		perPage : 'data-simple-locator-history-per-page'
	}

	self.bindEvents = function()
	{
		$(document).ready(function(){
			if ( $('[' + self.selectors.container + ']').length < 1 ) return;
			self.locations = search_history_locations;
			self.loadMap();
		});
		$('[' + self.selectors.perPage + ']').on('change', function(){
			$(this).parents('form').submit();
		});
	}

	/**
	* Load the Map
	*/
	self.loadMap = function()
	{
		var mapstyles = wpsl_locator_searchhistory.mapstyles;	
		var mappin = ( wpsl_locator_searchhistory.mappin ) ? wpsl_locator_searchhistory.mappin : '';
		var bounds = new google.maps.LatLngBounds();
		var locations = self.locations;
		var container = $('[' + self.selectors.container + ']');

		var mapOptions = {
			mapTypeId: 'roadmap',
			mapTypeControl: false,
			zoom: 8,
			styles: mapstyles,
			scrollwheel: false,
			panControl : false
		}
			
		var infoWindow = new google.maps.InfoWindow(), marker, i;
		var map = new google.maps.Map( container[0], mapOptions );
		
		// Loop through array of markers & place each one on the map  
		for( i = 0; i < locations.length; i++ ) {
			var position = new google.maps.LatLng(locations[i].latitude, locations[i].longitude);
			bounds.extend(position);
			
			var marker = new google.maps.Marker({
				position: position,
				map: map,
				title: locations[i].search_term,
				icon: mappin
			});	

			locations[i].infowindow = '<h4>' + locations[i].date + '</h4>';
			locations[i].infowindow += '<p>';
			locations[i].infowindow += wpsl_locator_searchhistory.searchTerm + ': ' + locations[i].search_term;
			locations[i].infowindow += '<br>' + wpsl_locator_searchhistory.searchTermFormatted + ': ' + locations[i].search_term_formatted;
			locations[i].infowindow += '<br>' + wpsl_locator_searchhistory.userIp + ': ' + locations[i].user_ip;
			locations[i].infowindow += '<br>' + wpsl_locator_searchhistory.distance + ': ' + locations[i].distance;
			locations[i].infowindow += '</p>';

			// Info window for each marker 
			google.maps.event.addListener(marker, 'click', (function(marker, i){
				return function() {
					infoWindow.setContent(locations[i].infowindow);
					infoWindow.open(map, marker);
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

		// Fit the map bounds to all the pins
		var boundsListener = google.maps.event.addListener((map), 'bounds_changed', function(event) {
			google.maps.event.removeListener(boundsListener);
		});

	}

	return self.bindEvents();
}