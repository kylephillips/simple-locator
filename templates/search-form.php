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
if ( $widget ) : 
?>
<div class="simple-locator-widget">
<span id="widget"></span>
<?php endif; ?>

<div class="simple-locator-form" data-simple-locator-form-container <?php if ( !$template_variables['resultscontainer'] ) echo 'data-simple-locator-results-wrapper'; ?>>

	<?php do_action('simple_locator_form_opening', $template_variables); ?>

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
	<?php do_action('simple_locator_form_taxonomy_fields', $template_variables); ?>
	<div class="submit">
		<?php
		do_action('simple_locator_form_hidden_fields', $template_variables);
		?>
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
		if ( $showall ) $out .= ' data-simple-locator-all-locations-map="' . $showall . '" data-include-listing="true"';
		$out .= '></div><!-- .wpsl-map -->';
		echo $out;
	endif;
	?>

	<div data-simple-locator-results class="wpsl-results"></div>
</div><!-- .simple-locator-form -->

<?php
if ( $widget ) echo '</div><!-- .simple-locator-widget -->';