<?php
/*
Plugin Name: WP Simple Locator
Plugin URI: https://github.com/kylephillips/wp-simple-locator
Description: Simple locator for Wordpress. Can be used for store or any other type of location. Simply add the shortcode [wp_simple_locator] to add the locator.
Version: 1.0
Author: Kyle Phillips
Author URI: https://github.com/kylephillips
License: GPL
*/


require_once('lib/shortcode.php');
require_once('lib/form-handler.php');
require_once('lib/settings.php');


/**
* Primary Plugin Class
*/
class WPSimpleLocator {

	public function __construct(){
		add_action( 'init', array( $this, 'register_post_type') );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ));
		add_action( 'save_post', array($this, 'meta_box_save' ));
		add_action( 'wp_print_scripts', array($this, 'deregister_wpml_script'));
		add_action( 'admin_head', array($this, 'admin_styes'));
		add_action( 'wp_enqueue_scripts', array($this, 'enqueueDependencies'));
		add_filter( 'manage_location_posts_columns', array($this,'locations_table_head'));
		add_action( 'manage_location_posts_custom_column', array($this, 'locations_table_columns'), 10, 2);
		add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array($this, 'settings_link' ) );

		if ( is_admin() ) {
			// Ajax form handler
			add_action( 'wp_ajax_nopriv_locate', 'wpsl_form_handler' );
			add_action( 'wp_ajax_locate', 'wpsl_form_handler' );
		}
	}


	/**
	* Register the Location Post Type
	*/
	public function register_post_type()
	{
		$labels = array(
			'name' => __('Locations'),  
		    'singular_name' => __('Location'),
			'add_new_item'=> 'Add Location',
			'edit_item' => 'Edit Location',
			'view_item' => 'View Location'
		);
		$args = array(
		 	'labels' => $labels,
		    'public' => true,  
		    'show_ui' => true,
			'menu_position' => 5,
		    'capability_type' => 'post',  
		    'hierarchical' => true,  
		    'has_archive' => true,
		    'supports' => array('title','editor','thumbnail'),
		    'rewrite' => array('slug' => 'location', 'with_front' => false)
		);
		register_post_type( 'location' , $args );
	}


	/**
	* Customize the admin table head
	*/	
	public function locations_table_head( $defaults )
	{
	    $defaults['address']  = 'Address';
	    $defaults['phone']    = 'Phone';
	    $defaults['website']  = 'Website';
	    return $defaults;
	}

	
	/**
	* Customize the admin table columns
	*/	
	public function locations_table_columns($column_name, $post_id)
	{
		if ( $column_name == 'address' ){
			$address = get_post_meta($post_id, 'wpsl_address', true);
			$city = get_post_meta($post_id, 'wpsl_city', true);
			$state = get_post_meta($post_id, 'wpsl_state', true);
			$zip = get_post_meta($post_id, 'wpsl_zip', true);
			echo $address . '<br />' . $city . ', ' . $state . ' ' . $zip;
		}
		if ( $column_name == 'phone' ){
			echo get_post_meta($post_id, 'wpsl_phone', true);
		}
		if ( $column_name == 'website' ){
			echo get_post_meta($post_id, 'wpsl_website', true);
		}
	}


	/**
	* Register the Meta Box
	*/
	public function add_meta_box() 
	{
    	add_meta_box( 
    		'wpsl-meta-box', 
    		'Location Information', 
    		array($this, 'meta_fields'), 
    		'location', 
    		'normal', 
    		'high' 
    	);
	}


	/**
	* Meta Boxes for Output
	*/
	public function meta_fields($post)
	{
		// Set field values
	    wp_nonce_field( 'my_wpsl_meta_box_nonce', 'wpsl_meta_box_nonce' ); 
		$latitude = get_post_meta( $post->ID, 'wpsl_latitude', true );
		$longitude = get_post_meta( $post->ID, 'wpsl_longitude', true );
		$address = get_post_meta( $post->ID, 'wpsl_address', true );
		$city = get_post_meta( $post->ID, 'wpsl_city', true );
		$state = get_post_meta( $post->ID, 'wpsl_state', true );
		$zip = get_post_meta( $post->ID, 'wpsl_zip', true );
		$phone = get_post_meta( $post->ID, 'wpsl_phone', true );
		$website = get_post_meta( $post->ID, 'wpsl_website', true );
		$additionalinfo = get_post_meta( $post->ID, 'wpsl_additionalinfo', true );

	    ?>
	    <div class="wpsl-meta">
		    <p class="full">
		    	<label for="wpsl_address">Street Address</label>
		        <input type="text" name="wpsl_address" id="wpsl_address" value="<?php echo $address; ?>" />
		    </p>
		    <p class="city">
		    	<label for="wpsl_city">City</label>
		        <input type="text" name="wpsl_city" id="wpsl_city" value="<?php echo $city; ?>" />
		    </p>
		    <p class="state">
		    	<label for="wpsl_state">State</label>
		        <input type="text" name="wpsl_state" id="wpsl_state" value="<?php echo $state; ?>" />
		    </p>
		    <p class="zip">
		    	<label for="wpsl_zip">Zip</label>
		        <input type="text" name="wpsl_zip" id="wpsl_zip" value="<?php echo $zip; ?>" />
		    </p>
		    <div id="wpslmap"></div>
		    <hr />
		    <div class="latlng">
		    	<span>Geocode values will update on save. Fields are for display purpose only.</span>
			    <p>
			        <label for="wpsl_latitude">Latitude</label>
			        <input type="text" name="wpsl_latitude" id="wpsl_latitude" value="<?php echo $latitude; ?>" />
			    </p>
			    <p class="lat">
			        <label for="wpsl_longitude">Longitude</label>
			        <input type="text" name="wpsl_longitude" id="wpsl_longitude" value="<?php echo $longitude; ?>"  />
			    </p>
			</div>
		    <hr />
		    <p class="half">
		        <label for="wpsl_phone">Phone Number</label>
		        <input type="text" name="wpsl_phone" id="wpsl_phone" value="<?php echo $phone; ?>" />
		    </p>
		    <p class="half right">
		        <label for="wpsl_website">Website</label>
		        <input type="text" name="wpsl_website" id="wpsl_website" value="<?php echo $website; ?>" />
		    </p>
		    <hr />
		    <p class="full">
		    	<label for="wpsl_additionalinfo">Additional Info</label>
		        <textarea name="wpsl_additionalinfo" id="wpsl_additionalinfo"><?php echo $additionalinfo; ?></textarea>
		    </p>
		</div><!-- .kyhi-meta -->
	    <script>
	    var form = jQuery("form[name='post']");
		jQuery(form).find("#publish").on('click', function(e){
			e.preventDefault();
			var streetaddress = jQuery('#wpsl_address').val();
			var city = jQuery('#wpsl_city').val();
			var state = jQuery('#wpsl_state').val();
			var zip = jQuery('#wpsl_zip').val();
			var address = streetaddress + ' ' + city + ' ' + state + ' ' + zip;
			geocodeAddress(address);
		});
		jQuery(document).ready(function(){
			checkMapStatus();
		});    	
	   	</script>
	    <?php
	} // kyhi_meta_output



	/**
	* Save the custom post meta
	*/
	public function meta_box_save( $post_id ) 
	{
		if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

		// Verify the nonce. If insn't there, stop the script
		if( !isset( $_POST['wpsl_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['wpsl_meta_box_nonce'], 'my_wpsl_meta_box_nonce' ) ) return;

		// Stop the script if the user does not have edit permissions
		if( !current_user_can( 'edit_post' ) ) return;

	    // Save the address
		if( isset( $_POST['wpsl_address'] ) )
			update_post_meta( $post_id, 'wpsl_address', esc_attr( $_POST['wpsl_address'] ) );

	    // Save the city
		if( isset( $_POST['wpsl_city'] ) )
			update_post_meta( $post_id, 'wpsl_city', esc_attr( $_POST['wpsl_city'] ) );

		// Save the state
		if( isset( $_POST['wpsl_state'] ) )
			update_post_meta( $post_id, 'wpsl_state', esc_attr( $_POST['wpsl_state'] ) );

		// Save the zip
		if( isset( $_POST['wpsl_zip'] ) )
			update_post_meta( $post_id, 'wpsl_zip', esc_attr( $_POST['wpsl_zip'] ) );

		// Save the latitude
		if( isset( $_POST['wpsl_latitude'] ) )
			update_post_meta( $post_id, 'wpsl_latitude', esc_attr( $_POST['wpsl_latitude'] ) );

		// Save the longitude
		if( isset( $_POST['wpsl_longitude'] ) )
			update_post_meta( $post_id, 'wpsl_longitude', esc_attr( $_POST['wpsl_longitude'] ) );

		// Save the phone
		if( isset( $_POST['wpsl_phone'] ) )
			update_post_meta( $post_id, 'wpsl_phone', esc_attr( $_POST['wpsl_phone'] ) );

		// Save the website
		if( isset( $_POST['wpsl_website'] ) )
			update_post_meta( $post_id, 'wpsl_website', esc_attr( $_POST['wpsl_website'] ) );

		// Save the additional info
		if( isset( $_POST['wpsl_additionalinfo'] ) )
			update_post_meta( $post_id, 'wpsl_additionalinfo', esc_attr( $_POST['wpsl_additionalinfo'] ) );
	} //_meta_box_save



	/**
	* Add the necessary styles & scripts
	*/
	public function admin_styes()
	{
		global $post_type;
	    if( 'location' == $post_type ){
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
				'locatorNonce' => wp_create_nonce( 'wpsl_locator-locator-nonce' )
		));
		wp_enqueue_style(
			'simple-locator', 
			plugins_url() . '/wpsimplelocator/assets/css/simple-locator.css', 
			'', 
			'1.0'
		);
	}


	/**
	* Add a link to the settings on the plugin page
	*/
	function settings_link($links)
	{ 
  		$settings_link = '<a href="options-general.php?page=wp_simple_locator">Settings</a>'; 
  		array_unshift($links, $settings_link); 
  		return $links; 
	}

}

/**
* Instantiate the Plugin Classes
*/
$wpsimplelocator = new WPSimpleLocator;
$wpsimplelocatorsettings = new WPSimpleLocatorSettings;
$wpsl_shortcode = new WPSLShortcode;


?>