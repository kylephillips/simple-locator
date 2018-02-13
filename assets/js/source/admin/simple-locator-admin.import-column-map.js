/**
* Import Functionality - Column Mapping
* @package simple-locator
*/
var SimpleLocatorAdmin = SimpleLocatorAdmin || {};
SimpleLocatorAdmin.ColumnMap = function()
{
	var self = this;
	var $ = jQuery;

	self.totalrows = 0;
	self.rowcount = 0; // for selecting a specific row from csv

	self.selectors = {
		loadingIndicator : 'data-simple-locator-import-loading',
		columnSelection : 'data-simple-locator-import-column-selection',
		rowSelectionButton : 'data-simple-locator-import-row-selection-button',
		rowSelectionButtonNext : '[data-simple-locator-import-row-selection-button="next"]',
		rowSelectionButtonBack : '[data-simple-locator-import-row-selection-button="back"]',
		currentRowText : 'data-simple-locator-import-current-row',
		postType : 'data-simple-locator-import-post-type',
		selectColumn : 'data-simple-locator-import-column-select',
		selectField : 'data-simple-locator-import-field-select',
		selectType : 'data-simple-locator-import-type-select',
		addFieldButton : 'data-simple-locator-import-add-field',
		removeFieldButton : 'data-simple-locator-import-remove-field',
		saveButton : 'data-simple-locator-import-save',
		formError : 'data-simple-locator-form-error',
		columnError : 'data-simple-locator-column-error',
		fieldRow : 'data-simple-locator-import-field',
		form : 'data-simple-locator-import-column-form'
	}

	self.bindEvents = function()
	{
		$(document).ready(function(){
			if ( wpsl_locator.isimport === "true" && wpsl_locator.importstep === "2" ) {
				self.getColumns();
				self.getPostTypeFields();
			}
		});
		$('[' + self.selectors.rowSelectionButton + ']').on('click', function(e){
			e.preventDefault();
			self.moveRowSelection($(this));
		});
		$(document).on('change', '[' + self.selectors.selectField + ']', function(){
			self.autoSelectFieldType($(this));
		});
		$(document).on('click', '[' + self.selectors.addFieldButton + ']', function(e){
			e.preventDefault();
			self.addField();
		});
		$(document).on('click', '[' + self.selectors.removeFieldButton + ']', function(e){
			e.preventDefault();
			self.removeField($(this));
		});
		$(document).on('click', '[' + self.selectors.saveButton + ']', function(e){
			e.preventDefault();
			$('[' + self.selectors.formError + '],[' + self.selectors.columnError + ']').hide();
			if ( self.validates() ) {
				$('[' + self.selectors.form + ']').submit();
			}
		});
	}

	/**
	* Get the columns for the row count
	*/
	self.getColumns = function()
	{
		self.toggleLoading(true);
		$.ajax({
			url: ajaxurl,
			type: 'POST',
			datatype: 'json',
			data: {
				action: 'wpslimportcolumns',
				rowcount: self.rowcount,
				nonce: wpsl_locator.locatorNonce
			},
			success: function(data){
				self.totalrows = data.row_count;
				self.updateCurrentRowText();
				self.populateColumnSelects(data);
			}
		});
	}

	/**
	* Populate the select boxes from the row data
	*/
	self.populateColumnSelects = function(data)
	{
		var nextBtnDisabled = ( (self.rowcount+1) === data.row_count ) ? 'disabled' : false;
		$(self.selectors.rowSelectionButtonNext).attr('disabled', nextBtnDisabled);

		var html = '<option value="">' + wpsl_locator.choose_column + '</option>';
		for ( var i = 0; i < data.columns.length; i++ ){
			html += '<option value="' + i + '">' + data.columns[i] + '</option>';
		}
		$('[' + self.selectors.selectColumn + ']').html(html);
		self.toggleLoading(false);
	}

	/**
	* Move the row selection back/next
	*/
	self.moveRowSelection = function(button)
	{
		self.rowcount = ( $(button).attr(self.selectors.rowSelectionButton) === 'next' ) ? self.rowcount + 1 : self.rowcount - 1;
		var buttonAttr = ( self.rowcount == 0 ) ? 'disabled' : false;
		$(self.selectors.rowSelectionButtonBack).attr('disabled', buttonAttr);
		self.getColumns();
	}

	/**
	* Update the current row text 
	*/
	self.updateCurrentRowText = function()
	{
		var text = wpsl_locator.Row + ' ' + (self.rowcount + 1) + ' of ' + self.totalrows;
		$('[' + self.selectors.currentRowText + ']').text(text);
	}

	/**
	* Get the custom fields for the post type
	*/
	self.getPostTypeFields = function()
	{
		$.ajax({
			type: 'GET',
			url: ajaxurl,
			data: {
				action: 'wpslposttype',
				nonce: wpsl_locator.locatorNonce,
				post_type: $('[' + self.selectors.postType + ']').val(),
				show_hidden: 'false',
				include_wpsl: 'true'
			},
			success: function(data){
				self.populateWordPressFieldSelects(data);
			}
		});
	}

	/**
	* Populate WordPress Field Selects
	*/
	self.populateWordPressFieldSelects = function(fields)
	{
		var selects = $('[' + self.selectors.selectField + ']');
		$.each(selects, function(i, v){
			$(this).append('<option value="title">' + wpsl_locator.title + '</option>');
			$(this).append('<option value="content">' + wpsl_locator.content + '</option>');
			$(this).append(fields.fields);
		});
	}

	/**
	* Choose Field type based on field selection
	*/
	self.autoSelectFieldType = function(select)
	{
		var field = $(select).val();
		var type_field = $(select).next('[' + self.selectors.selectType + ']');
		if ( field === 'wpsl_address' ) $(type_field).val('address');
		if ( field === 'wpsl_city' ) $(type_field).val('city');
		if ( field === 'wpsl_state' ) $(type_field).val('state');
		if ( field === 'wpsl_zip' ) $(type_field).val('zip');
		if ( field === 'wpsl_website' ) $(type_field).val('website');
	}

	/**
	* Add a new field to be mapped
	*/
	self.addField = function()
	{
		var fieldcount = $('.wpsl-field').length;
		
		var newrow = $('.row-template').clone().removeClass('row-template');
		$(newrow).appendTo('.wpsl-column-fields').find('[' + self.selectors.removeFieldButton + ']').show();
		$(newrow).find('.wpsl-column-error').text('').hide();
		
		// Set the name indexes
		$(newrow).find('[' + self.selectors.selectField + ']').attr('name', 'wpsl_import_field[' + fieldcount + '][field]');
		$(newrow).find('[' + self.selectors.selectColumn + ']').attr('name', 'wpsl_import_field[' + fieldcount + '][csv_column]');
		$(newrow).find('[' + self.selectors.selectType + ']').attr('name', 'wpsl_import_field[' + fieldcount + '][type]');
	}

	/**
	* Remove a field to be mapped
	*/
	self.removeField = function(button)
	{
		var row = $(button).parent('li');
		$(row).remove();
	}

	/**
	* Validate required fields
	*/
	self.validates = function()
	{
		var added_title = false;
		var added_address = false;
		var passes = true;

		$.each($('[' + self.selectors.fieldRow + ']'), function(i, v){
			
			// Validate Column & Field Selection
			var column = $(this).find('[' + self.selectors.selectColumn + ']');
			var field = $(this).find('[' + self.selectors.selectField + ']');
			if ( $(column).val() === "" || $(field).val() === "" ){
				$(this).find('[' + self.selectors.columnError + ']').text(wpsl_locator.required).show();
				passes = false;
			}

			// Make sure at least title and address are selected/added
			var type = $(this).find('[' + self.selectors.selectType + ']');
			if ( $(type).val() === "address" || $(type).val() === "full_address" ) added_address = true;
			if ( $(field).val() === "title" ) added_title = true;
		});

		// A title & address are required
		if ( !added_address ){
			$('[' + self.selectors.formError + ']').html('<p>' + wpsl_locator.required_address + '</p>').show();
			passes = false;
		}

		if ( !added_title ){
			$('[' + self.selectors.formError + ']').html('<p>' + wpsl_locator.required_title + '</p>').show();
			passes = false;
		}
		return passes;
	}

	/**
	* Toggle Loading
	*/
	self.toggleLoading = function(loading)
	{
		var loader = $('[' + self.selectors.loadingIndicator + ']');
		var columnSelection = $('[' + self.selectors.columnSelection + ']');
		if ( loading ){
			$(loader).show();
			$(columnSelection).hide();
			return;
		}
		$(loader).hide();
		$(columnSelection).show();
	}

	return self.bindEvents();
}