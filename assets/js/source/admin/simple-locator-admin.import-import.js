/**
* Import Functionality - The Import (Last Step)
* @package simple-locator
*/
var SimpleLocatorAdmin = SimpleLocatorAdmin || {};
SimpleLocatorAdmin.Import = function()
{
	var self = this;
	var $ = jQuery;

	self.selectors = {
		startButton : 'data-simple-locator-import-start-button',
		intro : 'data-simple-locator-import-intro',
		lastImported : 'data-simple-locator-import-last-imported',
		progressMessage : 'data-simple-locator-import-progress-message',
		progressBar : 'data-simple-locator-import-progress-bar'
	}

	self.bindEvents = function()
	{
		$(document).on('click', '[' + self.selectors.startButton + ']', function(e){
			e.preventDefault();
			self.startImport()
		})
	}

	self.startImport = function()
	{
		$('[' + self.selectors.intro + '],[' + self.selectors.lastImported + ']').hide();
		$('[' + self.selectors.progressMessage + ']').show();
	}

	return self.bindEvents();
}