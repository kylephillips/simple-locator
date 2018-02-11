/**
* Display a single location map
*/
var SimpleLocator = SimpleLocator || {};
SimpleLocator.AllLocations = function()
{
	var self = this;
	var $ = jQuery;

	self.locations = [];
	self.mapIndex;
	self.activeMap;
	self.formData = {}; // Data to send in request

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

	self.setMapIndex = function()
	{
		var maps = $('[' + SimpleLocator.selectors.map + ']');
		self.mapIndex = $(self.activeMap).index(maps);
	}

	/**
	* Set the taxonomy arguments, provided through data-taxfilter- attributes
	*/
	self.setTaxonomyArgs = function()
	{
		var data = $(self.activeMap).data();
		var taxonomies = {};
		$.each(data, function(i, v){
			if ( i.indexOf('taxfilter') === 0 ){
				var taxonomy = i.substring(9).toLowerCase();
				var terms = ( typeof v !== 'number' ) ? v.split(',') : [v];
				taxonomies[taxonomy] = terms;
			}
		});
		self.formData.taxfilter = taxonomies;
	}

	/**
	* Set the limit arg, provided through data-limit attribute
	*/
	self.setLimitArgs = function()
	{
		limit = $(self.activeMap).attr('data-limit');
		if ( typeof limit !== 'undefined' && limit !== '' ) self.formData.limit = limit;
	}

	/**
	* Set the id args, provided through data-post-ids attribute
	*/
	self.setIdArgs = function()
	{
		var ids = $(self.activeMap).attr('data-post-ids');
		if ( typeof ids !== 'undefined' && ids !== '' ) self.formData.ids = ids;
	}

	self.getData = function()
	{
		var maps = $('[' + self.selectors.map + ']');
		$.each(maps, function(){
			self.activeMap = $(this);
			self.setMapIndex();
			self.setTaxonomyArgs();
			self.setLimitArgs();
			self.setIdArgs();
			$.ajax({
				url : SimpleLocator.endpoints.locations,
				type: 'GET',
				datatype: 'json',
				data : self.formData,
				success: function(data){
					if ( !data ) return self.noLocations();
					self.locations = data;
					self.loadMap();
				},
				error: function(data){
					if ( wpsl_locator.jsdebug === '1' ){
						console.log('All Locations Error');
					}
				}
			});
		});
	}

	self.loadMap = function()
	{	
		SimpleLocator.markers[self.mapIndex] = [];
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
		var map = new google.maps.Map( $(self.activeMap)[0], mapOptions );
		
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

			 // Push the marker to the global 'markers' array
        	SimpleLocator.markers[self.mapIndex].push(marker);
			
			// Center the Map
			map.fitBounds(bounds);
			var listener = google.maps.event.addListener(map, "idle", function() { 
					if ( locations.length < 2 ) {
					map.setZoom(13);
				}
				google.maps.event.removeListener(listener); 
			});
		}

		SimpleLocator.maps[self.mapIndex] = map;
		self.loadList();

		$(document).trigger('simple-locator-all-locations-rendered', [map]);
		wpsl_all_locations_rendered(map); // Deprecated
	}

	/**
	* No Locations Found
	*/
	self.noLocations = function()
	{
		var text = $(self.activeMap).attr('data-no-results-text');
		text = ( typeof text === 'undefined' || text === '' ) ? wpsl_locator.nolocationsfound : text;
		var message = '<p>' + text + '</p>';
		$(message).insertAfter(self.activeMap);
		$(self.activeMap).hide();
	}

	self.loadList = function()
	{
		var includeListing = $(self.activeMap).attr('data-include-listing');
		if ( typeof includeListing === 'undefined' || includeListing !== 'true' ) return;

		var formContainer = $(self.activeMap).parents('[' + SimpleLocator.selectors.formContainer + ']');
		if ( formContainer.length < 1 ){
			var container = $('<div data-simple-locator-results-container><div ' + SimpleLocator.selectors.results + ' class="wpsl-results"></div></div>').insertAfter(self.activeMap);
		} else {
			var container = $(formContainer).find('[' + SimpleLocator.selectors.results + ']');
		}

		var options = ( typeof wpsl_locator_options === 'undefined' || wpsl_locator_options === '') ? false : true;

		var headerText = $(self.activeMap).attr(self.selectors.map);
		headerText = ( typeof headerText === undefined || headerText === '' ) ? wpsl_locator.alllocations : headerText;

		var output = '';
		if ( options ) output += '<h3 class="wpsl-results-header">' + headerText + '</h3>';
		
		if ( options && wpsl_locator_options.resultswrapper !== "" ) output += '<' + wpsl_locator_options.resultswrapper + '>';
		for( i = 0; i < self.locations.length; i++ ) {
			output = output + self.locations[i].output;
		}
		if ( options && wpsl_locator_options.resultswrapper !== "" ) output += '</' + wpsl_locator_options.resultswrapper + '>';
		$(container).html(output);
	}

	return self.bindEvents();
}