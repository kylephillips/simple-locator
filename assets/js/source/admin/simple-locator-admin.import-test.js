/**
* Test that the Google API key has the geocode service enabled
* @package simple-locator
*/
var SimpleLocatorAdmin = SimpleLocatorAdmin || {};
SimpleLocatorAdmin.ImportTest = function()
{
	var self = this;
	var $ = jQuery;

	self.selectors = {
		button : 'data-simple-locator-import-test-button',
		message : 'data-simple-locator-import-test-message'
	}

	self.bindEvents = function()
	{
		$(document).on('click', '[' + self.selectors.button+ ']', function(e){
			e.preventDefault();
			self.test();
		})
	}

	/**
	* Test
	*/
	self.test = function()
	{
		$.ajax({
			url: ajaxurl,
			type: 'post',
			datatype: 'json',
			data: {
				action: 'wpslimporttest',
				nonce: wpsl_locator.locatorNonce
			},
			success: function(data){
				if ( data.status === 'testing' ){
					console.log(data);
					return;
				}
				if ( data.status === 'success' ){
					$('[' + self.selectors.message + ']').text(data.message).addClass('wpsl-alert');
					return;
				}
				if (data.status === 'error') {
					$('[' + self.selectors.message + ']').text(data.message).addClass('wpsl-error');
					return;
				}
			}
		});
	}


	return self.bindEvents();
}