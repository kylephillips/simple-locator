/**
* Import Functionality
*/
jQuery(function($){

	var offset = 0;
	var completecount = 0;
	var errorcount = 0; 
	var pause = false; // pause import
	var rowcount = 0; // for selecting a specific row from csv
	var totalrows = 0;
	var passed_validation = true;
	var import_counter = 1;

	/**
	* ------------------------------------------------------
	* Map CSV columns
	* ------------------------------------------------------
	*/
	$(document).ready(function(){
		if ( wpsl_locator.isimport === "true" && wpsl_locator.importstep === "2" ){
			get_csv_columns();
		}
	});

	/**
	* Get the columns for the rowcount
	*/
	function get_csv_columns()
	{
		$('.wpsl-loading-settings').show();
		$('.wpsl-column-selection').hide();
		$.ajax({
			url: ajaxurl,
			type: 'post',
			datatype: 'json',
			data: {
				action: 'wpslimportcolumns',
				rowcount: rowcount,
				nonce: wpsl_locator.locatorNonce
			},
			success: function(data){
				totalrows = data.row_count;
				populate_select_boxes(data);
			}
		});
	}

	/**
	* Populate the select boxes from the row data
	*/
	function populate_select_boxes(row)
	{
		if ( (rowcount+1) === row.row_count ){
			$('button[data-direction="next"').attr('disabled', 'disabled');
		} else {
			$('button[data-direction="next"').attr('disabled', false);
		}

		var html = '<option value="">' + wpsl_locator.choose_column + '</option>';
		for ( var i = 0; i < row.columns.length; i++ ){
			html += '<option value="' + i + '">' + row.columns[i] + '</option>';
		}
		$('.wpsl-import-column-selection').html(html);
		$('.wpsl-loading-settings').hide();
		$('.wpsl-column-selection').show();
	}

	/**
	* Move forward/back a row in select display
	*/
	$('.wpsl-row-selection button').on('click', function(e){
		e.preventDefault();
		var direction = $(this).data('direction');
		move_row_selection(direction);
	});

	/**
	* Reload selected row to given index
	*/
	function move_row_selection(direction)
	{
		if ( direction == 'back' ){
			rowcount--;
		} else {
			rowcount++;
		}

		if ( rowcount == 0 ){
			$('button[data-direction="back"]').attr('disabled', 'disabled');
		} else {
			$('button[data-direction="back"]').attr('disabled', false);
		}

		var currentrow = rowcount + 1;
		var text = wpsl_locator.Row + ' ' + currentrow + ' of ' + totalrows;
		$('.wpsl-current-row').text(text);
		get_csv_columns();
	}

	/**
	* Get the fields for the chosen post type
	*/
	$(document).ready(function(){
		if ( wpsl_locator.importstep == "2" ) get_fields_for_posttype(true);
	});
	function get_fields_for_posttype(populate_all)
	{
		$.ajax({
			type: 'GET',
			url: ajaxurl,
			data: {
				action: 'wpslposttype',
				nonce: wpsl_locator.locatorNonce,
				post_type: $('#wpsl-import-post-type').val(),
				show_hidden: 'true'
			},
			success: function(data){
				if ( populate_all ){
					var fields = $('.wpsl-import-field-selection');
					$.each(fields, function(i, v){
						populate_field_select($(this), data.fields);
					});
				}
			}
		});
	}

	/**
	* Populate field select menu
	*/
	function populate_field_select(item, fields)
	{
		$(item).append('<option value="title">' + wpsl_locator.title + '</option>');
		$(item).append('<option value="content">' + wpsl_locator.content + '</option>');
		$(item).append(fields);
	}


	/**
	* Validate required columns
	*/
	$('.wpsl_save_columns').on('click', function(e){
		e.preventDefault();
		$('.wpsl-column-error, .wpsl-form-error').hide();
		
		passed_validation = true;
		validate_required_columns();
		
		if ( passed_validation ){
			$(this).unbind('click').click();	
		} 
	});

	function validate_required_columns()
	{
		var rows = $('.wpsl-field');
		var added_title = false;
		var added_address = false;

		$.each(rows, function(i, v){
			
			// Validate Column & Field Selection
			var column = $(this).find('.wpsl-import-column-selection');
			var field = $(this).find('.wpsl-import-field-selection');
			if ( $(column).val() === "" || $(field).val() === "" ){
				$(this).find('.wpsl-column-error').text(wpsl_locator.required).show();
				passed_validation = false;
			}

			// Make sure at least title and address are selected/added
			var type = $(this).find('.wpsl-import-type-selection');
			if ( $(type).val() === "address" || $(type).val() === "full_address" ) added_address = true;
			if ( $(field).val() === "title" ) added_title = true;
		});

		// A title & address are required
		if ( !added_address ){
			$('.wpsl-form-error').html('<p>' + wpsl_locator.required_address + '</p>').show();
			passed_validation = false;
		}

		if ( !added_title ){
			$('.wpsl-form-error').html('<p>' + wpsl_locator.required_title + '</p>').show();
			passed_validation = false;
		}
	}

	/**
	* Remove a field
	*/
	$(document).on('click', '.wpsl-import-remove-field', function(e){
		e.preventDefault();
		remove_field($(this))
	});
	function remove_field(button)
	{
		var row = $(button).parent('li');
		$(row).remove();
	}

	/**
	* Add a New Row/Field
	*/
	$(document).on('click', '.wpsl-import-add-field a', function(e){
		e.preventDefault();
		add_field();
	});
	function add_field()
	{
		var fieldcount = $('.wpsl-field').length;
		
		var newrow = $('.row-template').clone().removeClass('row-template');
		$(newrow).appendTo('.wpsl-column-fields').find('.wpsl-import-remove-field').show();
		$(newrow).find('.wpsl-column-error').text('').hide();
		
		// Set the name indexes
		$(newrow).find('.wpsl-import-field-selection').attr('name', 'wpsl_import_field[' + fieldcount + '][field]');
		$(newrow).find('.wpsl-import-column-selection').attr('name', 'wpsl_import_field[' + fieldcount + '][csv_column]');
		$(newrow).find('.wpsl-import-type-selection').attr('name', 'wpsl_import_field[' + fieldcount + '][type]');
	}


	/**
	* ------------------------------------------------------
	* Do the Import
	* ------------------------------------------------------
	*/

	$('.wpsl-start-import').on('click', function(e){
		e.preventDefault();
		$('.wpsl-import-indicator-intro').hide();
		$('.wpsl-import-indicator').show();
		do_import();
	});

	/**
	* Start the Import
	*/
	function do_import()
	{
		$.ajax({
			url: ajaxurl,
			type: 'post',
			datatype: 'json',
			data: {
				action: 'wpsldoimport',
				offset: offset
			},
			success: function(data){
				console.log(data);
				if ( data.status === 'complete' ){
					complete();
				} else if (data.status === 'apierror') {
					$('.wpsl-import-error p').text(data.message);
					$('.wpsl-import-error').show();
				} else {
					update_counts(data);
				}
			}
		});
	}

	/**
	* Update Counts
	*/
	function update_counts(data)
	{
		offset = offset + import_counter;
		var imported = data.import_count;
		completecount = completecount + imported;
		errorcount = errorcount + data.failed;
		$('.progress-count').text(completecount);
		$('.error-count').text(errorcount);
		update_progress_bar();
		if ( !pause ) do_import();
	}

	/**
	* Update the width of the progress bar indicator
	*/
	function update_progress_bar()
	{
		var progress_total_width = $('.progress-bar-bg').width();
		var progress_ratio = completecount / parseInt($('.progress-bar-bg').data('total'));
		var progress_width = progress_total_width * progress_ratio;
		$('.progress-bar').width(progress_width + 'px');
	}

	/**
	* Import is complete
	*/
	function complete()
	{
		$('.wpsl-import-indicator').hide();
		$('.wpsl-import-complete').show();
		get_import_complete_status();
	}

	/**
	* Pause the Import
	*/
	$('.wpsl-pause-import').on('click', function(e){
		
		if ( $(this).hasClass('paused') ){
			$(this).removeClass('paused');
			$(this).text(wpsl_locator.pause);
			pause = true;
		} else {
			$(this).addClass('paused');
			$(this).text(wpsl_locator.pause_continue);
			pause = false;
		}
	});



	/**
	* ------------------------------------------------------
	* Import is Complete
	* ------------------------------------------------------
	*/
	function get_import_complete_status()
	{
		$.ajax({
			url: ajaxurl,
			type: 'post',
			datatype: 'json',
			data: {
				action: 'wpslfinishimport'
			},
			success: function(data){
				console.log(data);
				$('.wpsl-total-import-count').text(data.import_count);
				$('.wpsl-total-error-count').text(data.error_count);
				if ( data.errors.length > 0 ){
					append_import_errors(data.errors);
				}
			}
		});
	}

	/**
	* Append rows to the error log table
	*/
	function append_import_errors(errors)
	{
		for ( var i = 0; i < errors.length; i++ ){
			var html = '<tr><td>' + errors[i].row + '</td><td>' + errors[i].error + '</td></tr>';
			$('.wpsl-import-details table').append(html);
		}
		$('.wpsl-import-details').show();
	}




}); // jQuery