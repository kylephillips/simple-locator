/**
* Quick Edit Functionality
* @package simple-locator
*/
var SimpleLocatorAdmin = SimpleLocatorAdmin || {};
SimpleLocatorAdmin.QuickEdit = function()
{
	var self = this;
	var $ = jQuery;

	self.map = false;
	self.mapMarker = false;
	self.customMarkerPosition = {custom : false};

	self.fields = [
		['address', 'Address'],
		['address_two', 'Address Line 2'],
		['city', 'City'],
		['state', 'State'],
		['zip', 'Zip'],
		['country', 'Country'],
		['phone', 'Phone']
	];

	self.activeItem;
	self.formData;
	self.geocoded = false;

	self.selectors = {
		quickEditContainer : '.simple-locator-quick-edit',
		editLink : 'data-simple-locator-quick-edit',
		submitButton : 'data-simple-locator-quick-edit-submit',
		cancelButton : 'data-simple-locator-quick-edit-cancel',
		previewButton : 'data-simple-locator-quick-edit-preview',
		error : 'data-simple-locator-quick-edit-error',
		loading : '.simple-locator-quick-edit-spinner',
		map : 'data-simple-locator-quick-edit-map'
	}

	self.bindEvents = function()
	{
		$(document).on('click', '[' + self.selectors.editLink + ']', function(e){
			e.preventDefault();
			self.clearQuickEdit();
			self.activeItem = $(this);
			self.openForm();
		});
		$(document).on('click', '[' + self.selectors.cancelButton + ']', function(e){
			e.preventDefault();
			self.clearQuickEdit();
		});
		$(document).on('click', '[' + self.selectors.submitButton + ']', function(e){
			e.preventDefault();
			self.loading(true);
			self.prepareFormData(false);
		});
		$(document).on('simple-locator-quick-edit-complete', function(e, formdata, link, geocoded){
			self.completed(formdata, link, geocoded);
		});
		$(document).on('keyup', function(e){
			if ( e.key === 'Escape' && $(self.selectors.quickEditContainer).length > 0 ){
				self.clearQuickEdit();
			}
		});
		$(document).on('click', '[' + self.selectors.previewButton + ']', function(e){
			e.preventDefault();
			self.preview();
		});
	}

	/**
	* Create the form and open it
	*/
	self.openForm = function()
	{		
		var column_count = $(self.activeItem).parents('tr').find('td').length + $(self.activeItem).parents('tr').find('th').length;
		var title = $(self.activeItem).attr('data-title');
		var html = '<tr class="simple-locator-quick-edit"><td colspan="' + column_count + '">';
		html += '<h3>' + title + '</h3>';
		html += '<div class="simple-locator-quick-edit-alert" ' + self.selectors.error + '></div>';
		html += '<div class="inner">';
		html += '<div class="fields">';

		// Loop through the localized form field data and create form fields
		// Enables PHP filtering 
		for ( var i = 0; i < wpsl_locator.form_fields_order.length; i++ ){
			var key = wpsl_locator.form_fields_order[i];
			if ( key == 'map' || key == 'latlng' || key == 'additionalInfo' || key == 'website' ) continue;
			var field = wpsl_locator.form_fields[key];
			var value = $(self.activeItem).attr('data-' + key);

			html += '<div class="field"><label>' + field.label + '</label>';

			// Text Fields
			if ( field.type === 'text' ) {
				html += '<input type="text" ';
				html += self.attributes(field);
				html += 'value="' + value + '" />';
			}

			// Select Fields
			if ( field.type === 'select' ){
				html += '<select';
				html += self.attributes(field);
				html += '>';
				for ( var key in field.choices ){
					var choice_value = field.choices[key];
					html += '<option value="' + key + '"'
					if ( key === value ) html += ' selected';
					html += '>' + choice_value + '</option>';
				}
				html += '</select>';
			}

			html += '</div>';
		}

		html += '</div><!-- .fields -->';
		html += '<div class="map" data-simple-locator-quick-edit-map></div>';
		html += '</div><!-- .inner -->';
		html += '<div class="buttons">';
		html += '<button class="button" ' + self.selectors.cancelButton + '>' + wpsl_locator.cancel + '</button>';
		html += '<button class="button button-primary" ' + self.selectors.submitButton + '>' + wpsl_locator.save + '</button>';
		html += '<button class="button" ' + self.selectors.previewButton + '>' + wpsl_locator.preview + '</button>';
		html += '<div class="simple-locator-quick-edit-spinner"><div class="wpsl-icon-spinner-image"><img src="' + wpsl_locator.loading_spinner + '" class="wpsl-spinner-image" /></div></div>';
		html += '</div><!-- .buttons -->';
		html += '</td></tr><!-- .dealer-quick-edit -->';
		$(self.activeItem).parents('tr').hide();
		$(html).insertAfter($(self.activeItem).parents('tr'));
		self.loadMap(true);
	}

	/**
	* Load Map
	*/
	self.loadMap = function(initialLoad)
	{
		if ( initialLoad ){
			var latitude = $(self.activeItem).attr('data-latitude');
			var longitude = $(self.activeItem).attr('data-longitude');
		}
		if ( !initialLoad ){
			var latitude = self.formData.latitude;
			var longitude = self.formData.longitude;
		}

		var container = $('[' + self.selectors.map + ']');
		var mapstyles = wpsl_locator.mapstyles;	
		var mappin = ( wpsl_locator.mappin ) ? wpsl_locator.mappin : '';
		var position = new google.maps.LatLng(latitude, longitude);

		if ( !self.map ) {
			var mapOptions = {
				mapTypeId: 'roadmap',
				mapTypeControl: false,
				zoom: 12,
				styles: mapstyles,
				scrollwheel: false,
				panControl : false,
				center : position
			}
			self.map = new google.maps.Map( container[0], mapOptions );
		} else {
			self.marker.setMap(null);
			self.map.setCenter(position)
		}
		
		self.marker = new google.maps.Marker({
			position: position,
			map: self.map,
			icon: mappin,
			draggable: true
		});

		// Make Marker Draggable and update on change
		google.maps.event.addListener(self.marker, 'drag', function(){
			self.customMarkerPosition.latitude = self.marker.position.lat();
			self.customMarkerPosition.longitude = self.marker.position.lng();
			self.customMarkerPosition.custom = true;
		});
	}

	/**
	* Prepare the form data for submission
	*/
	self.prepareFormData = function(preview)
	{
		$('[' + self.selectors.error + ']').hide();
		self.formData = {
			action: 'wpslquickedit',
			id : $(self.activeItem).attr(self.selectors.editLink)
		}
		for ( var i = 0; i < self.fields.length; i++ ){
			var key = self.fields[i][0];
			self.formData[key] = $('[data-quickedit-' + key + ']').val();
		}
		self.geocodeAddress(preview);	
	}

	/**
	* Preview the address before saving
	*/
	self.preview = function()
	{
		self.initialLoad = false;
		self.prepareFormData(true);
	}

	/**
	* Geocode Address
	*/
	self.geocodeAddress = function(preview)
	{
		var address = '';
		for ( var i = 0; i < self.fields.length; i++ ){
			var key = self.fields[i][0];
			if ( key === 'address_two' ) continue;
			var value = self.formData[key];
			address += value + ' ';
		}
		geocoder = new google.maps.Geocoder();
		geocoder.geocode({
			'address' : address
		}, function(results, status){
			if ( status == google.maps.GeocoderStatus.OK ){
				self.formData.latitude = results[0].geometry.location.lat();
				self.formData.longitude = results[0].geometry.location.lng();
				self.geocoded = true;
			} else {
				self.geocoded = false;
			}
			if ( preview ) {
				self.loadMap(false);
				return;
			}
			self.submitForm();
		});
	}

	/**
	* Submit the Form to Save
	*/
	self.submitForm = function()
	{
		if ( self.customMarkerPosition.custom ){
			self.formData.latitude = self.customMarkerPosition.latitude;
			self.formData.longitude = self.customMarkerPosition.longitude;
		}
		self.formData.custom_geo = self.customMarkerPosition.custom;
		$.ajax({
			type: 'GET',
			url: ajaxurl,
			data: self.formData,
			success: function(data){
				self.loading(false);
				if ( data.status === 'error' ){
					$('[' + self.selectors.error + ']').text(data.message).show();
					return;
				}
				$(document).trigger('simple-locator-quick-edit-complete', [self.formData, self.activeItem, self.geocoded]);
			},
			error : function(data){
				console.log(data);
			}
		});
	}

	/**
	* Complete the Quick Edit
	*/
	self.completed = function(data, link, geocoded)
	{
		for ( var i = 0; i < self.fields.length; i++ ){
			var attribute = 'data-' + self.fields[i][0];
			var value = self.formData[self.fields[i][0]];
			$(link).attr(attribute, value);
		}

		if ( !geocoded ){
			$('[' + self.selectors.error + ']').text(wpsl_locator.quickedit_geocode_error).show();
			return;
		}

		$(link).attr('data-latitude', self.formData.latitude);
		$(link).attr('data-longitude', self.formData.longitude);
		
		var row = $(link).parents('tr').show();
		self.clearQuickEdit();
	}

	/**
	* Clear out of quick edit
	*/
	self.clearQuickEdit = function()
	{
		self.map = false;
		self.mapMarker = false;
		self.customMarkerPosition.custom = false;
		$('tr').show();
		$(self.selectors.quickEditContainer).remove();
	}

	/**
	* Add attributes to fields
	*/
	self.attributes = function(field)
	{
		html = '';
		if ( field.attributes.length < 1 ) return;
		for ( var i = 0; i < field.attributes.length; i++ ){
			var attr = field.attributes[i];
			if ( typeof attr === 'string'){
				html += ' ' + attr;
				continue;
			}
			html += ' ' + attr.attr + '="' + attr.value + '"';
		}
		return html;
	}

	/**
	* Toggle Loading
	*/
	self.loading = function(loading)
	{
		if ( loading ){
			$(self.selectors.loading).show();
			$('[' + self.selectors.submitButton + ']').attr('disabled', true);
			return;
		}
		$('[' + self.selectors.submitButton + ']').removeAttr('disabled');
		$(self.selectors.loading).hide();
	}

	return self.bindEvents();
}