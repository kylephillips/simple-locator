<?php 
namespace SimpleLocator\WPData;

/**
* Locations Post Type
*/
class PostTypes 
{
	/**
	* Post Type Option
	* @var array
	*/
	private $pt_labels;

	function __construct()
	{
		$this->setLabels();
		add_action( 'init', [$this, 'registerLocation']);
		add_action( 'init', [$this, 'registerMaps']);
		add_action( 'init', [$this, 'registerImports']);
		add_action( 'init', [$this, 'registerImportTemplate']);
		add_filter( 'manage_location_posts_columns', [$this,'locations_table_head']);
		add_action( 'manage_location_posts_custom_column', [$this, 'locations_table_columns'], 10, 2);
	}

	/**
	* Public method to manually register type, used in activation for Rewrite flush
	* @since 1.2.2
	*/
	public function register()
	{
		$this->registerLocation();
		$this->registerMaps();
		$this->registerImports();
	}

	/**
	* Set the Post Type Labels from the option
	* @since 1.0.6
	*/
	private function setLabels()
	{
		$labels = get_option('wpsl_posttype_labels');
		if ( !isset($labels['menu_icon']) ) $labels['menu_icon'] = 'dashicons-post-status';
		$this->pt_labels = new \StdClass();
		$this->pt_labels->name = ( $labels['name'] !== "" ) ? $labels['name'] : 'location';
		$this->pt_labels->label = ( $labels['label'] !== "" ) ? $labels['label'] : 'Locations';
		$this->pt_labels->singular = ( $labels['singular'] !== "" ) ? $labels['singular'] : 'Location';
		$this->pt_labels->add_new = ( $labels['add_new_item'] !== "" ) ? $labels['add_new_item'] : 'Add Location';
		$this->pt_labels->edit_item = ( $labels['edit_item'] !== "" ) ? $labels['edit_item'] : 'Edit Location';
		$this->pt_labels->view_item = ( $labels['view_item'] !== "" ) ? $labels['view_item'] : 'View Location';
		$this->pt_labels->search_item = ( isset($labels['search_item']) && $labels['search_item'] !== "" ) ? $labels['search_item'] : 'Search Location';
		$this->pt_labels->slug = ( $labels['slug'] !== "" ) ? $labels['slug'] : 'location';
		$this->pt_labels->menu_icon = ( $labels['menu_icon'] !== "" ) ? $labels['menu_icon'] : 'dashicons-post-status';
		$this->pt_labels->menu_position = ( $labels['menu_position'] !== "" ) ? intval($labels['menu_position']) : 6;
	}

	/**
	* Register the Location Post Type
	*/
	public function registerLocation()
	{
		if ( $this->hideDefaultPostType() ) return;
		$labels = [
			'name' => $this->pt_labels->label,  
			'singular_name' => $this->pt_labels->singular,
			'add_new_item'=> $this->pt_labels->add_new,
			'edit_item' => $this->pt_labels->edit_item,
			'view_item' => $this->pt_labels->view_item,
			'search_items' => $this->pt_labels->search_item
		];
		$args = [
			'labels' => $labels,
			'public' => true,  
			'show_ui' => true,
			'menu_position' => $this->pt_labels->menu_position,
			'menu_icon' => $this->pt_labels->menu_icon,
			'capability_type' => 'post',  
			'hierarchical' => false,  
			'has_archive' => true,
			'show_in_rest' => true,
			'supports' => ['title', 'editor', 'excerpt', 'comments', 'thumbnail'],
			'rewrite' => ['slug' =>  $this->pt_labels->slug, 'with_front' => false]
		];

		register_post_type($this->pt_labels->name, apply_filters('simple_locator_post_type_args', $args));
	}

	/**
	* Register the Maps Post Type
	* (for setting custom map styles)
	*/
	public function registerMaps()
	{
		$labels = [
			'name' => __('Simple Locator Maps'),  
			'singular_name' => __('Simple Locator Map')
		];
		$args = [
			'labels' => $labels,
			'public' => false, 
			'show_ui' => false,
			'capability_type' => 'post',  
			'hierarchical' => false,  
			'has_archive' => false,
			'supports' => ['title','editor'],
		];
		register_post_type( 'wpslmaps' , $args );
	}

	/**
	* Register the Imports Post Type
	* (for saving import data)
	*/
	public function registerImports()
	{
		$labels = [
			'name' => __('Simple Locator Imports'),  
			'singular_name' => __('Simple Locator Import')
		];
		$args = [
			'labels' => $labels,
			'public' => false,  
			'show_ui' => false,
			'capability_type' => 'post',  
			'hierarchical' => false,  
			'has_archive' => false,
			'supports' => ['title', 'editor'],
		];
		register_post_type( 'wpslimport' , $args );
	}

	/**
	* Register the Import Templates Post Type
	* (for saving import as a template)
	*/
	public function registerImportTemplate()
	{
		$labels = [
			'name' => __('Simple Locator Import Templates'),  
			'singular_name' => __('Simple Locator Import Template')
		];
		$args = [
			'labels' => $labels,
			'public' => false,  
			'show_ui' => false,
			'capability_type' => 'post',  
			'hierarchical' => false,  
			'has_archive' => false,
			'supports' => ['title', 'editor'],
		];
		register_post_type( 'wpslimporttemplate' , $args );
	}

	/**
	* Check the post type option
	*/
	private function hideDefaultPostType()
	{
		return ( get_option('wpsl_hide_default') == 'true' ) ? true : false;
	}

	/**
	* Locations Admin Table Head
	*/	
	public function locations_table_head( $defaults )
	{
	    $defaults['address']  = __('Address', 'simple-locator');
	    $defaults['phone']    = __('Phone', 'simple-locator');
	    $defaults['website']  = __('Website', 'simple-locator');
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