<?php namespace SimpleLocator\WPData;
/**
* Locations Post Type
*/
class PostTypes {

	/**
	* Post Type Option
	* @var array
	*/
	private $pt_labels;

	function __construct()
	{
		$this->setLabels();
		add_action( 'init', array( $this, 'registerLocation') );
		add_action( 'init', array( $this, 'registerMaps') );
		add_filter( 'manage_location_posts_columns', array($this,'locations_table_head'));
		add_action( 'manage_location_posts_custom_column', array($this, 'locations_table_columns'), 10, 2);
	}

	/**
	* Set the Post Type Labels from the option
	* @since 1.0.6
	*/
	private function setLabels()
	{
		$labels = get_option('wpsl_posttype_labels');
		$this->pt_labels = new \StdClass();
		$this->pt_labels->name = ( $labels['name'] !== "" ) ? $labels['name'] : 'location';
		$this->pt_labels->label = ( $labels['label'] !== "" ) ? $labels['label'] : 'Locations';
		$this->pt_labels->singular = ( $labels['singular'] !== "" ) ? $labels['singular'] : 'Location';
		$this->pt_labels->add_new = ( $labels['add_new_item'] !== "" ) ? $labels['add_new_item'] : 'Add Location';
		$this->pt_labels->edit_item = ( $labels['edit_item'] !== "" ) ? $labels['edit_item'] : 'Edit Location';
		$this->pt_labels->view_item = ( $labels['view_item'] !== "" ) ? $labels['view_item'] : 'View Location';
		$this->pt_labels->slug = ( $labels['slug'] !== "" ) ? $labels['slug'] : 'location';
		$this->pt_labels->menu_position = ( $labels['menu_position'] !== "" ) ? intval($labels['menu_position']) : 6;
	}


	/**
	* Register the Location Post Type
	*/
	public function registerLocation()
	{
		$labels = array(
			'name' => $this->pt_labels->label,  
			'singular_name' => $this->pt_labels->singular,
			'add_new_item'=> $this->pt_labels->add_new,
			'edit_item' => $this->pt_labels->edit_item,
			'view_item' => $this->pt_labels->view_item
		);
		$args = array(
			'labels' => $labels,
			'public' => true,  
			'show_ui' => $this->check_post_type(),
			'menu_position' => $this->pt_labels->menu_position,
			'menu_icon' => 'dashicons-post-status',
			'capability_type' => 'post',  
			'hierarchical' => false,  
			'has_archive' => true,
			'supports' => array('title','editor','thumbnail'),
			'rewrite' => array('slug' =>  $this->pt_labels->slug, 'with_front' => false)
		);
		register_post_type(  $this->pt_labels->name , $args );
	}


	/**
	* Register the Maps Post Type
	* (for setting custom map styles)
	*/
	public function registerMaps()
	{
		$labels = array(
			'name' => __('Simple Locator Maps'),  
			'singular_name' => __('Simple Locator Map')
		);
		$args = array(
			'labels' => $labels,
			'public' => false,  
			'show_ui' => false,
			'capability_type' => 'post',  
			'hierarchical' => false,  
			'has_archive' => false,
			'supports' => array('title','editor','thumbnail'),
		);
		register_post_type( 'wpslmaps' , $args );
	}


	/**
	* Check the post type option
	*/
	private function check_post_type()
	{
		return ( get_option('wpsl_post_type') == 'location' ) ? true : false;
	}


	/**
	* Locations Admin Table Head
	*/	
	public function locations_table_head( $defaults )
	{
	    $defaults['address']  = __('Address', 'wpsimplelocator');
	    $defaults['phone']    = __('Phone', 'wpsimplelocator');
	    $defaults['website']  = __('Website', 'wpsimplelocator');
	    return $defaults;
	}

	
	/**
	* Locations Admin Table Columns
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

}