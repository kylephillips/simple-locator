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
			wpsl_error(message, self.form);
		});
	}

	self.toggleError = function(message)
	{
		$(self.formContainer).find('[' + SimpleLocator.selectors.results + ']').hide();
		$(self.formContainer).find('[' + SimpleLocator.selectors.map + ']').addClass('loading');
		$(self.formContainer).find('[' + SimpleLocator.selectors.formError + ']').text(message).show();
	}

	return self.bindEvents();
}