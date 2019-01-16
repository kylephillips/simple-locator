/**
* Display a single location map
*/
var SimpleLocator = SimpleLocator || {};
SimpleLocator.AllLocations = function()
{
	var self = this;
	var $ = jQuery;

	self.data;
	self.mapIndex;
	self.activeMap;
	self.formData = {}; // Data to send in request
	self.paginated = false;
	self.page = 1;

	self.selectors = {
		map : 'data-simple-locator-all-locations-map',
		pagination : 'data-simple-locator-all-results-pagination',
		pageJumpForm : 'data-simple-locator-page-jump-form'
	}

	self.bindEvents = function()
	{
		if ( $('[' + self.selectors.map + ']').length < 1 ) return;	
		$(document).ready(function(){
			self.getData();
		});
		$(document).on('click', '[' + self.selectors.pagination + '] [' + SimpleLocator.selectors.paginationButton + ']', function(e){
			e.preventDefault();
			self.paginate($(this));
		});
		$(document).on('submit', '[' + SimpleLocator.selectors.pageJumpForm + ']', function(e){
			var container = $(this).parents('[' + SimpleLocator.selectors.resultsWrapper + ']');
			if ( $(container).hasClass('non-ajax') ) return;
			e.preventDefault();
			self.jumpToPage($(this));
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
		self.formData.limit = ( typeof limit !== 'undefined' && limit !== '' ) ? limit : '-1';
	}

	/**
	* Set the per page arg, provided through data-per-page attribute
	*/
	self.setPerPageArgs = function()
	{
		perpage = $(self.activeMap).attr('data-per-page');
		if ( typeof perpage !== 'undefined' && perpage !== '' ) {
			self.formData.per_page = parseInt(perpage);
			self.paginated = true;
		}
	}

	/**
	* Set the id args, provided through data-post-ids attribute
	*/
	self.setIdArgs = function()
	{
		var ids = $(self.activeMap).attr('data-post-ids');
		if ( typeof ids !== 'undefined' && ids !== '' ) self.formData.ids = ids;
	}

	/**
	* Set location args (if default map is enabled, center there )
	*/
	self.setLocationArgs = function()
	{
		var latitude = wpsl_locator.default_latitude;
		var longitude = wpsl_locator.default_longitude;
		var default_enabled = wpsl_locator.default_enabled;
		if ( typeof default_enabled === 'undefined' || default_enabled === '' ){
			self.formData.allow_empty_address = 'true';
			return;
		}
		if ( typeof latitude !== 'undefined' && latitude !== '' ){
			self.formData.latitude = latitude;
			self.formData.longitude = longitude;
			self.formData.distance = 5000;
			return;
		}
		self.formData.allow_empty_address = 'true';
	}

	self.getData = function()
	{
		var maps = $('[' + self.selectors.map + ']');
		$.each(maps, function(){
			self.activeMap = $(this);
			self.setMapIndex();

			// If default maps arent enabled and this is a form, abort
			var form = $(this).parents('[' + SimpleLocator.selectors.formContainer + ']').find('[' + SimpleLocator.selectors.form + ']');
			if ( typeof form !== 'undefined' && form !== '' && form.length > 0 ){
				if ( wpsl_locator.default_enabled !== '1' ) return;
			}

			// Set Query Args
			self.formData.page = self.page;
			self.setLocationArgs();
			self.setPerPageArgs();
			self.setTaxonomyArgs();
			self.setLimitArgs();
			self.setIdArgs();

			$.ajax({
				url : SimpleLocator.endpoints.search,
				type: 'GET',
				datatype: 'json',
				data : self.formData,
				success: function(data){
					if ( !data.results ) return self.noLocations();
					SimpleLocator.formData[self.mapIndex] = data;
					SimpleLocator.formData[self.mapIndex].allLocations = true;
					self.data = data;
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
		var locations = self.data.results;
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
		SimpleLocator.infoWindow = infoWindow;
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
					$(document).trigger('simple-locator-all-locations-marker-clicked', [marker, infoWindow, index]);
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
			var container = $(self.activeMap).siblings('[data-simple-locator-results-container]');
			if ( container.length < 1 ) {
				container = $('<div data-simple-locator-results-container><div ' + SimpleLocator.selectors.results + ' class="wpsl-results"></div></div>').insertAfter(self.activeMap);
			} 
		} else {
			var container = $(formContainer).find('[' + SimpleLocator.selectors.results + ']');
		}

		// If a title is passed in the shortcode, use it. Othewise use what is provided by the response
		var title = $(self.activeMap).attr(self.selectors.map);
		title = ( typeof title !== 'undefined' && title !== '' && title !== 'true' ) 
			? '<h3 class="wpsl-results-header">' + title + '</h3>' : false;

		var output = '';
		output += ( title ) ? title : self.data.results_header + self.data.current_counts;
		
		var options = ( typeof wpsl_locator_options === 'undefined' || wpsl_locator_options === '' ) ? false : true;
		if ( options && wpsl_locator_options.resultswrapper !== "" ) output += '<' + wpsl_locator_options.resultswrapper + '>';

		for( i = 0; i < self.data.results.length; i++ ) {
			output = output + self.data.results[i].output;
		}

		if ( options && wpsl_locator_options.resultswrapper !== "" ) output += '</' + wpsl_locator_options.resultswrapper + '>';

		if ( self.paginated && self.data.total_pages > 1 ){
			output += '<div class="simple-locator-ajax-pagination" data-simple-locator-all-results-pagination>';
			if ( self.data.back_button ) output += self.data.back_button;
			if ( self.data.next_button ) output += self.data.next_button;
			if ( self.data.loading_spinner ) output += self.data.loading_spinner;
			if ( self.data.page_jump_form ) output += self.data.page_jump_form;
			if ( self.data.page_position ) output += self.data.page_position;
			output += '</div>';
		}

		$(container).removeClass('loading').html(output);
		$(container).parents('[' + SimpleLocator.selectors.formContainer + ']').removeClass('loading');
	}

	/**
	* Pagination Action
	*/
	self.paginate = function(button)
	{
		$(button).siblings('.wpsl-icon-spinner').addClass('visible');
		var direction = $(button).attr(SimpleLocator.selectors.paginationButton);
		if ( direction === 'next' ){
			self.page = self.page + 1;
			self.getData();
			return;
		}
		self.page = self.page - 1;
		self.getData();
	}

	/**
	* Jump to a page
	*/
	self.jumpToPage = function(form)
	{
		var page = parseInt($(form).find('input[type="tel"]').val());
		if ( isNaN(page) ) return;
		var totalPages = SimpleLocator.formData[self.mapIndex].total_pages;
		if ( typeof totalPages !== 'undefined' && page > totalPages ) return;

		var activeContainer = $(form).parents('[' + SimpleLocator.selectors.formContainer + ']');
		var formIndex = $(activeContainer).index('[' + SimpleLocator.selectors.formContainer + ']');
		if ( !SimpleLocator.formData[formIndex].allLocations ) return;

		$(activeContainer).addClass('loading');

		self.page = page;
		self.getData();
	}

	return self.bindEvents();
}