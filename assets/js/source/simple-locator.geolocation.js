/**
* Gets the user's current location if available
* @package simple-locator
*/
var SimpleLocator = SimpleLocator || {};
SimpleLocator.Geolocation = function()
{
	var self = this;
	var $ = jQuery;

	self.geolocationAvailable = true;
	self.activeForm;
	self.activeFormContainer;

	self.bindEvents = function()
	{
		$(document).ready(function(){
			if ( wpsl_locator.showgeobutton !== 'true' ) return false;
			self.setGeolocationAvailable();
		});
		$(document).on('simple-locator-geolocation-available-set', function(){
			if ( !self.geolocationAvailable ) return;
			self.appendButton();
		});
		$(document).on('click', '[' + SimpleLocator.selectors.geoButton + ']', function(e){
			e.preventDefault();
			self.activeForm = $(this).parents('[' + SimpleLocator.selectors.form + ']');
			self.activeFormContainer = $(this).parents('[' + SimpleLocator.selectors.formContainer + ']');
			self.toggleLoading(true);
			self.getLocation();
		});
	}

	/**
	* Append the Geolocation Button if available
	*/
	self.appendButton = function()
	{
		var html = '<button class="wpsl-geo-button" ' + SimpleLocator.selectors.geoButton + '>' + wpsl_locator.geobuttontext + '</button>';
			$('.geo_button_cont').html(html);
	}

	/**
	* Set whether or not geolocation is available
	*/
	self.setGeolocationAvailable = function()
	{
		if ( !navigator.geolocation ) return available;
		navigator.permissions.query({name:'geolocation'}).then(function(permissionStatus){  
    		if ( permissionStatus.state == 'denied' ) self.geolocationAvailable = false;
    		$(document).trigger('simple-locator-geolocation-available-set');
    	});
	}

	/**
	* Returns user location if available, null if not
	*/
	self.getLocation = function()
	{
		navigator.geolocation.getCurrentPosition(function(position){
			$(self.activeForm).find('[' + SimpleLocator.selectors.inputLatitude + ']').val(position.coords.latitude);
			$(self.activeForm).find('[' + SimpleLocator.selectors.inputLongitude + ']').val(position.coords.longitude);
			$(self.activeForm).find('[' + SimpleLocator.selectors.inputGeocode + ']').val('1');
			$(document).trigger('simple-locator-geolocation-success', [self.activeForm]);
		},
		function(error){
			self.toggleLoading(false);
		});
	}

	/**
	* Toggle Loading on the form
	*/
	self.toggleLoading = function(loading)
	{
		if ( loading ){
			$('[' + SimpleLocator.selectors.inputAddress + ']').val('');
			$('[' + SimpleLocator.selectors.inputLatitude + ']').val('');
			$('[' + SimpleLocator.selectors.inputLongitude + ']').val('');
			$('[' + SimpleLocator.selectors.inputGeocode + ']').val('');
			$('[' + SimpleLocator.selectors.inputFormattedLocation + ']').val('');
			$(self.activeFormContainer).find('[' + SimpleLocator.selectors.formError + ']').hide();
			$(self.activeFormContainer).find('[' + SimpleLocator.selectors.results + ']').empty().addClass('loading').show();
			return;
		}
		$(self.activeFormContainer).find('[' + SimpleLocator.selectors.results + ']').removeClass('loading').hide();
	}

	return self.bindEvents();

}