/**
* Modal Windows
* 
* @package Simple Locator
* @author Kyle Phillips - https://github.com/kylephillips
* 
* To use, include a modal backdrop and modal content window with the appropriate data-attributes
* The data attributes should match the value of the toggle buttons data-modal-toggle attribute
*/
var SimpleLocatorAdmin = SimpleLocatorAdmin || {};
SimpleLocatorAdmin.Modals = function()
{
	var plugin = this;
	var $ = jQuery;

	plugin.activeBtn = '';
	plugin.activeModal = '';
	plugin.modalOpen = false;

	plugin.selectors = {
		toggleBtn : '[data-modal-toggle]',
		backdrop : '[data-modal-backdrop]',
		closeBtn : '[data-modal-close]'
	}

	plugin.bindEvents = function()
	{
		$(document).on('click', plugin.selectors.toggleBtn, function(e){
			e.preventDefault();
			plugin.activeBtn = $(this);
			plugin.openModal();
		});
		$(document).on('click', plugin.selectors.closeBtn, function(e){
			e.preventDefault();
			plugin.closeModals();
		});
		$(document).on('open-modal-manual', function(e, modal){
			plugin.activeModal = $('*[data-modal="' + modal + '"]');
			plugin.openModal();
		});
		$(document).on('close-modal-manual', function(e){
			plugin.closeModals();
		});
		$(document).on('click', plugin.selectors.backdrop, function(e){
			plugin.closeModals();
		});
	}

	/**
	* Open the Modal Window
	*/
	plugin.openModal = function()
	{
		if ( plugin.modalOpen ){
			plugin.closeModals();
			return;
		}
		if ( $(plugin.activeBtn).length > 0 ){
			var modal = $(plugin.activeBtn).attr('data-modal-toggle');
			plugin.activeModal = $('*[data-modal="' + modal + '"]');
		}
		$(plugin.activeModal).addClass('active');
		plugin.modalOpen = true;
		$(document).trigger('open-modal', [plugin.activeBtn, plugin.activeModal]);
	}

	/**
	* Close the Modal Window
	*/
	plugin.closeModals = function()
	{
		plugin.modalOpen = false;
		$('[data-modal]').removeClass('active');
		$(document).trigger('close-modal', [plugin.activeBtn, plugin.activeModal]);
	}

	return plugin.bindEvents();
}