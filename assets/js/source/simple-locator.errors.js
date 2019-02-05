/**
* Error Handling
* @package simple-locator
*/
var SimpleLocator = SimpleLocator || {};
SimpleLocator.Errors = function()
{
	var self = this;
	var $ = jQuery;

	self.error;
	self.form;
	self.formContainer;

	self.bindEvents = function()
	{
		$(document).on('simple-locator-error', function(e, form, message){
			self.form = form;
			self.formContainer = $(form).parents('[' + SimpleLocator.selectors.formContainer + ']');
			self.toggleError(message);
			$(document).trigger('simple-locator-error-message', [form, message]);
			self.clearMap();
			wpsl_error(message, self.form);
		});
	}

	self.toggleError = function(message)
	{
		$(self.formContainer).removeClass('loading').addClass('has-error');
		$(self.formContainer).find('[' + SimpleLocator.selectors.formError + ']').text(message).show();
	}

	/**
	* Remove all the markers from the map
	*/
	self.clearMap = function()
	{
		var wrappers = $('[' + SimpleLocator.selectors.resultsWrapper + ']');
		var mapIndex = $(self.formContainer).index(wrappers);
		if ( !SimpleLocator.markers[mapIndex] ) SimpleLocator.markers[mapIndex] = [];
		for (var i = 0; i < SimpleLocator.markers[mapIndex].length; i++){
			SimpleLocator.markers[mapIndex][i].setMap(null);
		}
		SimpleLocator.markers[mapIndex] = [];
		if ( wpsl_locator.includeuserpin === '' ) return;
		if ( !SimpleLocator.userPin[mapIndex] ) return;
		SimpleLocator.userPin[mapIndex].setMap(null);
		SimpleLocator.userPin[mapIndex] = null;
	}

	return self.bindEvents();
}