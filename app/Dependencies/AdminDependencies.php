<?php namespace SimpleLocator\Dependencies;

/**
* Register & Enqueue Admin Styles & Scripts
*/
class AdminDependencies extends DependencyBase {

	public function __construct()
	{
		parent::__construct();
		add_action( 'admin_enqueue_scripts', array( $this, 'styles' ));
		add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ));
		add_action( 'admin_enqueue_scripts', array( $this, 'mapSettings' ));
		add_action( 'admin_enqueue_scripts', array( $this, 'defaultMapSettings' ));
	}

	/**
	* Admin Styles
	*/
	public function styles()
	{
		wp_enqueue_style(
			'simplelocator', 
			$this->plugin_dir . '/assets/css/simple-locator-admin.css', 
			array(), 
			$this->version
		);
	}

	/**
	* Admin Scripts
	*/
	public function scripts()
	{
		$screen = get_current_screen();
		if ( ($screen->post_type == $this->post_type) || ($screen->id == 'settings_page_wp_simple_locator') ) {
			$this->addGoogleMaps();
			wp_enqueue_script('google-maps');
			wp_enqueue_script('media-upload');
			wp_enqueue_script('thickbox');
			wp_enqueue_style('thickbox');
			wp_enqueue_script(
				'simple-locator-admin', 
				$this->plugin_dir . '/assets/js/simple-locator-admin.js', 
				array('jquery', 'jquery-ui-sortable'), 
				$this->version
			);
			$data = array( 
				'locatorNonce' => wp_create_nonce( 'wpsl_locator-locator-nonce' ),
				'upload' => __('Upload', 'wpsimplelocator'),
				'remove' => __('Remove', 'wpsimplelocator'),
				'posttype' => $this->post_type
			);
			$data = $this->importVars($data);
			wp_localize_script( 
				'simple-locator-admin', 
				'wpsl_locator', 
				$data
			);
		}
	}

	/**
	* Map Style Settings Screen
	*/
	public function mapSettings()
	{
		$screen = get_current_screen();
		if ( ($screen->id == 'settings_page_wp_simple_locator') && (isset($_GET['tab'])) && ($_GET['tab'] == 'map') ){
			wp_enqueue_script(
				'simple-locator-admin-maps', 
				$this->plugin_dir . '/assets/js/simple-locator-admin-maps.js', 
				array('jquery'), 
				$this->version
			);
			wp_localize_script( 
				'simple-locator-admin-maps', 
				'wpsl_locator_mapstyles', 
				$this->mapStyleData()
			);
		}
	}

	/**
	* Default Map Settings Screen
	*/
	public function defaultMapSettings()
	{
		$screen = get_current_screen();
		if ( ($screen->id == 'settings_page_wp_simple_locator') && (isset($_GET['tab'])) && ($_GET['tab'] == 'defaultmap') ){
			wp_enqueue_script(
				'simple-locator-admin-defaultmap', 
				$this->plugin_dir . '/assets/js/simple-locator-admin-defaultmap.js', 
				array('jquery'), 
				$this->version
			);
			wp_localize_script( 
				'simple-locator-admin-defaultmap', 
				'wpsl_locator_defaultmap', 
				array(
					'enabled' => $this->settings_repo->showDefaultMap(),
					'latitude' => $this->settings_repo->defaultMap('latitude'),
					'longitude' => $this->settings_repo->defaultMap('longitude'),
					'zoom' => intval($this->settings_repo->defaultMap('zoom')),
					'searchtext' => __('Search for a location', 'wpsimplelocator'),
					'styles' => $this->styles_repo->getLocalizedStyles(),
					'mappin' => get_option('wpsl_map_pin')
				)
			);
		}
	}

	/**
	* Add keys needed for importing process
	*/
	private function importVars($data)
	{
		if ( !isset($_GET['tab']) || $_GET['tab'] !== "import" || !isset($_GET['step']) ){
			$data['isimport'] = "false";
			return $data;
		} 
		$data['isimport'] = "true";
		$data['importstep'] = $_GET['step'];
		$data['Row'] = 'Showing Row';
		$data['pause'] = __('Pause', 'wpsimplelocator');
		$data['pause_continue'] = __('Continue', 'wpsimplelocator');
		$data['post_type'] = get_option('wpsl_post_type');
		$data['choose_column'] = 'Choose Column';
		$data['required'] = __('Column and field selections required for import.', 'wpsimplelocator');
		$data['required_address'] = __('An address field is required for import.', 'wpsimplelocator');
		$data['required_title'] = __('A post title field is required for import.', 'wpsimplelocator');
		$data['title'] = __('Post Title', 'wpsimplelocator');
		$data['content'] = __('Post Content', 'wpsimplelocator');
		return $data;
	}

}