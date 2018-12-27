<?php
/**
 * The Template for displaying Simple Locator's Search Form
 *
 * This template can be overridden by copying it to yourtheme/simple-locator/search-form.php.
 *
 * @package SimpleLocator
 * @version 2.0.1
 *
 * All variables are available in a $template_variables array. var_dump($template_variables) to see the array.
 */
global $post;
if ( $widget ) : 
?>
<div class="simple-locator-widget">
<span id="widget"></span>
<?php endif; ?>

<div class="simple-locator-form" data-simple-locator-form-container data-simple-locator-results-wrapper>
<form 
	method="<?php echo strtoupper($formmethod); ?>" 
	action="<?php echo get_the_permalink($resultspage); ?>" 
	data-simple-locator-form
	<?php
	if ( $allowemptyaddress ) echo 'class="allow-empty" data-simple-locator-form-allow-empty';
	if ( !$mapcontrols ) echo ' data-simple-locator-hide-map-controls="true"';
	if ( $mapcontainer ) echo ' data-simple-locator-map-container="' . $mapcontainer . '"';
	if ( $resultscontainer ) echo ' data-simple-locator-results-container="' . $resultscontainer . '"';
	if ( $ajax ) echo ' data-simple-locator-ajax-form="true"';
	?>
	 data-simple-locator-map-control-position="<?php echo $mapcontrolsposition; ?>">

	<div class="wpsl-error alert alert-error" style="display:none;" data-simple-locator-form-error></div>
	<div class="address-input form-field">
		<label for="wpsl_address"><?php echo $addresslabel; ?></label>
		<input type="text" id="wpsl_address" data-simple-locator-input-address name="address" class="address wpsl-search-form" placeholder="<?php echo $placeholder; ?>" <?php if ( $autocomplete ) echo ' data-simple-locator-autocomplete'; ?> />
	</div>
	<div class="distance form-field">
		<label for="wpsl_distance"><?php _e('Distance', 'simple-locator'); ?></label>
		<select name="wpsl_distance" class="distanceselect" data-simple-locator-input-distance>
			<?php echo $distance_options; ?>
		</select>
	</div>
	<?php if ( $taxonomies ) : ?>
	<div class="wpsl-taxonomy-filters">
		<?php
		if ( $taxonomy_field_type == 'select' ) :
			foreach ( $taxonomies as $tax_name => $taxonomy ) : ?>
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
			foreach ( $taxonomies as $tax_name => $taxonomy ) : ?>
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
		<input type="hidden" name="results_page" value="<?php echo $resultspage; ?>" />
		<input type="hidden" data-simple-locator-input-limit name="per_page" value="<?php echo $perpage; ?>" />
		<input type="hidden" name="simple_locator_results" value="true" />
		<input type="hidden" name="method" value="<?php echo $formmethod; ?>" />
		<input type="hidden" name="mapheight" value="<?php echo $mapheight; ?>" />
		<input type="hidden" data-simple-locator-input-geocode name="geolocation" />
		<input type="hidden" data-simple-locator-input-unit name="unit" value="<?php echo $unit_raw; ?>" class="unit" />
		<?php if ( $formmethod == 'get' ) : // Fixes GET forms on sites without pretty permalinks ?>
		<input type="hidden" name="page_id" value="<?php echo $resultspage; ?>">
		<?php endif; ?>
		<button type="submit" data-simple-locator-submit class="wpslsubmit"><?php echo html_entity_decode($buttontext); ?></button>
		<div class="geo_button_cont"></div>
		<div class="wpsl-icon-spinner"><div class="wpsl-icon-spinner-image"><img src="<?php echo apply_filters('simple_locator_results_loading_spinner', \SimpleLocator\Helpers::plugin_url() . '/assets/images/loading-spinner.svg'); ?>" class="wpsl-spinner-image" /></div></div>
		</div>
	</form>
	<?php 
	if ( !$mapcontainer ) :
		$out = '<div data-simple-locator-map class="wpsl-map"';
		if ( isset($mapheight) && $mapheight !== "" ) $out .= 'style="height:' . $mapheight . 'px;"';
		if ( $show_default_map ) $out .= ' data-simple-locator-default-enabled="true"';
		if ( $perpage !== '' ) $out .= ' data-per-page="' . $perpage . '"';
		if ( $showall !== '' ) $out .= ' data-simple-locator-all-locations-map="' . $showall . '" data-include-listing="true"';
		$out .= '></div><!-- .wpsl-map -->';
		echo $out;
	endif;
	?>

	<div data-simple-locator-results class="wpsl-results"></div>
</div><!-- .simple-locator-form -->

<?php
if ( $widget ) echo '</div><!-- .simple-locator-widget -->';