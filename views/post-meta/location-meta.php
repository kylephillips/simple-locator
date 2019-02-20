<?php 
wp_nonce_field( 'my_wpsl_meta_box_nonce', 'wpsl_meta_box_nonce' ); 
$fields_ordered = $this->form_fields->order();
?>
<div class="wpsl-meta">
	<?php
	$html = '';
	foreach ( $fields_ordered as $method ){
		$value = ( isset($this->meta[$method]) ) ? $this->meta[$method] : null;		
		$field = ( method_exists($this->form_fields, $method) ) 
			? $this->form_fields->$method($value, $post->ID) // Included Field
			: $this->form_fields->customField($method, $value, $post->ID); // Custom Field
		
		
		if ( $method == 'map' ) :
			$html .= '<div id="wpslmap"></div>';
			continue;
		endif;
		
		if ( $method == 'latlng' ) :
			$latitude = ( isset($this->meta['latitude']) ) ? $this->meta['latitude'] : null;
			$longitude = ( isset($this->meta['longitude']) ) ? $this->meta['longitude'] : null;
			$html .= '<div class="latlng">';
			$html .= '<span>' . __('Geocode values will update on save. Fields are for display purpose only.', 'simple-locator') . '</span>';
			$html .= '<p class="wpsl-latitude-field">';
			$html .= '<label for="wpsl_latitude">' . __('Latitude', 'simple-locator') . '</label>';
			$html .= '<input type="text" name="wpsl_latitude" id="wpsl_latitude" value="' . $latitude . '" readonly />';
			$html .= '</p>';
			$html .= '<p class="lat wpsl-longitude-field">';
			$html .= '<label for="wpsl_longitude">' . __('Longitude', 'simple-locator') . '</label>';
			$html .= '<input type="text" name="wpsl_longitude" id="wpsl_longitude" value="' . $longitude . '" readonly />';
			$html .= '</p>';
			$html .= '</div>';
			continue;
		endif;
		
		$html .= '<p class="';
		foreach ( $field['css-class'] as $class ){
			$html .= $class . ' ';
		}
		$html .= '">' . $this->form_fields->output($field, $value) . '</p>';
	}
	echo $html;
	?>
	<input type="hidden" name="wpsl_custom_geo" id="wpsl_custom_geo" value="<?php echo $this->meta['custom_geo']; ?>">
</div>
<?php include('error-modal.php');?>