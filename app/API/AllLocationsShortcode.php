<?php
namespace SimpleLocator\API;

class AllLocationsShortcode
{
	/**
	* Shortcode Options
	* @var arrat
	*/
	public $options;

	/**
	* Post Args (for post ids, taxonomies, etc)
	*/
	private $args = [];

	public function __construct()
	{
		add_shortcode('simple_locator_all_locations', [$this, 'renderView']);
	}

	/**
	* Shortcode Options
	*/
	private function setOptions($options)
	{
		// Taxonomies may also be passed in to filter:
		// Ex: category="4,6"
		$this->options = shortcode_atts([
			'limit' => '-1',
			'mapheight' => '',
			'taxonomies' => '',
			'terms' => ''
		], $options);
		$this->setTaxonomyArgs($options);
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
	* Setup Taxonomy Arguments
	*/
	private function setTaxonomyArgs($options)
	{
		$tax_args = [];
		$taxonomies = get_taxonomies();
		foreach ( $taxonomies as $tax ){
			if ( array_key_exists($tax, $options) ) $tax_args[$tax] = explode(',', $options[$tax]);
		}
		if ( !empty($tax_args) ) $this->args['taxfilter'] = $tax_args;
	}

	/**
	* The View
	*/
	public function renderView($options)
	{
		$this->setOptions($options);
		$this->enqueueScripts();
		$output = '<div data-simple-locator-all-locations-map data-limit="' . $this->options['limit'] . '" class="wpsl-map"';

		// Add data-attributes to handle taxonomy arguments
		if ( isset($this->args['taxfilter']) ) {
			foreach ( $this->args['taxfilter'] as $taxonomy => $terms ) {
				$output .= ' data-taxfilter-' . $taxonomy . '="' . implode(',', $terms) . '"';
			}
		}
		
		if ( $this->options['mapheight'] !== '' ) $output .= ' style="height:' . intval($this->options['mapheight']) . 'px;"';

		$output .= '></div>';
		return $output;
	}
}