/**
* Load and Delete Export Templates
* @package simple-locator
*/
var SimpleLocatorAdmin = SimpleLocatorAdmin || {};
SimpleLocatorAdmin.ExportTemplates = function()
{
	var self = this;
	var $ = jQuery;

	self.selectors = {
		loadBtn : 'data-wpsl-export-template-load',
		deleteBtn : 'data-wpsl-export-template-delete'
	}

	self.bindEvents = function()
	{
		$(document).on('click', '[' + self.selectors.loadBtn + ']', function(e){
			e.preventDefault();
			self.loadTemplate($(this));
		});
		$(document).on('click', '[' + self.selectors.deleteBtn + ']', function(e){
			e.preventDefault();
			self.deleteTemplate($(this));
		});
	}

	self.loadTemplate = function(button)
	{
		var templateKey = $(button).attr(self.selectors.loadBtn);
		$.ajax({
			type: 'GET',
			url: ajaxurl,
			data: {
				action: 'wpslexporttemplates',
				nonce: wpsl_locator.locatorNonce,
				templateKey: templateKey
			},
			success: function(data){
				self.populateTemplate(data.templates);
			}
		});
	}

	self.populateTemplate = function(template)
	{
		var standard_fields = template.standard_columns;
		var custom_fields = template.custom_columns;
		var include_header = template.include_header;
		var template_name = template.name;
		var column_names = template.column_names;

		$('input[name="standard_columns[]"]').removeAttr('checked');
		for ( var i = 0; i < standard_fields.length; i++ ){
			var checkbox = $('input[name="standard_columns[]"][value="' + standard_fields[i] + '"]');
			$(checkbox).attr('checked', true);
		}

		$('input[name="custom_columns[]"]').removeAttr('checked');
		for ( var i = 0; i < custom_fields.length; i++ ){
			var checkbox = $('input[name="custom_columns[]"][value="' + custom_fields[i] + '"]');
			$(checkbox).attr('checked', true);
		}

		for ( var index in column_names ){
			$('input[name="column_name[' + index + ']').val(column_names[index]);
		}

		if ( include_header ){
			$('input[name="include_header_row"]').attr('checked', true);
		} else {
			$('input[name="include_header_row"]').removeAttr('checked');
		}

		var filename = template.filename.replace('.csv', '');
		$('input[name="file_name"]').val(filename);
	}

	self.deleteTemplate = function(button)
	{
		var templateKey = $(button).attr(self.selectors.deleteBtn);
		$.ajax({
			type: 'GET',
			url: ajaxurl,
			data: {
				action: 'wpslexporttemplatedelete',
				nonce: wpsl_locator.locatorNonce,
				templateKey: templateKey
			},
			success: function(data){
				$(button).parent('li').fadeOut('normal', function(){
					$(this).remove();
				});
			}
		});
	}

	return self.bindEvents();
}