/**
* The Primary Form Object
* @package simple-locator
*/
var SimpleLocator = SimpleLocator || {};
SimpleLocator.Form = function()
{
	var self = this;
	var $ = jQuery;

	self.activeForm;
	self.activeFormContainer;
	self.isWidget = false;
	self.mapContainer;
	self.resultsContainer;
	self.formData;
	self.isAjax = false;
	self.page = 0;

	self.bindEvents = function()
	{
		$(document).on('click', '[' + SimpleLocator.selectors.submitButton + ']', function(e){
			e.preventDefault();
			self.activeForm = $(this).parents('[' + SimpleLocator.selectors.form + ']');
			self.activeFormContainer = $(this).parents('[' + SimpleLocator.selectors.formContainer + ']');
			self.setAjax()
			active_form = self.activeForm; // Deprecated
			wpsl_before_submit(self.activeForm); // Deprecated
			$(document).trigger('simple-locator-before-submit', [self.activeForm]);
			self.processForm();
		});
		$(document).on('simple-locator-geolocation-success', function(e, form){
			self.activeForm = $(form);
			self.activeFormContainer = $(form).parents('[' + SimpleLocator.selectors.formContainer + ']');
			self.setAjax();
			wpsl_before_submit(self.activeForm); // Deprecated
			self.toggleLoading(true, true);
			$(document).trigger('simple-locator-before-submit', [self.activeForm]);
			self.setResultsContainers();
			self.setFormData();
			self.submitForm();
		});
		$(document).on('simple-locator-address-geocoded', function(e, results, form){
			self.toggleLoading(true, true);
			$(self.activeForm).find('[' + SimpleLocator.selectors.inputLatitude + ']').val(results.latitude);
			$(self.activeForm).find('[' + SimpleLocator.selectors.inputLongitude + ']').val(results.longitude);
			$(self.activeForm).find('[' + SimpleLocator.selectors.inputFormattedLocation + ']').val(results.formatted_address);
			self.setFormData();
			self.submitForm();
		});
		$(document).on('click', '[' + SimpleLocator.selectors.paginationButton + ']', function(e){
			if ( !self.activeForm ) return;
			e.preventDefault();
			$(self.activeFormContainer).addClass('loading');
			self.paginate($(this));
		});
		$(document).on('simple-locator-autocomplete-changed', function(e, place, form){
			self.activeForm = $(form);
			self.activeFormContainer = $(form).parents('[' + SimpleLocator.selectors.formContainer + ']');
			self.setAjax();
			self.toggleLoading(true, true);
			$(self.activeForm).find('[' + SimpleLocator.selectors.inputLatitude + ']').val(place.geometry.location.lat());
			$(self.activeForm).find('[' + SimpleLocator.selectors.inputLongitude + ']').val(place.geometry.location.lng());
			$(self.activeForm).find('[' + SimpleLocator.selectors.inputFormattedLocation + ']').val(place.formatted_address);
			if ( self.page > 0 ) self.page = 0;
			self.setFormData();
			self.submitForm();
		});
	}

	/**
	* Set whether the active form is ajax or not
	*/
	self.setAjax = function()
	{
		var ajax = $(self.activeForm).attr(SimpleLocator.selectors.ajaxForm);
		self.isAjax = ( typeof ajax === 'undefined' || ajax === '' ) ? false : true;
	}

	/**
	* Process the form submission
	*/
	self.processForm = function(geocode)
	{
		self.toggleLoading(true, true);
		self.setResultsContainers();
		var geocoder = new SimpleLocator.Geocoder();
		geocoder.getCoordinates(self.activeForm);
	}

	/**
	* Set the appropriate containers for results
	*/
	self.setResultsContainers = function()
	{
		if ( $(self.activeForm).siblings('#widget').length > 0 ) self.isWidget = true;	
		if ( typeof wpsl_locator_options === 'undefined' || wpsl_locator_options === '' ) wpsl_locator_options = '';
		self.mapContainer = ( wpsl_locator_options.mapcont === '' || self.isWidget )
			? $(self.activeFormContainer).find('[' + SimpleLocator.selectors.map + ']')
			: $(wpsl_locator_options.mapcont);
		
		self.resultsContainer = ( wpsl_locator_options.resultscontainer === '' || self.isWidget )
			? (self.activeFormContainer).find('[' + SimpleLocator.selectors.results + ']')
			: $(wpsl_locator_options.resultscontainer);
		return;
	}

	/**
	* Set the form data for processing
	*/
	self.setFormData = function(geocode_results)
	{
		var allow_empty_address = $(self.activeForm).attr('data-simple-locator-form-allow-empty');
		allow_empty_address = ( typeof allow_empty_address === 'undefined' || allow_empty_address === '' ) ? false : true;

		var address = $(self.activeForm).find('[' + SimpleLocator.selectors.inputAddress + ']');
		address = ( typeof address === 'undefined' ) ? false : $(address).val();

		var distance = $(self.activeForm).find('[' + SimpleLocator.selectors.inputDistance + ']');
		distance = ( typeof distance === 'undefined' ) ? false : $(distance).val();

		var geolocation = $(self.activeForm).find('[' + SimpleLocator.selectors.inputGeocode + ']').val();
		geolocation = ( geolocation === '' || geolocation === 'false' ) ? false : true;	

		var limit = $(self.activeForm).find('[' + SimpleLocator.selectors.inputLimit + ']').val();
		limit = ( limit === '' ) ? null : limit;

		self.formData = {
			address : address,
			formatted_address : $(self.activeForm).find('[' + SimpleLocator.selectors.inputFormattedLocation + ']').val(),
			distance : distance,
			latitude : $(self.activeForm).find('[' + SimpleLocator.selectors.inputLatitude + ']').val(),
			longitude :  $(self.activeForm).find('[' + SimpleLocator.selectors.inputLongitude + ']').val(),
			unit : $(self.activeForm).find('[' + SimpleLocator.selectors.inputUnit + ']').val(),
			geolocation : geolocation,
			allow_empty_address : allow_empty_address,
			ajax : self.isAjax,
			per_page : limit
		}

		self.setTaxonomies();

		// Custom Input Data (for SQL filter availability)
		if ( wpsl_locator.postfields.length == 0 ) return
		for ( var i = 0; i < wpsl_locator.postfields.length; i++ ){
			var field = wpsl_locator.postfields[i];
			formdata[field] = $('input[name=' + field + ']').val();
		}
	}

	/**
	* Set taxonomies in the form data if applicable
	*/
	self.setTaxonomies = function()
	{
		var taxonomyCheckboxes = $(self.activeForm).find('input[name^="taxonomy"]:checked');
		var taxonomySelect = $(self.activeForm).find('select[name^="taxonomy"]')

		var taxonomies = ( taxonomyCheckboxes.length > 0 ) ? $(taxonomyCheckboxes).serializeArray() : [];
	
		// Select Menus
		$.each(taxonomySelect, function(i, v){
			if ( $(this).val() === "" ) return;
			var selected = {};
			selected.name = $(this).attr('name');
			selected.value = $(this).val();
			taxonomies.push(selected);
		});
		
		// // Create an array from the selected taxonomies
		var taxonomy_array = {};
		$.each(taxonomies, function(i, v){
			var tax_name = this.name.replace( /(^.*\[|\].*$)/g, '' );
			if ( (typeof taxonomy_array[tax_name] == undefined) 
				|| !(taxonomy_array[tax_name] instanceof Array) ) 
				taxonomy_array[tax_name] = [];
			if ( tax_name) taxonomy_array[tax_name].push(this.value);
		});

		self.formData.taxonomies = taxonomy_array;
	}

	/**
	* Submit the form
	*/
	self.submitForm = function()
	{
		if ( !self.formData.ajax ) {
			$(self.activeForm).submit();
			return;
		}
		self.formData.page = self.page;
		$.ajax({
			url : SimpleLocator.endpoints.search,
			type: 'GET',
			datatype: 'jsonp',
			data: self.formData,
			success: function(data){
				if ( wpsl_locator.jsdebug === '1' ){
					console.log('Form Response');
					console.log(data);
				}
				if (data.status === 'error'){
					$(document).trigger('simple-locator-error', [self.activeForm, data.message]);
					self.toggleLoading(false, true);
					return;
				}
				if ( data.result_count === 0 ){
					var message = wpsl_locator.nolocationserror + ' ' + data.formatted_address;
					$(document).trigger('simple-locator-error', [self.activeForm, message]);
					wpsl_no_results(self.formData.formatted_address, self.activeForm); // Deprecated
					self.toggleLoading(false, true);
					return;
				}
				$(document).trigger('simple-locator-form-success', [data, self.activeForm]);
				wpsl_success(data.result_count, data.results, self.activeForm); // Deprecated
			},
			error: function(data){
				if ( wpsl_locator.jsdebug === '1' ){
					console.log('Form Response Error');
					console.log(data.responseText);
				}
			}
		});
	}

	/**
	* Pagination Action
	*/
	self.paginate = function(button)
	{
		var direction = $(button).attr(SimpleLocator.selectors.paginationButton);
		if ( direction === 'next' ){
			self.page = self.page + 1;
			self.submitForm();
			return;
		}
		self.page = self.page - 1;
		self.submitForm();
	}

	/**
	* Toggle Loading
	*/
	self.toggleLoading = function(loading, clearvalues)
	{
		var results = $(self.activeFormContainer).find('[' + SimpleLocator.selectors.results + ']');
		if ( loading ){
			if ( clearvalues ){
				$('[' + SimpleLocator.selectors.inputLatitude + ']').val('');
				$('[' + SimpleLocator.selectors.inputLongitude + ']').val('');
				$('[' + SimpleLocator.selectors.inputGeocode + ']').val('');
				$('[' + SimpleLocator.selectors.inputFormattedLocation + ']').val('');
			}
			$(self.activeFormContainer).addClass('loading');
			$(self.activeFormContainer).find('[' + SimpleLocator.selectors.formError + ']').hide();
			$(results).empty();
			return;
		}
		$(self.activeFormContainer).removeClass('loading');
	}

	return self.bindEvents();
}