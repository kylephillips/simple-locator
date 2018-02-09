/**
* Geocode an Address
* @package simple-locator
*/
var SimpleLocator = SimpleLocator || {};
SimpleLocator.Geocoder = function()
{
	var self = this;
	var $ = jQuery;

	self.form;
	self.searchTerm;
	self.results = [];

	/**
	* Get coordinates and formatted for a provided address
	* @param address string
	* @param form object
	* @return array
	*/
	self.getCoordinates = function(form)
	{
		self.form = form;
		self.searchTerm = $(self.form).find('[' + SimpleLocator.selectors.inputAddress + ']').val();
		if ( SimpleLocator.mapservice === 'google' ) self.queryGoogleMaps();
	}

	/**
	* Query Google
	*/
	self.queryGoogleMaps = function()
	{
		geocoder = new google.maps.Geocoder();
		geocoder.geocode({
			'address' : self.searchTerm
		}, function(results, status){
			if ( status == google.maps.GeocoderStatus.OK ){
				googlemaps_response = results; // deprecated
				self.results['latitude'] = results[0].geometry.location.lat();
				self.results['longitude'] = results[0].geometry.location.lng();
				self.results['formatted_address'] = results[0].formatted_address;
				if ( wpsl_locator.jsdebug === '1' ){
					console.log('Google Geocode Response');
					console.log(self.results);
				}
				$(document).trigger('simple-locator-address-geocoded', [self.results, self.form]);
			} else {
				$(document).trigger('simple-locator-error', [self.form, wpsl_locator.notfounderror]);
				self.removeLoading();
			}
		});
	}

	/**
	* Remove Loading
	*/
	self.removeLoading = function()
	{
		$(self.form).parents('[' + SimpleLocator.selectors.formContainer + ']').removeClass('loading');
	}
}