/**
* Quick Edit Functionality
* @package simple-locator
*/
var SimpleLocatorAdmin = SimpleLocatorAdmin || {};
SimpleLocatorAdmin.QuickEdit = function()
{
	var self = this;
	var $ = jQuery;

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
		error : 'data-simple-locator-quick-edit-error',
		loading : '.simple-locator-quick-edit-spinner'
	}

	self.bindEvents = function()
	{
		$(document).on('click', '[' + self.selectors.editLink + ']', function(e){
			e.preventDefault();
			self.activeItem = $(this);
			self.openForm();
		});
		$(document).on('click', '[' + self.selectors.cancelButton + ']', function(e){
			e.preventDefault();
			$(self.selectors.quickEditContainer).remove();
			$('tr').show();
		});
		$(document).on('click', '[' + self.selectors.submitButton + ']', function(e){
			e.preventDefault();
			self.loading(true);
			self.prepareFormData();
		});
		$(document).on('simple-locator-quick-edit-complete', function(e, formdata, link, geocoded){
			self.completed(formdata, link, geocoded);
		});
		$(document).on('keyup', function(e){
			if ( e.key === 'Escape' && $(self.selectors.quickEditContainer).length > 0 ){
				$(self.selectors.quickEditContainer).remove();
				$('tr').show();
			}
		});
	}

	/**
	* Create the form and open it
	*/
	self.openForm = function()
	{
		$(self.selectors.quickEditContainer).remove();
		$('tr').show();

		var title = $(self.activeItem).attr('data-title');
		var html = '<tr class="simple-locator-quick-edit"><td colspan="4">';
		html += '<h3>' + title + '</h3>';
		html += '<div class="simple-locator-quick-edit-alert" ' + self.selectors.error + '></div>';
		html += '<div class="fields">';
		for ( var i = 0; i < self.fields.length; i++ ){
			var key = self.fields[i][0];
			var value = $(self.activeItem).attr('data-' + key);
			html += '<div class="field"><label>' + self.fields[i][1] + '</label><input type="text" data-quickedit-' + key + ' value="' + value + '" /></div>';
		}
		html += '</div><!-- .fields -->';
		html += '<div class="buttons">';
		html += '<button class="button" ' + self.selectors.cancelButton + '>' + wpsl_locator.cancel + '</button>';
		html += '<button class="button button-primary" ' + self.selectors.submitButton + '>' + wpsl_locator.save + '</button>';
		html += '<div class="simple-locator-quick-edit-spinner"><div class="wpsl-icon-spinner-image"><img src="' + wpsl_locator.loading_spinner + '" class="wpsl-spinner-image" /></div></div>';
		html += '</div><!-- .buttons -->';
		html += '</td></tr><!-- .dealer-quick-edit -->';
		$(self.activeItem).parents('tr').hide();
		$(html).insertAfter($(self.activeItem).parents('tr'));
	}

	/**
	* Prepare the form data for submission
	*/
	self.prepareFormData = function()
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
		self.geocodeAddress();	
	}

	/**
	* Geocode Address
	*/
	self.geocodeAddress = function()
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
			self.submitForm();
		});
	}

	/**
	* Submit the Form to Save
	*/
	self.submitForm = function()
	{
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
		var row = $(link).parents('tr').show();
		$(self.selectors.quickEditContainer).remove();
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