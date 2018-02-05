/**
* Post Edit Page
* @package simple-locator
*/
var SimpleLocatorAdmin = SimpleLocatorAdmin || {};
SimpleLocatorAdmin.PostEdit = function()
{
	var self = this;
	var $ = jQuery;

	self.lookupaddress = true;
	self.mappinrelocated = false;

	self.selectors = {
		inputAddress : '#wpsl_address',
		inputCity : '#wpsl_city',
		inputState : '#wpsl_state',
		inputZip : '#wpsl_zip',
		inputLatitude : '#wpsl_latitude',
		inputLongitude : '#wpsl_longitude',
		noAddressConfirm : 'data-simple-locator-confirm-no-address',
		publishButton : '#publish'
	}

	self.bindEvents = function()
	{
		$(window).on('load', function(){
			self.setMapPinStatus();
			self.checkMapStatus();
			if ( $('#wpsl_custom_geo').val() === 'true' ) mappinrelocated = true;
		});
		$(document).on('click', '.acf-tab-button', function(){
			self.checkMapStatus();
		});
		$(document).on('click', self.selectors.publishButton, function(e){
			if ( wpsl_locator.lat_field !== 'wpsl_latitude' && wpsl_locator.map_field !== "" ) return;
			if ( !self.lookupaddress ) return;
			if ( self.mappinrelocated ) return;
			e.preventDefault();
			if ( editScreenGoogleApiError ){
				$(document).trigger('open-modal-manual', ['post-edit-error'])
				return;
			}
			var address = self.formatAddress();
			self.googleGeocodeAddress(address);
		});
		$(document).on('click', '[' + self.selectors.noAddressConfirm + ']', function(e){
			e.preventDefault();
			self.saveWithoutLocation();
		});
	}

	/**
	* Set Mappin relocated
	*/
	self.setMapPinStatus = function()
	{
		self.mappinrelocated = ( $('#wpsl_custom_geo').val('true') ) ? true : false;
	}

	/**
	* Format the provided address to submit for geocoding
	*/
	self.formatAddress = function()
	{
		var streetaddress = $(self.selectors.inputAddress).val();
		var city = $(self.selectors.inputCity).val();
		var state = $(self.selectors.inputState).val();
		var zip = $(self.selectors.inputZip).val();
		var address = streetaddress + ' ' + city + ' ' + state + ' ' + zip;
		return address;
	}

	/**
	* Geocode the provided address and save the coordinates
	*/
	self.googleGeocodeAddress = function(address)
	{
		geocoder = new google.maps.Geocoder();
		geocoder.geocode({
			'address' : address
		}, 
		function(results, status){
			if ( status == google.maps.GeocoderStatus.OK ){
				var lat = results[0].geometry.location.lat();
				var lng = results[0].geometry.location.lng();
				$(self.selectors.inputLatitude).val(lat);
				$(self.selectors.inputLongitude).val(lng);
				self.lookupaddress = false;
				$(self.selectors.publishButton).click();
			} else {
				$(document).trigger('open-modal-manual', ['post-edit-error'])
			}
		});
	}

	/**
	* Save without a location
	*/
	self.saveWithoutLocation = function()
	{
		$(document).trigger('close-modal-manual');
		self.lookupaddress = false;
		$(self.selectors.publishButton).click();
	}

	/**
	* Check Map Status
	*/
	self.checkMapStatus = function()
	{
		if ( $("#wpslmap").length > 0 ){
			var lat = $(self.selectors.inputLatitude).val();
			var lng = $(self.selectors.inputLongitude).val();
			if ( (lat !== "") && (lng !== "") ){
				$('#wpslmap').show();
				self.loadGoogleMap(lat, lng);
			}
		}
	}

	/**
	* Load the Google Map
	*/
	self.loadGoogleMap = function(lat, lng)
	{
		var map = new google.maps.Map(document.getElementById('wpslmap'), {
			zoom: 14,
			center: new google.maps.LatLng(lat,lng),
			mapTypeId: google.maps.MapTypeId.ROADMAP,
			mapTypeControl: false,
			scaleControl : false,
		});

		var marker, i;

		marker = new google.maps.Marker({
			position: new google.maps.LatLng(lat, lng),
			map: map,
			draggable: true
		});

		// Make Marker Draggable and update on change
		google.maps.event.addListener(marker, 'drag', function(){
			$(self.selectors.inputLatitude).val(marker.position.lat());
			$(self.selectors.inputLongitude).val(marker.position.lng());
			$(self.selectors.inputLatitude + ',' +  self.selectors.inputLongitude).attr('readonly', false);
			$('#wpsl_custom_geo').val('true');
			mappinrelocated = true;
		});
	}

	return self.bindEvents();
}