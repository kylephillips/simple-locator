<?php
/**
* Custom Meta Fields
*/
class WPSLMetaFields {

	function __construct()
	{
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ));
		add_action( 'save_post', array($this, 'meta_box_save' ));
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
	}



	/**
	* Save the custom post meta
	*/
	public function meta_box_save( $post_id ) 
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