/**
* General Settings Page
* @package simple-locator
*/
var SimpleLocatorAdmin = SimpleLocatorAdmin || {};
SimpleLocatorAdmin.GeneralSettings = function()
{
	var self = this;
	var $ = jQuery;

	self.selectors = {
		geoEnableCheckbox : 'data-simple-locator-geo-enabled-checkbox',
		locationButtonText : 'data-simple-locator-location-button-text',
		uploadMapPinButton : 'data-simple-locator-upload-pin-button',
		removeMapPinButton : 'data-simple-locator-remove-pin-button',
		mapPinImage : 'data-simple-locator-map-pin-image',
		mapPinInput : 'data-simple-locator-map-pin-input',
		mapPinContainer : 'data-simple-locator-map-pin-image-container',
		userMapPin : 'data-simple-locator-user-map-pin',
		userPinCheckbox : 'data-simple-locator-toggle-user-pin'
	}

	self.bindEvents = function()
	{
		$(document).ready(function(){
			self.toggleLocationButtonText();
			self.toggleUserPin();
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
			self.removeMapPin($(this));
		});
		$(document).on('change', '[' + self.selectors.userPinCheckbox + ']', function(){
			self.toggleUserPin();
		});
	}

	self.toggleLocationButtonText = function()
	{
		var text = $('[' + self.selectors.locationButtonText + ']');
		if ( $('[' + self.selectors.geoEnableCheckbox + ']').is(':checked') ){
			if ( location.protocol !== 'https:' ){
				$('[data-simple-locator-no-https]').show();
			}
			$(text).show();
			return;
		}
		$('[data-simple-locator-no-https]').hide();
		$(text).hide();
	}

	self.removeMapPin = function(button)
	{
		var pinContainer = $(button).parent('td');
		var input = $(pinContainer).find('[' + self.selectors.mapPinInput + ']');
		$(pinContainer).find('[' + self.selectors.mapPinContainer + ']').html('');
		$(pinContainer).find('[' + self.selectors.mapPinInput + ']').val('');
		var buttonhtml ='<input id="upload_image_button" type="button" value="' + wpsl_locator.upload + '" class="button action" ' + self.selectors.uploadMapPinButton + ' />';
		$(buttonhtml).insertBefore(input);
		$(pinContainer).find('[' + self.selectors.removeMapPinButton + ']').remove();
	}

	self.addMapPin = function(imageUrl, button)
	{
		var pinContainer = $(button).parent('td');
		var imagehtml = '<img src="' + imageUrl + '" id="map-pin-image" ' + self.selectors.mapPinImage + ' />';
		var buttonhtml = '<input type="button" value="' + wpsl_locator.remove + '" class="button action" style="margin-right:5px;margin-left:10px;" ' + self.selectors.removeMapPinButton + ' />';
		$(pinContainer).find('[' + self.selectors.mapPinContainer + ']').html(imagehtml);
		var input = $(pinContainer).find('[' + self.selectors.mapPinInput + ']');
		$(buttonhtml).insertBefore(input);
		$(pinContainer).find('[' + self.selectors.uploadMapPinButton + ']').remove();
		$(pinContainer).find('[' + self.selectors.mapPinInput + ']').val(imageUrl);
	}

	self.openMediaLibrary = function(button)
	{
		var mediauploader;
		mediaUploader = wp.media.frames.file_frame = wp.media({
			title: 'Choose Map Pin',
			button: {
				text: 'Choose Map Pin'
			}, multiple: false 
		});
		mediaUploader.on('select', function(){
			var attachment = mediaUploader.state().get('selection').first().toJSON();
			self.addMapPin(attachment.url, button);
		});
		mediaUploader.open();
	}

	self.toggleUserPin = function()
	{
		var checked = $('[' + self.selectors.userPinCheckbox + ']').is(':checked');
		if ( checked ){
			$('[' + self.selectors.userMapPin + ']').show();
			return;
		}
		$('[' + self.selectors.userMapPin + ']').hide();
	}

	return self.bindEvents();
}