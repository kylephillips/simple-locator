/**
* Enable Google Maps Places Autocomplete on Fields
* @package simple-locator
*/
var SimpleLocator = SimpleLocator || {};
SimpleLocator.PlacesAutocomplete = function()
{
	var self = this;
	var $ = jQuery;

	self.selectors = {
		input : 'data-simple-locator-autocomplete'
	}

	self.bindEvents = function()
	{
		$(document).ready(function(){
			self.enableAutocomplete();
		});
	}

	self.enableAutocomplete = function()
	{
		var inputs = $('[' + self.selectors.input + ']');
		$.each(inputs, function(i, v){
			var options = {};
			if ( wpsl_locator.custom_autocomplete )	options = wpsl_locator.autocomplete_options;
			var autocomplete = new google.maps.places.Autocomplete(this, options);
			var submitBtn = $(this).parents('[' + SimpleLocator.selectors.form + ']').find('[' + SimpleLocator.selectors.submitButton + ']');
			var form = $(this).parents('form');
			google.maps.event.addListener(autocomplete, 'place_changed', function(){
				$(document).trigger('simple-locator-autocomplete-changed', [autocomplete.getPlace(), form]);
			});
		});
	}
	
	return self.bindEvents();
}