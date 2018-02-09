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
			self.setGeolocationAvailable();
		});
		$(document).on('simple-locator-geolocation-available-set', function(){
			if ( !self.geolocationAvailable ) return;
			self.appendButton();
			self.setDefaultUserLocation();
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
	* If the default map is enabled with user-centered location, get the user's location
	*/
	self.setDefaultUserLocation = function()
	{
		if ( wpsl_locator.default_user_center !== 'true' ) return;
		var defaultMap = $('[data-simple-locator-default-enabled]');
		self.activeFormContainer = $(defaultMap).parents('[' + SimpleLocator.selectors.formContainer + ']');
		self.activeForm = $(self.activeFormContainer).find('[' + SimpleLocator.selectors.form + ']');
		$(self.activeFormContainer).addClass('loading');
		self.getLocation();
	}

	/**
	* Set whether or not geolocation is available
	*/
	self.setGeolocationAvailable = function()
	{
		if ( wpsl_locator.showgeobutton !== 'true' ) return;
		$('[' + SimpleLocator.selectors.formContainer + ']').removeClass('no-geolocation');
		if ( !navigator.geolocation ) return false;
		navigator.permissions.query({name:'geolocation'}).then(function(permissionStatus){  
    		if ( permissionStatus.state == 'denied' ) {
    			self.geolocationAvailable = false;
    			$('[' + SimpleLocator.selectors.formContainer + ']').addClass('no-geolocation');
    		}
    		$(document).trigger('simple-locator-geolocation-available-set', [self.geolocationAvailable]);
    	});
	}

	/**
	* Returns user location if available, null if not
	*/
	self.getLocation = function()
	{
		navigator.geolocation.getCurrentPosition(function(position){
			var results = {
				latitude : position.coords.latitude,
				longitude : position.coords.longitude
			};
			$(self.activeFormContainer).addClass('has-geolocation');
			$(document).trigger('simple-locator-geolocation-success', [self.activeForm, results]);
		},
		function(error){
			$(document).trigger('simple-locator-error', [self.activeForm, 'Your location could not be determined']);
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
			$(self.activeFormContainer).addClass('loading');
			$(self.activeFormContainer).find('[' + SimpleLocator.selectors.formError + ']').hide();
			return;
		}
		$(self.activeFormContainer).removeClass('loading');
	}

	return self.bindEvents();

}