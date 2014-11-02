<?php namespace SimpleLocator\API;

use \SimpleLocator\Repositories\MapStyles;

class ShortcodeForm {

	/**
	* Unit of Measurement
	*/
	private $unit;

	/**
	* Shortcode Options
	*/
	public $options;


	public function __construct()
	{
		$this->setUnit();
		$this->styles_repo = new MapStyles;
		add_shortcode('wp_simple_locator', array($this, 'renderView'));
	}


	/**
	* Set the unit of measurement
	*/
	private function setUnit()
	{
		$this->unit = ( get_option('wpsl_measurement_unit') ) ? get_option('wpsl_measurement_unit') : 'miles';
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
			'mapcontrolsposition' => 'TOP_LEFT'
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

