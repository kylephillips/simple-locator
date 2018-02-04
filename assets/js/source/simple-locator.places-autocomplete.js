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
			var autocomplete = new google.maps.places.Autocomplete(this);
			var submitBtn = $(this).parents('[' + SimpleLocator.selectors.form + ']').find('[' + SimpleLocator.selectors.submitButton + ']');
			google.maps.event.addListener(autocomplete, 'place_changed', function(){
				$(submitBtn).click();
			});
		});
	}
	
	return self.bindEvents();
}