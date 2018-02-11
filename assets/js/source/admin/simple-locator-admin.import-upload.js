/**
* Import Functionality - Upload/Page One
* @package simple-locator
*/
var SimpleLocatorAdmin = SimpleLocatorAdmin || {};
SimpleLocatorAdmin.ImportUpload = function()
{
	var self = this;
	var $ = jQuery;

	self.selectors = {
		startNewBtn : 'data-simple-locator-import-start-new',
		uploadForm : 'data-simple-locator-import-upload-form',
		previousImportMessage : 'data-simple-locator-import-previous-message',
		postTypeInput : 'data-simple-locator-import-post-type-input',
		toggleHiddenPostTypeCheckbox : 'data-simple-locator-show-non-public-types',
		nonPublicPostType : 'data-non-public-post-type',
		toggleImportDetails : 'data-import-toggle-details',
		undoImportButton : 'data-simple-locator-import-undo-button'
	}

	self.bindEvents = function()
	{
		$(document).ready(function(){
			self.togglePublicPostTypes();
		});	
		$(document).on('change', '[' + self.selectors.toggleHiddenPostTypeCheckbox + ']', function(){
			self.togglePublicPostTypes();
		});
		$(document).on('click', '[' + self.selectors.startNewBtn + ']', function(e){
			e.preventDefault();
			self.startNewImport();
		});
		$(document).on('click', '[' + self.selectors.toggleImportDetails + ']', function(e){
			e.preventDefault();
			self.toggleImportDetails($(this));
		});
		$(document).on('click', '[' + self.selectors.undoImportButton + ']', function(e){
			e.preventDefault();
			var id = $(this).attr(self.selectors.undoImportButton);
			self.undoImport(id);
		});
	}

	/**
	* Cancel a previous import and start new
	*/
	self.startNewImport = function()
	{
		$('[' + self.selectors.uploadForm + ']').show();
		$('[' + self.selectors.previousImportMessage + ']').hide();
	}

	/**
	* Toggle non-public post types in the post type field
	*/
	self.togglePublicPostTypes = function()
	{
		var nonPublic = $('[' + self.selectors.nonPublicPostType + ']');
		var checked = ( $('[' + self.selectors.toggleHiddenPostTypeCheckbox + ']').is(':checked') );
		if ( checked ){
			$(nonPublic).show();
			return;
		}
		$(nonPublic).hide();
	}

	/**
	* Toggle previous import details
	*/
	self.toggleImportDetails = function(button)
	{
		$(button).parents('.import').find('.import-body').toggle();
	}

	/**
	* Undo a previous import
	*/
	self.undoImport = function(id)
	{
		if ( !confirm(wpsl_locator.confirm_undo) ) return;
		$('#undo_import_id').val(id);
		$('[data-undo-import-form]').submit();
	}

	return self.bindEvents();
}