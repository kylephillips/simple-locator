<?php 
$out = "";

if ( ($this->location_data['latitude'] !== "") && ($this->location_data['longitude'] !== "") ){
	$out = '<div id="locationmap" class="wpsl-map"></div>';
}

// It's the built in location post type, safe to use fields
if ( ($this->settings_repo->getLocationPostType() == 'location') && ($this->location_data['additionalfields'] == 'show') ) :

	$out .= '<div class="wpsl-location-info">';

	// Address
	$out .= '<div class="wpsl-location-address"><p>';
	if ( isset($this->location_data['address']) && $this->location_data['address'] !== "" ) 
		$out .= $this->location_data['address']; 
	if ( isset($this->location_data['city']) && $this->location_data['city'] !== "" ) 
		$out .= '<br />' . $this->location_data['city'];
	if ( isset($this->location_data['state']) && $this->location_data['state'] !== "" ) 
		$out .= ', ' . $this->location_data['state'];
	if ( isset($this->location_data['zip']) && $this->location_data['zip'] !== "" ) 
		$out .= ' ' . $this->location_data['zip'];
	$out .= '</p></div>';

	// Website
	if ( (isset($this->location_data['website'])) && ($this->location_data['website'] !== "") ){
		$url = \SimpleLocator\Helpers::checkURL($this->location_data['website']);
		$out .= '<div class="wpsl-location-website"><p>';
		$out .= __('Website', 'wpsimplelocator') . ':';
		$out .= ' <a href="' . $url . '" target="_blank">' . $this->location_data['website'] . '</a>';
		$out .= '</p></div>';
	}

	// Phone
	if ( isset($this->location_data['phone']) && $this->location_data['phone'] !== "" ){
		$out .= '<div class="wpsl-location-phone"><p>';
		$out .= __('Phone', 'wpsimplelocator') . ': ' . $this->location_data['phone'];
		$out .= '</p></div>';
	}

	// Additional info
	if ( isset($this->location_data['additionalinfo']) && $this->location_data['additionalinfo'] !== "" ){
		$out .= '<div class="wpsl-location-additionalinfo"><p>';
		$out .= $this->location_data['additionalinfo'];
		$out .= '</p></div>';
	}

	$out .= '</div><!-- .wpsl-location-info -->';

endif; // Post type

