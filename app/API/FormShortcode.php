<?php 

namespace SimpleLocator\API;

use \SimpleLocator\Repositories\MapStyles;
use \SimpleLocator\Repositories\SettingsRepository;

class FormShortcode 
{

	/**
	* Unit of Measurement
	*/
	private $unit;

	/**
	* Untranslated Unit
	*/
	private $unit_raw;

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
		add_action('init', array($this, 'init'));
		add_shortcode('wp_simple_locator', array($this, 'renderView'));
	}

	/**
	* Init
	*/
	public function init()
	{
		$this->setUnit();
	}

	/**
	* Set the unit of measurement
	*/
	private function setUnit()
	{
		$this->unit_raw = $this->settings_repo->getDistanceUnit();
		$this->unit = $this->settings_repo->getDistanceUnitLocalized();
	}

	/**
	* Enqueue the Required Scripts
	*/
	private function enqueueScripts()
	{
		wp_enqueue_script('google-maps');
		wp_enqueue_script('simple-locator');
		if ( $this->options['ajax'] !== 'true' ) wp_enqueue_script('simple-locator-non-ajax-results');
	}

	/**
	* Shortcode Options
	*/
	private function setOptions($options)
	{
		$this->options = shortcode_atts(array(
			'distances' => '5,10,20,50,100',
			'mapheight' => '250',
			'mapcontainer' => '.wpsl-map',
			'resultscontainer' => '.wpsl-results',
			'buttontext' => __('Search', 'wpsimplelocator'),
			'addresslabel' => __('Zip/Postal Code', 'wpsimplelocator'),
			'mapcontrols' => 'show',
			'mapcontrolsposition' => 'TOP_LEFT',
			'showgeobutton' => $this->settings_repo->showGeoButton('enabled'),
			'geobuttontext' => $this->settings_repo->showGeoButton('text'),
			'placeholder'=> __('Enter a Location', 'wpsimplelocator'),
			'ajax' => 'true',
			'perpage' => get_option('posts_per_page'),
			'noresultstext' => __('No results found.', 'wpsimplelocator')
		), $options);
	}

	/**
	* Format Distances option as array & return a list of select options
	*/
	private function distanceOptions()
	{
		$this->options['distances'] = explode(',', $this->options['distances']);
		$out = "";
		foreach ( $this->options['distances'] as $distance ){
			if ( !is_numeric($distance) ) continue;
			$out .= '<option value="' . $distance . '">' . $distance . ' ' . $this->unit . '</option>';
		}
		return $out;
	}

	/**
	* Localize Shortcode Options
	*/ 
	private function localizeOptions()
	{
		$localized_data = array(
			'mapcont' => $this->options['mapcontainer'],
			'resultscontainer' => $this->options['resultscontainer'],
			'mapcontrols' => $this->options['mapcontrols'],
			'mapcontrolsposition' => $this->options['mapcontrolsposition'],
			'ajax' => $this->options['ajax'],
			'noresultstext' => $this->options['noresultstext']
		);
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
		$this->setOptions($options);
		$this->enqueueScripts();
		$this->localizeOptions();
		if ( $this->options['ajax'] !== 'false' ){
			include ( \SimpleLocator\Helpers::view('simple-locator-form-ajax') );
			return $output;
		}
		include ( \SimpleLocator\Helpers::view('simple-locator-form') );
		return $output;
	}

}