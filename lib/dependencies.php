<?php
/**
* Styles & Scripts required by plugin
*/
class WPSLDependencies {

	function __construct()
	{
		add_action( 'admin_head', array($this, 'admin_styles'));
		add_action( 'wp_enqueue_scripts', array($this, 'enqueueDependencies'));
		add_action( 'wp_print_scripts', array($this, 'deregister_wpml_script'));
	}


	/**
	* Add the necessary styles & scripts
	*/
	public function admin_styles($page)
	{
		echo '<link rel="stylesheet" href="' . plugins_url() . '/wpsimplelocator/assets/css/wpsl_admin_styles.css' . '" type="text/css">';
		echo "\n";
		echo '<script src="' . plugins_url() . '/wpsimplelocator
		/assets/js/wpsl_admin.js"></script>';
		echo "\n";
		if ( get_option('wpsl_google_api_key') ){
			echo '<script src="http://maps.google.com/maps/api/js?key=' . get_option('wpsl_google_api_key') . '&sensor=false"></script>';
		} else {
			echo '<script src="http://maps.google.com/maps/api/js?sensor=false"></script>';
		}
	}


	/**
	* Enqueue the front-end scripts & styles
	*/
	public function enqueueDependencies()
	{
		wp_enqueue_script(
			'wpsl-locator', 
			plugins_url() . '/wpsimplelocator/assets/js/wpsl-locator.js', 
			'jquery', '1.0', 
			true
		);
		wp_localize_script( 
			'wpsl-locator', 
			'wpsl_locator', 
			array( 
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'locatorNonce' => wp_create_nonce( 'wpsl_locator-locator-nonce' ),
				'distance' => __( 'Distance', 'wpsimplelocator' ), 
				'website' => __('Website', 'wpsimplelocator'),
				'location' => __('location', 'wpsimplelocator'),
				'locations' => __('locations', 'wpsimplelocator'),
				'found_within' => __('found within', 'wpsimplelocator'),
				'phone' => __('Phone', 'wpsimplelocator')
		));
		wp_enqueue_style(
			'simple-locator', 
			plugins_url() . '/wpsimplelocator/assets/css/simple-locator.css', 
			'', 
			'1.0'
		);
	}


	/**
	* Deregister WPMLs script on counselor edit screen to fix conflict bug
	*/
	public function deregister_wpml_script()
	{
	  global $post_type;
	  if ( 'location' == $post_type ){
	    wp_deregister_script('sitepress-post-edit');
	    wp_dequeue_script('sitepress-post-edit');
	  }
	}

}