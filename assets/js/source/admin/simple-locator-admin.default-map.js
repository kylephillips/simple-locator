/**
* Default Map Settings Page
* @package simple-locator
*/
var SimpleLocatorAdmin = SimpleLocatorAdmin || {};
SimpleLocatorAdmin.DefaultMap = function()
{
	var self = this;
	var $ = jQuery;

	self.selectors = {
		showDefaultCheckbox : 'data-simple-locator-default-checkbox',
		defaultMap : 'data-simple-locator-default-map',
		defaultMapContainer : 'data-simple-locator-default-map-container',
		zoomInput : 'data-simple-locator-default-map-zoom',
		latitudeInput : 'data-simple-locator-default-map-latitude',
		longitudeInput : 'data-simple-locator-default-map-longitude',
		locationSearchButton : 'data-simple-locator-location-search-button',
		locationSearchInput : 'data-simple-locator-location-search-input',
		error : 'data-simple-locator-error',
		centerUserPositionCheckbox : 'data-simple-locator-user-position-default'
	}

	self.bindEvents = function()
	{
		$(document).ready(function(){
			if ( $('[' + self.selectors.defaultMap + ']').length < 1 ) return;
			self.toggleDefaultMap();
			self.toggleHttpsWarning();
		});
		$(document).on('change', '[' + self.selectors.showDefaultCheckbox + ']', function(){
			self.toggleDefaultMap();
		});
		$(document).on('click', '[' + self.selectors.locationSearchButton + ']', function(e){
			e.preventDefault();
			self.findLocation();
		});
		$(document).on('change', '[' + self.selectors.centerUserPositionCheckbox + ']', function(){
			self.toggleHttpsWarning();
		});
	}

	self.toggleDefaultMap = function()
	{
		var checked = $('[' + self.selectors.showDefaultCheckbox + ']').is(':checked');
		var row = $('[' + self.selectors.defaultMapContainer + ']');
		if ( checked ){
			$(row).show();
			self.loadDefaultMap();
			return
		}
		$(row).hide();
	}

	self.loadDefaultMap = function(lat, lng)
	{
		lat = ( typeof lat === 'undefined' || lat === '' ) ? false : lat;
		lng = ( typeof lat === 'undefined' || lng === '' ) ? false : lng;

		if ( !lat ) lat = wpsl_locator_defaultmap.latitude;
		if ( !lng ) lng = wpsl_locator_defaultmap.longitude;

		var center = new google.maps.LatLng(lat, lng);
		var mapOptions = {
			center: center,
			zoom: parseInt(wpsl_locator_defaultmap.zoom),
			mapTypeControl: false,
			streetViewControl: false,
			styles: wpsl_locator_defaultmap.styles
		}
		var map = new google.maps.Map(document.getElementById('wpsl-default'),mapOptions);
		var marker = new google.maps.Marker({
			position: center,
			map: map,
			icon: wpsl_locator_defaultmap.mappin,
			draggable: true
		});

		if ( lat && lng ){
			$('[' + self.selectors.latitudeInput + ']').val(lat);
			$('[' + self.selectors.longitudeInput + ']').val(lng);
		}

		// Set the Zoom Level on Change
		google.maps.event.addListener(map, 'zoom_changed', function(){
			var zoomLevel = map.getZoom();
			$('[' + self.selectors.zoomInput + ']').val(zoomLevel);
		});

		// Make Marker Draggable and update on change
		google.maps.event.addListener(marker, 'drag', function(){
			$('[' + self.selectors.latitudeInput + ']').val(marker.position.lat());
			$('[' + self.selectors.longitudeInput + ']').val(marker.position.lng());
		});
	}

	self.findLocation = function()
	{
		self.toggleError(false);
		var searchterm = $('[' + self.selectors.locationSearchInput + ']').val();
		geocoder = new google.maps.Geocoder();
			
		geocoder.geocode({
			'address' : searchterm
		}, 
		function(results, status){
			if ( status == google.maps.GeocoderStatus.OK ){
				var lat = results[0].geometry.location.lat();
				var lng = results[0].geometry.location.lng();
				self.loadDefaultMap(lat, lng);
				return;
			}
			self.toggleError(true);
		});
	}

	self.loadSearchResults = function(lat, lng)
	{
		self.loadDefaultMap(lat, lng);
	}

	self.toggleError = function(visible)
	{
		if ( visible ){
			$('[' + self.selectors.error + ']').show();
			return;
		}
		$('[' + self.selectors.error + ']').hide();
	}

	self.toggleHttpsWarning = function()
	{
		if ( $('[' + self.selectors.centerUserPositionCheckbox + ']').is(':checked') ){
			if ( location.protocol !== 'https:' ){
				$('[data-simple-locator-no-https]').show();
			}
			return;
		}
		$('[data-simple-locator-no-https]').hide();
	}

	return self.bindEvents();
}