<?php 
namespace SimpleLocator\API;

use \SimpleLocator\Repositories\SettingsRepository;

class FormWidget extends \WP_Widget 
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
	* Widget Options
	*/
	public $options;

	/**
	* Settings Repository
	*/
	private $settings_repo;

	public function __construct()
	{
		$this->settings_repo = new SettingsRepository();
		$this->setUnit();
		parent::__construct(
			'simple_locator',
			__('Simple Locator', 'simple-locator'),
			['description' => __( 'Display the Simple Locator Form', 'simple-locator' )]
		);
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
	* Options
	*/
	private function setOptions($instance)
	{
		$this->options['distances'] = (isset($instance['distance_options'])) ? $instance['distance_options'] : '5,10,20,50,100';
		$this->options['buttontext'] = __('Search', 'simple-locator');
		$this->options['mapcontrols'] = 'show';
		$this->options['showgeobutton'] = $this->settings_repo->geoButton('enabled');
		$this->options['geobuttontext'] = $this->settings_repo->geoButton('text');
		$this->options['placeholder'] = ( isset($instance['placeholder']) ) ? $instance['placeholder'] :__('Enter a Location', 'simple-locator');
		$this->options['noresultstext'] = __('No results found', 'simple-locator');
		$this->options['addresslabel'] = __('Zip/Postal Code', 'simple-locator');
		$this->options['mapcontainer'] = '';
		$this->options['ajax'] = true;
		$this->options['formmethod'] = 'get';
		$this->options['resultspage'] = '';
		$this->options['resultscontainer'] = '';
		$this->options['mapcontrolsposition'] = '';
		$this->options['perpage'] = ( isset($instance['perpage']) ) ? $instance['perpage'] : '';
		$this->options['mapheight'] = ( isset($instance['map_height']) ) ? $instance['map_height'] : 200;
		$this->options['showall'] = '';
		$this->options['widget'] = true;
		$this->options['autocomplete'] = $this->settings_repo->autocomplete();
		$this->options['distance_options'] = $this->distanceOptions();
		$this->options['taxonomies'] = false;
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
	* Enqueue the Required Scripts
	*/
	private function enqueueScripts()
	{
		wp_enqueue_script('google-maps');
		if ( wp_script_is('simple-locator', 'enqueued') ) return;
		wp_enqueue_script('simple-locator');	
	}

	/**
	* Widget Form in Admin
	*/
	public function form( $instance ) {
		$title = ( isset($instance['title']) ) ? $instance[ 'title' ] : '';
		$distance_options = ( isset($instance['distance_options']) ) ? $instance[ 'distance_options' ] : '';
		$map_height = ( isset($instance['map_height']) ) ? $instance[ 'map_height' ] : '';
		$placeholder = ( isset($instance['placeholder']) ) ? $instance['placeholder'] : __('Enter a Location', 'simple-locator');
		$perpage = ( isset($instance['perpage']) ) ? $instance['perpage'] : '';
		include( \SimpleLocator\Helpers::view('widget-options') );
	}

	/**
	* Save Widget Form Data
	*/
	public function update( $new_instance, $old_instance ) {
		$instance = [];
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['distance_options'] = ( ! empty( $new_instance['distance_options'] ) ) ? strip_tags( $new_instance['distance_options'] ) : '5,10,20,50,100';
		$instance['map_height'] = ( ! empty( $new_instance['map_height'] ) ) ? strip_tags( intval($new_instance['map_height']) ) : '';
		$instance['placeholder'] = ( ! empty( $new_instance['placeholder'] ) ) ? strip_tags( $new_instance['placeholder'] ) : '';
		$instance['perpage'] = ( ! empty( $new_instance['perpage'] ) ) ? strip_tags( intval($new_instance['perpage']) ) : '';
		return $instance;
	}

	/**
	* Front End of Widget
	*/
	public function widget( $args, $instance )
	{
		$this->setOptions($instance);
		echo $args['before_widget'];
		
		if ( !empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		}
		
		$this->enqueueScripts();

		ob_start();
		include( \SimpleLocator\Helpers::template('search-form') );
		$output = ob_get_contents();
		ob_end_clean();
		echo $output;
		echo $args['after_widget'];
	}
}