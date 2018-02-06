/**
* General Settings Page
* @package simple-locator
*/
var SimpleLocatorAdmin = SimpleLocatorAdmin || {};
SimpleLocatorAdmin.GeneralSettings = function()
{
	var self = this;
	var $ = jQuery;

	self.mediaUploader = null;

	self.selectors = {
		geoEnableCheckbox : 'data-simple-locator-geo-enabled-checkbox',
		locationButtonText : 'data-simple-locator-location-button-text',
		uploadMapPinButton : 'data-simple-locator-upload-pin-button',
		removeMapPinButton : 'data-simple-locator-remove-pin-button',
		mapPinImage : 'data-simple-locator-map-pin-image',
		mapPinInput : 'data-simple-locator-map-pin-input',
		mapPinContainer : 'data-simple-locator-map-pin-image-container'
	}

	self.bindEvents = function()
	{
		$(document).ready(function(){
			self.toggleLocationButtonText();
		});
		$(document).on('change', '[' + self.selectors.geoEnableCheckbox + ']', function(){
			self.toggleLocationButtonText();
		});
		$(document).on('click', '[' + self.selectors.uploadMapPinButton + ']', function(e){
			e.preventDefault();
			self.openMediaLibrary($(this));
		});
		$(document).on('click', '[' + self.selectors.removeMapPinButton + ']', function(e){
			e.preventDefault();
			self.removeMapPin();
		});
	}

	self.toggleLocationButtonText = function()
	{
		var text = $('[' + self.selectors.locationButtonText + ']');
		if ( $('[' + self.selectors.geoEnableCheckbox + ']').is(':checked') ){
			$(text).show();
			return;
		}
		$(text).hide();
	}

	self.removeMapPin = function()
	{
		$('[' + self.selectors.mapPinImage + ']').remove();
		$('[' + self.selectors.mapPinInput + ']').val('');
		$('[' + self.selectors.mapPinContainer + ']').append('<input id="upload_image_button" type="button" value="' + wpsl_locator.upload + '" class="button action" ' + self.selectors.uploadMapPinButton + ' />');
		$('[' + self.selectors.removeMapPinButton + ']').remove();
	}

	self.addMapPin = function(imageUrl)
	{
		var imagehtml = '<img src="' + imageUrl + '" id="map-pin-image" ' + self.selectors.mapPinImage + ' />';
			imagehtml += '<input id="remove_map_pin" type="button" value="' + wpsl_locator.remove + '" class="button action" style="margin-right:5px;margin-left:10px;" ' + self.selectors.removeMapPinButton + ' />';
			$('[' + self.selectors.mapPinContainer + ']').append(imagehtml);
			$('[' + self.selectors.uploadMapPinButton + ']').remove();
			$('[' + self.selectors.mapPinInput + ']').val(imageUrl);
	}

	self.openMediaLibrary = function(button)
	{
		if (self.mediaUploader){
			self.mediaUploader.open();
			return;
		}
		self.mediaUploader = wp.media.frames.file_frame = wp.media({
			title: 'Choose Map Pin',
			button: {
				text: 'Choose Map Pin'
			}, multiple: false 
		});
		self.mediaUploader.on('select', function(){
			var attachment = self.mediaUploader.state().get('selection').first().toJSON();
			self.addMapPin(attachment.url);
		});
		self.mediaUploader.open();
	}

	return self.bindEvents();
}