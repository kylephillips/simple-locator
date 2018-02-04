/**
* Geocode an Address
* @package simple-locator
*/
var SimpleLocator = SimpleLocator || {};
SimpleLocator.Geocoder = function()
{
	var self = this;
	var $ = jQuery;

	/**
	* Get coordinates and formatted for a provided address
	* @param address string
	* @param form object
	* @return array
	*/
	self.getCoordinates = function(form)
	{
		var searchTerm = $(form).find('[' + SimpleLocator.selectors.inputAddress + ']').val();
		var searchResults = [];
		geocoder = new google.maps.Geocoder();
		geocoder.geocode({
			'address' : searchTerm
		}, function(results, status){
			if ( status == google.maps.GeocoderStatus.OK ){
				googlemaps_response = results;
				searchResults['latitude'] = results[0].geometry.location.lat();
				searchResults['longitude'] = results[0].geometry.location.lng();
				searchResults['formatted_address'] = results[0].formatted_address;
				if ( wpsl_locator.jsdebug === '1' ){
					console.log('Google Geocode Response');
					console.log(results);
				}
				$(document).trigger('simple-locator-address-geocoded', [searchResults, form]);
			} else {
				$(document).trigger('simple-locator-error', ['geocode-error', form, wpsl_locator.notfounderror]);
			}
		});
	}
}