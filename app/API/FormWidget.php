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
			__('Simple Locator', 'wpsimplelocator'),
			array( 'description' => __( 'Display the Simple Locator Form', 'wpsimplelocator' ) )
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
		$this->options['buttontext'] = __('Search', 'wpsimplelocator');
		$this->options['mapcontrols'] = 'show';
		$this->options['showgeobutton'] = $this->settings_repo->showGeoButton('enabled');
		$this->options['geobuttontext'] = $this->settings_repo->showGeoButton('text');
		$this->options['placeholder'] = __('Enter a Location', 'wpsimplelocator');
		$this->options['noresultstext'] = __('No results found', 'wpsimplelocator');
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
		$localized_data = array(
			'noresultstext' => $this->options['noresultstext']
		);
		wp_localize_script( 
			'simple-locator', 
			'wpsl_locator_options', 
			$localized_data
		);
	}

	/**
	* Widget Form in Admin
	*/
	public function form( $instance ) {
		$title = ( isset($instance['title']) ) ? $instance[ 'title' ] : '';
		$distance_options = ( isset($instance['distance_options']) ) ? $instance[ 'distance_options' ] : '';
		$map_height = ( isset($instance['map_height']) ) ? $instance[ 'map_height' ] : '';
		$placeholder = ( isset($instance['placeholder']) ) ? $instance['placeholder'] : __('Enter a Location', 'wpsimplelocator');
		include( \SimpleLocator\Helpers::view('widget-options') );
	}

	/**
	* Save Widget Form Data
	*/
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['distance_options'] = ( ! empty( $new_instance['distance_options'] ) ) ? strip_tags( $new_instance['distance_options'] ) : '5,10,20,50,100';
		$instance['map_height'] = ( ! empty( $new_instance['map_height'] ) ) ? strip_tags( intval($new_instance['map_height']) ) : '';
		$instance['placeholder'] = ( ! empty( $new_instance['placeholder'] ) ) ? strip_tags( $new_instance['placeholder'] ) : '';
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
		$widget_instance = true;
		include( \SimpleLocator\Helpers::view('simple-locator-form-ajax') );
		echo $output;

		echo $args['after_widget'];
	}

}