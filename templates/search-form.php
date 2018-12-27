<?php
global $post;

// Is this a widget form or a shortcode form
if ( $this->options['widget'] ) : ?>
	<div class="simple-locator-widget">
	<span id="widget"></span>
<?php endif; ?>

<div class="simple-locator-form" data-simple-locator-form-container data-simple-locator-results-wrapper>
<form 
	method="<?php echo strtoupper($this->options['formmethod']); ?>" 
	action="<?php echo get_the_permalink($this->options['resultspage']); ?>" 
	data-simple-locator-form
	<?php
	if ( isset($this->options['allowemptyaddress']) && $this->options['allowemptyaddress'] == 'true' ) : 
		echo 'class="allow-empty" data-simple-locator-form-allow-empty';
	endif;
	if ( $this->options['mapcontrols'] != 'show' ) : 
		echo ' data-simple-locator-hide-map-controls="true"';
	endif;
	if ( $this->options['mapcontainer'] != '') :
		echo ' data-simple-locator-map-container="' . $this->options['mapcontainer'] . '"';
	endif;
	if ( $this->options['resultscontainer'] != '') :
		echo ' data-simple-locator-results-container="' . $this->options['resultscontainer'] . '"';
	endif;
	if ( $this->options['ajax'] ) : 
		echo ' data-simple-locator-ajax-form="true"';
	endif;
	?>
	 data-simple-locator-map-control-position="<?php echo $this->options['mapcontrolsposition']; ?>">

	<div class="wpsl-error alert alert-error" style="display:none;" data-simple-locator-form-error></div>
	<div class="address-input form-field">
		<label for="wpsl_address"><?php echo $this->options['addresslabel']; ?></label>
		<input type="text" id="wpsl_address" data-simple-locator-input-address name="address" class="address wpsl-search-form" placeholder="<?php echo $this->options['placeholder']; ?>" <?php if ( $this->options['autocomplete'] ) echo ' data-simple-locator-autocomplete'; ?> />
	</div>
	<div class="distance form-field">
		<label for="wpsl_distance"><?php _e('Distance', 'simple-locator'); ?></label>
		<select name="wpsl_distance" class="distanceselect" data-simple-locator-input-distance>
			<?php echo $this->options['distance_options']; ?>
		</select>
	</div>
	<?php if ( $this->options['taxonomies'] ) : ?>
	<div class="wpsl-taxonomy-filters">
		<?php
		if ( $this->options['taxonomy_field_type'] == 'select' ) :
			foreach ( $this->options['taxonomies'] as $tax_name => $taxonomy ) : ?>
				<div class="wpsl-taxonomy-filter">
				<label for="wpsl_taxonomy_<?php echo $tax_name; ?>" class="taxonomy-label"><?php echo $taxonomy['label']; ?></label>
				<select id="wpsl_taxonomy_<?php echo $tax_name; ?>" name="taxfilter[<?php echo $tax_name; ?>]" data-simple-locator-taxonomy-select="<?php echo $tax_name; ?>">
					<option value="">--</option>
					<?php
					foreach ( $taxonomy['terms'] as $term ){
						echo '<option value="' . $term->term_id . '" />' . $term->name . '</option>';
					} ?>
				</select>
				</div><!-- .taxonomy -->
			<?php
			endforeach;
		else :
			foreach ( $this->options['taxonomies'] as $tax_name => $taxonomy ) : ?>
				<div class="wpsl-taxonomy-filter checkboxes">
				<label class="taxonomy-label"><?php echo $taxonomy['label']; ?></label>
				<?php
				foreach ( $taxonomy['terms'] as $term ){
					echo '<label for="wpsl_taxonomy_' . $tax_name . '"><input type="checkbox" id="wpsl_taxonomy_' . $tax_name . '" name="taxfilter[' . $tax_name . '][]" value="' . $term->term_id . '" data-simple-locator-taxonomy-checkbox="' . $tax_name . '" />' .$term->name . '</label>';
				}
				?>
				</div><!-- .taxonomy -->
			<?php
			endforeach;
		endif; ?>
		</div><!-- .wpsl-taxonomy-filters -->
	<?php endif; // taxonomies ?>
	<div class="submit">
		<input type="hidden" data-simple-locator-input-latitude name="latitude" class="latitude" />
		<input type="hidden" data-simple-locator-input-longitude name="longitude" class="longitude" />
		<input type="hidden" data-simple-locator-input-formatted-location name="formatted_location" />
		<input type="hidden" name="page_num" value="1" />
		<input type="hidden" name="search_page" value="<?php echo $post->ID; ?>" />
		<input type="hidden" name="results_page" value="<?php echo $this->options['resultspage']; ?>" />
		<input type="hidden" data-simple-locator-input-limit name="per_page" value="<?php echo $this->options['perpage']; ?>" />
		<input type="hidden" name="simple_locator_results" value="true" />
		<input type="hidden" name="method" value="<?php echo $this->options['formmethod']; ?>" />
		<input type="hidden" name="mapheight" value="<?php echo $this->options['mapheight']; ?>" />
		<input type="hidden" data-simple-locator-input-geocode name="geolocation" />
		<input type="hidden" data-simple-locator-input-unit name="unit" value="<?php echo $this->unit_raw; ?>" class="unit" />
		<?php if ( $this->options['formmethod'] == 'get' ) : // Fixes GET forms on sites without pretty permalinks ?>
		<input type="hidden" name="page_id" value="<?php echo $this->options['resultspage']; ?>">
		<?php endif; ?>
		<button type="submit" data-simple-locator-submit class="wpslsubmit"><?php echo html_entity_decode($this->options['buttontext']); ?></button>
		<div class="geo_button_cont"></div>
		<div class="wpsl-icon-spinner"><div class="wpsl-icon-spinner-image"><img src="<?php echo apply_filters('simple_locator_results_loading_spinner', \SimpleLocator\Helpers::plugin_url() . '/assets/images/loading-spinner.svg'); ?>" class="wpsl-spinner-image" /></div></div>
		</div>
	</form>
	<?php 
	if ( $this->options['mapcontainer'] == '' ) :
		$out = '<div data-simple-locator-map class="wpsl-map"';
		if ( isset($this->options['mapheight']) && $this->options['mapheight'] !== "" ) 
			$out .= 'style="height:' . $this->options['mapheight'] . 'px;"';
		if ( $this->settings_repo->showDefaultMap() ) 
			$out .= ' data-simple-locator-default-enabled="true"';
		if ( $this->options['perpage'] !== '' ) 
			$out .= ' data-per-page="' . $this->options['perpage'] . '"';
		if ( $this->options['showall'] !== '' ) 
			$out .= ' data-simple-locator-all-locations-map="' . $this->options['showall'] . '" data-include-listing="true"';
		$out .= '></div><!-- .wpsl-map -->';
		echo $out;
	endif;
	?>

	<div data-simple-locator-results class="wpsl-results"></div>
</div><!-- .simple-locator-form -->

<?php
if ( $this->options['widget'] ) echo '</div><!-- .simple-locator-widget -->';