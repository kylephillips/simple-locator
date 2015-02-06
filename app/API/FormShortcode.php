<?php namespace SimpleLocator\API;

use \SimpleLocator\Repositories\MapStyles;
use \SimpleLocator\Repositories\SettingsRepository;

class FormShortcode {

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
		$unit = get_option('wpsl_measurement_unit');
		if ( $unit == "" || $unit == 'miles' ) {
			$this->unit_raw = 'miles';
			$this->unit = __('Miles', 'wpsimplelocator');
			return;
		}
		$this->unit_raw = 'kilometers';
		$this->unit = __('Kilometers', 'wpsimplelocator');
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
			'geobuttontext' => $this->settings_repo->showGeoButton('text')
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
			'mapcontrolsposition' => $this->options['mapcontrolsposition']
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
		include ( \SimpleLocator\Helpers::view('simple-locator-form') );
		return $output;
	}

}

