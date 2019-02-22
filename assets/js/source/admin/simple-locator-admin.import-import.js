/**
* Import Functionality - The Import (Last Step)
* @package simple-locator
*/
var SimpleLocatorAdmin = SimpleLocatorAdmin || {};
SimpleLocatorAdmin.Import = function()
{
	var self = this;
	var $ = jQuery;

	self.pause = false;
	self.offset = parseInt(wpsl_locator.importoffset); // Current offset to fetch
	self.completecount = parseInt(wpsl_locator.complete_count); // How many rows complete
	self.errorcount = parseInt(wpsl_locator.error_count); // How many error rows
	self.imports_per_request = 5; // How many imports per request

	self.selectors = {
		startButton : 'data-simple-locator-import-start-button',
		pauseButton : 'data-simple-locator-import-pause',
		intro : 'data-simple-locator-import-intro',
		lastImported : 'data-simple-locator-import-last-imported',
		progressMessage : 'data-simple-locator-import-progress-message',
		progressBar : 'data-simple-locator-import-progress-bar',
		progressBarBackground : 'data-simple-locator-import-progress-bar-background',
		error : 'data-simple-locator-import-error',
		errorCount : 'data-simple-locator-import-error-count',
		progressCount : 'data-simple-locator-import-progress-count',
		importComplete : 'data-simple-locator-import-complete',
		totalCount : 'data-simple-locator-import-total-count'
	}

	self.bindEvents = function()
	{
		$(document).on('click', '[' + self.selectors.startButton + ']', function(e){
			e.preventDefault();
			self.startImport();
		})
		$(document).on('click', '[' + self.selectors.pauseButton + ']', function(e){
			e.preventDefault();
			self.pause = ( self.pause ) ? false : true;
			var buttontext = ( self.pause ) ? wpsl_locator.pause_continue : wpsl_locator.pause;
			$(this).text(buttontext);
			self.doImport();
		});
	}

	/**
	* Start the Import
	*/
	self.startImport = function()
	{
		$('[' + self.selectors.intro + '],[' + self.selectors.lastImported + ']').hide();
		$('[' + self.selectors.progressMessage + ']').show();
		self.doImport();
	}

	/**
	* Do the Import – Recursive function fires every 5 rows until import is complete
	*/
	self.doImport = function()
	{
		if ( self.pause ) return;
		$.ajax({
			url: ajaxurl,
			type: 'post',
			datatype: 'json',
			data: {
				action: 'wpsldoimport',
				nonce: wpsl_locator.locatorNonce,
				offset: self.offset,
				imports_per_request: self.imports_per_request
			},
			success: function(data){
				if ( data.status === 'testing' ){
					console.log(data);
					return;
				}
				if ( data.status === 'complete' ){
					self.completeImport();
					return;
				}
				if (data.status === 'error') {
					$('[' + self.selectors.error + ']').html('<p>' + data.message + '</p>').show();
					$('[' + self.selectors.progressMessage + ']').hide();
					return;
				}
				self.updateProgress(data);
			}
		});
	}

	/**
	* Update Progress
	*/
	self.updateProgress = function(data)
	{
		// Progress Count
		self.completecount = self.completecount + data.import_count;
		self.offset = self.offset + data.import_count + data.failed_count; // increment the offset
		self.errorcount = self.errorcount + data.failed_count;
		$('[' + self.selectors.progressCount + ']').text(self.completecount);
		$('[' + self.selectors.errorCount + ']').text(self.errorcount);
		
		// Progress Bar
		var progress_total_width = $('[' + self.selectors.progressBarBackground + ']').width();
		var progress_ratio = self.completecount / parseInt($('[' + self.selectors.progressBarBackground + ']').data('total'));
		var progress_width = progress_total_width * progress_ratio;
		$('[' + self.selectors.progressBar + ']').width(progress_width + 'px');

		self.doImport();
	}

	/**
	* Import is Complete
	*/
	self.completeImport = function()
	{
		$('[' + self.selectors.progressMessage + ']').hide();
		$('[' + self.selectors.importComplete + ']').show();
		$.ajax({
			url: ajaxurl,
			type: 'post',
			datatype: 'json',
			data: {
				action: 'wpslfinishimport'
			},
			success: function(data){
				$('[' + self.selectors.totalCount + ']').text(data.import_count);
				$('[' + self.selectors.errorCount + ']').text(data.error_count);
				if ( data.errors.length > 0 ){
					self.appendErrorRows(data.errors);
				}
			}
		});
	}

	/**
	* Append Import Errors after complete
	*/
	self.appendErrorRows = function(errors)
	{
		for ( var i = 0; i < errors.length; i++ ){
			var html = '<tr><td>' + errors[i].row + '</td><td>' + errors[i].error + '</td></tr>';
			$('.wpsl-import-details table').append(html);
		}
		$('.wpsl-import-details').show();
	}

	return self.bindEvents();
}