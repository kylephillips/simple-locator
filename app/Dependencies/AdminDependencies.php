<?php 
namespace SimpleLocator\Dependencies;

use SimpleLocator\WPData\Fields\FormFields;

/**
* Register & Enqueue Admin Styles & Scripts
*/
class AdminDependencies extends DependencyBase 
{

	public function __construct()
	{
		parent::__construct();
		add_action( 'admin_enqueue_scripts', [$this, 'styles']);
		add_action( 'admin_enqueue_scripts', [$this, 'scripts']);
		add_action( 'admin_enqueue_scripts', [$this, 'mapSettings']);
		add_action( 'admin_enqueue_scripts', [$this, 'defaultMapSettings']);
		add_action( 'admin_enqueue_scripts', [$this, 'searchHistory']);
		add_action( 'admin_head', [$this, 'acfTabs']);
	}

	/**
	* Admin Styles
	*/
	public function styles()
	{
		wp_enqueue_style(
			'simplelocator', 
			$this->plugin_dir . '/assets/css/simple-locator-admin.css', 
			[], 
			$this->version
		);
	}

	/**
	* Admin Scripts
	*/
	public function scripts()
	{
		$screen = get_current_screen();
		if ( ($screen->post_type == get_option('wpsl_post_type')) || ($screen->id == 'settings_page_wp_simple_locator') ) {
			$this->addGoogleMaps();
			$post_type = get_post_type_object(get_option('wpsl_post_type'));
			wp_enqueue_script('google-maps');
			wp_enqueue_media();
			wp_enqueue_script(
				'simple-locator-admin',
				$this->plugin_dir . '/assets/js/simple-locator-admin.min.js', 
				['jquery', 'jquery-ui-sortable', 'jquery-ui-datepicker'], 
				$this->version
			);
			$data = [
				'locatorNonce' 		=> wp_create_nonce( 'wpsl_locator-locator-nonce' ),
				'upload' 			=> __('Upload', 'simple-locator'),
				'remove' 			=> __('Remove', 'simple-locator'),
				'edit' 				=> $post_type->labels->edit_item,
				'view'				=> $post_type->labels->view_item,
				'save'				=> __('Save', 'simple-locator'),
				'preview'			=> __('Preview', 'simple-locator'),
				'cancel'			=> __('Cancel', 'simple-locator'),
				'posttype' 			=> $this->post_type,
				'posttype_setting'	=> get_option('wpsl_post_type'),
				'lat_field'			=> $this->settings_repo->getGeoField('lat'),
				'lng_field'			=> $this->settings_repo->getGeoField('lng'),
				'map_field'			=> $this->settings_repo->acfMapField(),
				'location_not_found'	=> __('The address could not be found at this time.', 'simple-locator'),
				'api_load_error'	=> __('There was an error loading the Google Maps API. This may be due to a missing or invalid API key.', 'simple-locator'),
				'show_listing_map'	=> $this->settings_repo->includeAdminListMap(),
				'mapstyles' 		=> $this->styles_repo->getLocalizedStyles(),
				'mappin' 			=> $this->settings_repo->mapPin(),
				'acf_tab'			=> $this->settings_repo->acfTab(),
				'quickedit_geocode_error' => __('The location was successfully saved, but could not be geocoded.', 'simple-locator'),
				'loading_spinner'	=> \SimpleLocator\Helpers::plugin_url() . '/assets/images/loading-spinner.svg'
			];
			$data['form_fields_order'] = ( new FormFields )->order();
			$data['form_fields'] = ( new FormFields )->allFields();
			$data = $this->importVars($data);
			$data['confirm_undo'] 	= __('Are you sure you want to undo this import? This action cannot be undone.', 'simple-locator');
			$data['confirm_redo'] 	= __('Are you sure you want to redo this import? This will erase any currently pending imports.', 'simple-locator');
			$data['confirm_remove']	= __('Are you sure you want to remove this import record? You will no longer be able to redo or undo this import. All imported post data will remain.', 'simple-locator');
			$data['edit_listing'] = ( $screen->id == 'edit-' . $screen->post_type ) ? '1' : '0';
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
			wp_localize_script( 
				'simple-locator-admin', 
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
			wp_localize_script( 
				'simple-locator-admin', 
				'wpsl_locator_defaultmap', 
				[
					'enabled' 		=> $this->settings_repo->showDefaultMap(),
					'latitude' 		=> $this->settings_repo->defaultMap('latitude'),
					'longitude' 	=> $this->settings_repo->defaultMap('longitude'),
					'zoom' 			=> intval($this->settings_repo->defaultMap('zoom')),
					'searchtext' 	=> __('Search for a location', 'simple-locator'),
					'styles' 		=> $this->styles_repo->getLocalizedStyles(),
					'mappin' 		=> $this->settings_repo->mapPin()
				]
			);
		}
	}

	/**
	* Search History Page
	*/
	public function searchHistory()
	{
		$screen = get_current_screen();
		if ( ($screen->id == 'settings_page_wp_simple_locator') && (isset($_GET['tab'])) && ($_GET['tab'] == 'search-history') ){
			wp_localize_script( 
				'simple-locator-admin', 
				'wpsl_locator_searchhistory', 
				[
					'styles' 		=> $this->styles_repo->getLocalizedStyles(),
					'mappin' 		=> get_option('wpsl_map_pin'),
					'userIp' 		=> __('User IP', 'simple-locator'),
					'searchTermFormatted' => __('Search Term Formatted', 'simple-locator'),
					'searchTerm' => __('Search Term', 'simple-locator'),
					'distance' 		=> __('Distance', 'simple-locator')
				]
			);
		}
	}

	/**
	* Add keys needed for importing process
	*/
	private function importVars($data)
	{
		$transient = get_transient('wpsl_import_file');
		if ( !isset($_GET['tab']) || $_GET['tab'] !== "import" || !isset($_GET['step']) ){
			$data['isimport'] = "false";
			return $data;
		} 
		$data['isimport'] 			= "true";
		$data['importstep'] 		= $_GET['step'];
		$data['Row'] 				= 'Showing Row';
		$data['pause'] 				= __('Pause Import', 'simple-locator');
		$data['pause_continue'] 	= __('Continue Import', 'simple-locator');
		$data['post_type'] 			= get_option('wpsl_post_type');
		$data['choose_column'] 		= 'Choose Column';
		$data['required'] 			= __('Column and field selections required for import.', 'simple-locator');
		$data['required_address'] 	= __('An address field is required for import.', 'simple-locator');
		$data['required_title'] 	= __('A post title field is required for import.', 'simple-locator');
		$data['title'] 				= __('Post Title', 'simple-locator');
		$data['content'] 			= __('Post Content', 'simple-locator');
		$data['publish_date']		= __('Publish Date', 'simple-locator');
		$data['publish_date_gmt']	= __('Publish Date (GMT)', 'simple-locator');
		$data['modified_date']		= __('Modified Date', 'simple-locator');
		$data['modified_date_gmt']	= __('Modified Date (GMT)', 'simple-locator');
		$data['slug']				= __('Post Slug/Name', 'simple-locator');
		$data['status']				= __('Post Status', 'simple-locator');
		$data['excerpt']			= __('Post Excerpt', 'simple-locator');
		$data['taxonomies']			= __('Taxonomies', 'simple-locator');
		$data['wordpress_fields']	= __('WordPress Fields', 'simple-locator');
		$data['custom_fields']		= __('Custom Fields', 'simple-locator');
		$data['importoffset'] 		= ( isset($transient['last_imported']) ) ? $transient['last_imported'] : 0;
		if ( isset($transient['skip_first']) 
			&& $transient['skip_first'] 
			&& $data['importoffset'] == 0 ) $data['importoffset'] = 1;
		$data['complete_count'] 	= ( isset($transient['complete_rows']) ) ? $transient['complete_rows'] : 0;
		$data['error_count'] 		= ( isset($transient['error_rows']) ) ? count($transient['error_rows']) : 0;
		return $data;
	}

	/**
	* Hide the meta box if acf tab functionality is enabled
	*/
	public function acfTabs()
	{
		$screen = get_current_screen();
		if ( ($screen->post_type == get_option('wpsl_post_type')) ) {
			$tab = $this->settings_repo->acfTab();
			if ( !$tab || $tab == '' ) return;
			echo '<style>#wpsl-meta-box {display:none;}</style>';
		}
	}
}