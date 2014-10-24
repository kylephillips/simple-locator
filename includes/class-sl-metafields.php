<?php
/**
* Custom Meta Fields
*/
class SL_MetaFields {

	/**
	* Meta Data
	*/
	private $meta;

	function __construct()
	{
		add_action( 'add_meta_boxes', array( $this, 'metaBox' ));
		add_action( 'save_post', array($this, 'saveLocation' ));
	}


	/**
	* Register the Meta Box
	*/
	public function metaBox() 
	{
    	add_meta_box( 
    		'wpsl-meta-box', 
    		'Location Information', 
    		array($this, 'fields'), 
    		'location', 
    		'normal', 
    		'high' 
    	);
	}


	/**
	* Meta Boxes for Output
	*/
	public function fields($post)
	{
		wp_nonce_field( 'my_wpsl_meta_box_nonce', 'wpsl_meta_box_nonce' ); 
		$this->setData($post);
		include( dirname(dirname(__FILE__)) . '/views/location-meta.php' );
	}


	/**
	* Set the Field Data
	*/
	private function setData($post)
	{
		$this->meta['latitude'] = get_post_meta( $post->ID, 'wpsl_latitude', true );
		$this->meta['longitude'] = get_post_meta( $post->ID, 'wpsl_longitude', true );
		$this->meta['address'] = get_post_meta( $post->ID, 'wpsl_address', true );
		$this->meta['city'] = get_post_meta( $post->ID, 'wpsl_city', true );
		$this->meta['state'] = get_post_meta( $post->ID, 'wpsl_state', true );
		$this->meta['zip'] = get_post_meta( $post->ID, 'wpsl_zip', true );
		$this->meta['phone'] = get_post_meta( $post->ID, 'wpsl_phone', true );
		$this->meta['website'] = get_post_meta( $post->ID, 'wpsl_website', true );
		$this->meta['additionalinfo'] = get_post_meta( $post->ID, 'wpsl_additionalinfo', true );
	}



	/**
	* Save the custom post meta
	*/
	public function saveLocation( $post_id ) 
	{
		if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

		// Verify the nonce & permissions.
		if( !isset( $_POST['wpsl_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['wpsl_meta_box_nonce'], 'my_wpsl_meta_box_nonce' ) ) return;
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
	} 

}