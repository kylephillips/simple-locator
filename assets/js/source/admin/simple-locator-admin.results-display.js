/**
* Results Display Settings Page
* @package simple-locator
*/
var SimpleLocatorAdmin = SimpleLocatorAdmin || {};
SimpleLocatorAdmin.ResultsDisplay = function()
{
	var self = this;
	var $ = jQuery;

	self.bindEvents = function()
	{
		$(document).ready(function(){
			self.enableDatePicker();
		});
		$(document).on('click', '.wpsl-field-add', function(e){
			e.preventDefault();
			self.addResultField();
		});
		$(document).on('click', '.wpsl-post-field-add', function(e){
			e.preventDefault();
			self.addPostField();
		});
	}

	self.addResultField = function()
	{
		var field = '[' + $('#wpsl-fields').val() + ']';
		tinymce.activeEditor.execCommand('mceInsertContent', false, field);
	}

	self.addPostField = function()
	{
		var field = '[' + $('#wpsl-post-fields').val() + ']';
		tinymce.activeEditor.execCommand('mceInsertContent', false, field);
	}

	self.enableDatePicker = function()
	{
		$('[data-date-picker]').datepicker({
			beforeShow: function(input, inst){
				$('#ui-datepicker-div').addClass('wpsl-datepicker');
			}
		});
	}

	return self.bindEvents();
}