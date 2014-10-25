<p>
	<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
	<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
</p>
<p>
	<label for="<?php echo $this->get_field_id( 'distance_options' ); ?>"><?php _e( 'Distance Options (Comma Separated Numbers)' ); ?></label> 
	<input class="widefat" id="<?php echo $this->get_field_id( 'distance_options' ); ?>" name="<?php echo $this->get_field_name('distance_options'); ?>" type="text" value="<?php echo esc_attr( $distance_options ); ?>" />
</p>