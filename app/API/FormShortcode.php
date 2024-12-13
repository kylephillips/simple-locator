<?php 
namespace SimpleLocator\API;

use \SimpleLocator\Repositories\MapStyles;
use \SimpleLocator\Repositories\SettingsRepository;
use \SimpleLocator\Form\SearchForm;

class FormShortcode 
{
	/**
	* Taxonomies to filter
	* @var array
	*/
	private $taxonomies;

	/**
	* Shortcode Options
	*/
	public $options;

	/**
	* Settings Repository
	*/
	private $settings_repo;


	public function __construct()
	{
		$this->styles_repo = new MapStyles;
		$this->settings_repo = new SettingsRepository();
		add_shortcode('wp_simple_locator', [$this, 'renderView']);
		add_shortcode('simple_locator', [$this, 'renderView']);
	}

	/**
	* Set the taxonomy filters
	*/
	private function taxonomies()
	{
		if ( $this->options['taxonomies'] == "" ) return false;
		$tax_array = explode(',', $this->options['taxonomies']);
		$taxonomies = [];
		foreach ( $tax_array as $key => $tax ){
			$taxonomy = get_taxonomy(esc_attr($tax));
			if ( !$taxonomy ) continue;
			$tax_label = $taxonomy->labels->name; // Get the label
			$terms = get_terms($tax); // Get the terms
			if ( !$terms ) continue;
			$taxonomies[$tax]['label'] = $tax_label;
			$taxonomies[$tax]['terms'] = $terms;
		}
		return $taxonomies;
	}

	/**
	* Enqueue the Required Scripts
	*/
	private function enqueueScripts()
	{
		wp_enqueue_script('google-maps');
		wp_enqueue_script('simple-locator');
	}

	/**
	* Shortcode Options
	*/
	private function setOptions($options)
	{
		global $post;
		$this->options = shortcode_atts([
			'distances' => '5,10,20,50,100',
			'mapheight' => '250',
			'mapcontainer' => '', // AJAX Only
			'resultscontainer' => '', // AJAX Only
			'buttontext' => __('Search', 'simple-locator'),
			'addresslabel' => __('Zip/Postal Code', 'simple-locator'),
			'mapcontrols' => 'show', // AJAX Only
			'mapcontrolsposition' => 'TOP_LEFT', // AJAX Only
			'showgeobutton' => $this->settings_repo->geoButton('enabled'),
			'geobuttontext' => $this->settings_repo->geoButton('text'),
			'placeholder'=> __('Enter a Location', 'simple-locator'),
			'ajax' => 'true',
			'formmethod' => 'post', // Non-AJAX Only
			'perpage' => '', // Set to empty or 0 for no pagination
			'resultspage' => $post->ID, // Non-AJAX Only
			'noresultstext' => __('No results found.', 'simple-locator'),
			'taxonomies' => '',
			'taxonomy_field_type' => 'select', // or Checkbox
			'allowemptyaddress' => 'false',
			'resultswrapper' => '',
			'showall' => '' // Show all locations on inital load, value is used as list header if enabled
		], $options);
		$this->options['formmethod'] = ( $this->options['formmethod'] == 'post' ) ? 'post' : 'get';
		$this->options['ajax'] = ( $this->options['ajax'] == 'true' ) ? true : false;
		$this->options['widget'] = false;
		$this->options['autocomplete'] = $this->settings_repo->autocomplete();
		$this->options['unit_raw'] = $this->settings_repo->getDistanceUnit();
		$this->options['unit'] = $this->settings_repo->getDistanceUnitLocalized();
		$this->options['distance_options'] = $this->distanceOptions();
		$this->options['taxonomies'] = $this->taxonomies();
		$this->options['show_default_map'] = $this->settings_repo->showDefaultMap();
		$this->options['allowemptyaddress'] = ( $this->options['allowemptyaddress'] == 'true' ) ? true : false;
		$this->options['mapcontrols'] = ( $this->options['mapcontrols'] == 'show' ) ? true : false;
		$this->options['mapcontainer'] = ( $this->options['mapcontainer'] == '' ) ? null : $this->options['mapcontainer'];
		$this->options['resultscontainer'] = ( $this->options['resultscontainer'] == '' ) ? null : $this->options['resultscontainer'];
		if ( $this->options['ajax'] == 'false' && $this->options['perpage'] == '' ) $this->options['perpage'] = get_option('posts_per_page');
		$this->options['showall'] = ( $this->options['showall'] !== '' ) ? true : false;
	}

	/**
	* Format Distances option as array & return a list of select options
	*/
	private function distanceOptions()
	{
		$this->options['distances'] = explode(',', $this->options['distances']);
		$out = "";
		foreach ( $this->options['distances'] as $distance ){
			$default = false;
			if ( !is_numeric($distance) && !strpos($distance, '*') ) continue;
			if ( strpos($distance, '*') ){
				$default = true;
				$distance = intval(str_replace('*', '', $distance));
			}
			$out .= '<option value="' . $distance . '"';
			if ( $default ) $out .= ' selected';
			$out .= '>' . $distance . ' ' . $this->options['unit'] . '</option>';
		}
		return $out;
	}

	/**
	* Localize Shortcode Options
	*/ 
	private function localizeOptions()
	{
		$localized_data = [
			'mapcont' => $this->options['mapcontainer'],
			'resultscontainer' => $this->options['resultscontainer'],
			'mapcontrols' => $this->options['mapcontrols'],
			'mapcontrolsposition' => $this->options['mapcontrolsposition'],
			'ajax' => $this->options['ajax'],
			'noresultstext' => $this->options['noresultstext'],
			'resultswrapper' => $this->options['resultswrapper']
		];
		wp_localize_script( 
			'simple-locator', 
			'wpsl_locator_options', 
			$localized_data
		);
	}

	/**
	* The View
	*/
	public function renderView($options)
	{	
		$display_form = ( isset($_POST['simple_locator_results']) || isset($_GET['simple_locator_results']) ) ? false : true;
		$display_form = apply_filters('simple_locator_form_in_results', $display_form);
		if ( !$display_form ) return;
		$this->setOptions($options);
		$this->enqueueScripts();
		$this->localizeOptions();
		$form = new SearchForm($this->options);
		$output = \SimpleLocator\Helpers::template('search-form', $this->options);
		return apply_filters('simple_locator_form', $output, $this->options);
	}
}