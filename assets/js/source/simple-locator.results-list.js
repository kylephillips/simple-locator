/**
* Display a list of results
* @package simple-locator
*/
var SimpleLocator = SimpleLocator || {};
SimpleLocator.ResultsList = function()
{
	var self = this;
	var $ = jQuery;

	self.activeForm;
	self.activeFormContainer;
	self.activeList;
	self.data;
	self.listIndex;

	self.bindEvents = function()
	{
		$(document).on('simple-locator-form-success', function(e, data, form){
			self.activeForm = $(form);
			self.activeFormContainer = $(form).parents('[' + SimpleLocator.selectors.formContainer + ']');
			self.data = data;
			self.setListContainer();
			self.setMapIndex();
			self.loadList();
		});
	}

	/**
	* Set the map index
	*/
	self.setMapIndex = function()
	{
		var wrappers = $('[' + SimpleLocator.selectors.resultsWrapper + ']');
		self.listIndex = $(self.activeFormContainer).index(wrappers);
	}

	/**
	* Set the list container
	*/
	self.setListContainer = function()
	{
		var container = $(self.activeForm).attr('data-simple-locator-results-container');
		if ( typeof container === 'undefined' || container === ''){
			self.activeList = $(self.activeFormContainer).find('[' + SimpleLocator.selectors.results + ']');
			return;
		}
		self.activeList = $(container);
	}

	/**
	* Load the results
	*/
	self.loadList = function()
	{
		if ( self.data.result_count < 1 ){ // No results were returned
			$(self.activeFormContainer).find('[' + SimpleLocator.selectors.error + ']').text(wpsl_locator_options.noresultstext).show();
			$(self.activeList).hide();
			$(self.activeFormContainer).find('[' + SimpleLocator.selectors.map + ']').hide();
			wpsl_no_results(data.formatted_address, active_form);
			return;
		}

		var location = ( self.data.result_count === 1 ) ? wpsl_locator.location : wpsl_locator.locations;

		var output = '<h3 class="wpsl-results-header">' + self.data.result_count + ' ' + location;
		if ( self.data.latitude !== "" ) output += ' ' + wpsl_locator.found_within + ' ' + self.data.distance + ' ' + self.data.unit + ' ' + wpsl_locator.of + ' ';
		output += ( self.data.geolocation === "true" ) ? wpsl_locator.yourlocation : self.data.formatted_address;
		output += '</h3>';
		
		if ( wpsl_locator_options.resultswrapper !== "" ) output += '<' + wpsl_locator_options.resultswrapper + '>';

		for( i = 0; i < self.data.results.length; i++ ) {
			output = output + self.data.results[i].output;
		}

		if ( wpsl_locator_options.resultswrapper !== "" ) output += '</' + wpsl_locator_options.resultswrapper + '>';

		self.toggleLoading(false);
		$(self.activeList).removeClass('loading').html(output);

		$(document).trigger('simple-locator-results-rendered', [self.listIndex, self.activeForm]);
		wpsl_after_render(self.activeList); // Deprecated
	}

	/**
	* Toggle the loading state on the map
	*/
	self.toggleLoading = function(loading)
	{
		if ( loading ){
			$(self.activeList).addClass('loading');
			return;
		}
		$(self.activeList).removeClass('loading');
		$(self.activeList).show();
	}

	return self.bindEvents();
}