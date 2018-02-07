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
			var form = $(this).parents('form');
			var ajax = $(form).attr(SimpleLocator.selectors.ajaxForm);
			ajax = ( typeof ajax === 'undefined' || ajax === '' ) ? false : true;
			if ( !ajax ) return;
			google.maps.event.addListener(autocomplete, 'place_changed', function(){
				$(submitBtn).click();
			});
		});
	}
	
	return self.bindEvents();
}