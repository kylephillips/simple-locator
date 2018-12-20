/**
* Map on Admin Post List Screen
* @package simple-locator
*/
var SimpleLocatorAdmin = SimpleLocatorAdmin || {};
SimpleLocatorAdmin.ListingMap = function()
{
	var self = this;
	var $ = jQuery;

	self.listings = [];
	self.markers = [];

	self.selectors = {
		mapLink : 'data-wpsl-listing-map-link',
		listingLatitude : 'data-wpsl-post-latitude',
		listingLongitude : 'data-wpsl-post-longitude',
		listingId : 'data-wpsl-listing-post-id',
		listingTitle : 'data-wpsl-post-title',
		listingEditLink : 'data-wpsl-edit-link',
		listingViewLink : 'data-wpsl-view-link',
		listingInfoWindow : 'data-wpsl-post-infowindow'
	}

	self.bindEvents = function()
	{
		$(document).ready(function(){
			if ( wpsl_locator.show_listing_map !== '1' ) return;
			if ( wpsl_locator.edit_listing !== '1' ) return;
			if ( $('.wp-list-table').length < 1 ) return;
			self.addMapElement();
			self.setListings();
		});
		$(document).on('click', '[' + self.selectors.mapLink + ']', function(e){
			e.preventDefault();
			self.openInfoWindow($(this));
		});
	}

	/**
	* Add the container for the map
	*/
	self.addMapElement = function()
	{
		var html = '<div class="wpsl-post-listing-map" id="wpsl-post-listing-map"></div>';
		$(html).insertBefore('.wp-list-table')
	}

	/**
	* Set the listings and indexes for each map link
	*/
	self.setListings = function()
	{
		var listings = $('.wp-list-table tbody tr');
		$.each(listings, function(i, v){
			var link = $(this).find('[' + self.selectors.mapLink + ']');
			$(link).attr(self.selectors.mapLink, i);
			var listing = {
				id : $(link).attr(self.selectors.listingId),
				title : $(link).attr(self.selectors.listingTitle),
				editLink : $(link).attr(self.selectors.listingEditLink),
				viewLink : $(link).attr(self.selectors.listingViewLink),
				latitude : $(link).attr(self.selectors.listingLatitude),
				longitude : $(link).attr(self.selectors.listingLongitude)
			};
			
			var infoWindow = $(link).siblings('[' + self.selectors.listingInfoWindow + ']').html();
			infoWindow += '<p>';
			infoWindow += '<a href="' + listing.editLink + '" class="button">' + wpsl_locator.edit + '</a>';
			infoWindow += '</p>';
			listing.infowindow = infoWindow
			self.listings.push(listing);
		});
		self.loadMap();
	}

	self.loadMap = function()
	{
		var mapstyles = wpsl_locator.mapstyles;	
		var mappin = ( wpsl_locator.mappin ) ? wpsl_locator.mappin : '';
		var bounds = new google.maps.LatLngBounds();
		var locations = self.listings;
		var container = $('#wpsl-post-listing-map');

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
				title: locations[i].title,
				icon: mappin
			});	
			self.markers.push(marker);

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

	/**
	* Open the infowindow when clicking a "View on map" link in the listing
	*/
	self.openInfoWindow = function(link)
	{
		var linkIndex = $(link).attr(self.selectors.mapLink);
		google.maps.event.trigger(self.markers[linkIndex], 'click');
	}

	return self.bindEvents();
}