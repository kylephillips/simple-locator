<?php
namespace SimpleLocator\API;

class AllLocationsShortcode
{
	/**
	* Shortcode Options
	* @var arrat
	*/
	public $options;

	public function __construct()
	{
		add_shortcode('simple_locator_all_locations', [$this, 'renderView']);
	}

	/**
	* Shortcode Options
	*/
	private function setOptions($options)
	{
		$this->options = shortcode_atts([
			'limit' => '-1',
			'mapheight' => ''
		], $options);
	}

	/**
	* Enqueue the single view script & add localized data
	*/
	private function enqueueScripts()
	{
		wp_enqueue_script('google-maps');
		wp_enqueue_script('simple-locator');
	}

	/**
	* The View
	*/
	public function renderView($options)
	{
		$this->setOptions($options);
		$this->enqueueScripts();
		$output = '<div data-simple-locator-all-locations-map data-limit="' . $this->options['limit'] . '" class="wpsl-map" style="display:block;';
		if ( $this->options['mapheight'] !== '' ) $output .= 'height:' . intval($this->options['mapheight']) . 'px;';
		$output .= '"></div>';
		return $output;
	}
}