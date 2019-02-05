<?php
namespace SimpleLocator\Form;

class SearchForm
{
	public function __construct()
	{
		add_action('simple_locator_form_opening', [$this, 'formOpening']);
		add_action('simple_locator_form_taxonomy_fields', [$this, 'fieldsTaxonomy']);
		add_action('simple_locator_form_hidden_fields', [$this, 'fieldsHidden']);
	}

	public function formOpening($options)
	{
		$out = '<form method="' . strtoupper($options['formmethod']) . '" action="' . get_the_permalink($options['resultspage']) . '" data-simple-locator-form ';
		if ( !$options['mapcontrols'] ) $out .= ' data-simple-locator-hide-map-controls="true"';
		if ( $options['mapcontainer'] ) $out .= ' data-simple-locator-map-container="' . $options['mapcontainer'] . '"';
		if ( $options['resultscontainer'] ) $out .= ' data-simple-locator-results-container="' . $options['resultscontainer'] . '"';
		if ( $options['ajax'] ) $out .= ' data-simple-locator-ajax-form="true"';
	 	$out .= 'data-simple-locator-map-control-position="' . $options['mapcontrolsposition'] . '"';
	 	if ( $options['allowemptyaddress'] ) $out .= ' class="allow-empty" data-simple-locator-form-allow-empty="true"';
	 	$out .= '>';
	 	echo $out;
	}

	public function fieldsTaxonomy($options)
	{
		$taxonomies = apply_filters('simple_locator_form_taxonomies', $options['taxonomies'], $options);
		if ( !$taxonomies ) return;
		$out = '<div class="wpsl-taxonomy-filters">';
		
		if ( $options['taxonomy_field_type'] == 'select' ) :
			foreach ( $taxonomies as $tax_name => $taxonomy ) :
				$tax_obj = get_taxonomy($tax_name);
				$out .= '<div class="wpsl-taxonomy-filter">';
				$out .= '<label for="wpsl_taxonomy_' . $tax_name . '" class="taxonomy-label">' . $taxonomy['label'] . '</label>';
				$out .= '<select id="wpsl_taxonomy_' . $tax_name . '" name="taxfilter[' . $tax_name . ']" data-simple-locator-taxonomy-select="' . $tax_name . '">';
				$out .= '<option value="">' . sprintf(esc_html__('--Select %s--', 'simple-locator'), $tax_obj->labels->singular_name) . '</option>';
				foreach ( $taxonomy['terms'] as $term ){
					$out .= '<option value="' . $term->term_id . '" />' . $term->name . '</option>';
				}
				$out .= '</select></div><!-- .taxonomy -->';
				$out .= '</div><!-- .wpsl-taxonomy-filters -->';
			endforeach;
			echo $out;
			return;
		endif;

		// Checkboxes
		foreach ( $taxonomies as $tax_name => $taxonomy ) :
			$out .= '<div class="wpsl-taxonomy-filter checkboxes">';
			$out .= '<label class="taxonomy-label">' . $taxonomy['label'] . '</label>';
			foreach ( $taxonomy['terms'] as $term ){
				$out .= '<label for="wpsl_taxonomy_' . $tax_name . '"><input type="checkbox" id="wpsl_taxonomy_' . $tax_name . '" name="taxfilter[' . $tax_name . '][]" value="' . $term->term_id . '" data-simple-locator-taxonomy-checkbox="' . $tax_name . '" />' .$term->name . '</label>';
			}
			$out .= '</div><!-- .taxonomy -->';
		endforeach;
		$out .= '</div><!-- .wpsl-taxonomy-filters -->';
		echo $out;
	}

	public function fieldsHidden($options)
	{
		global $post;
		$out  = '<input type="hidden" data-simple-locator-input-latitude name="latitude" class="latitude" />
		<input type="hidden" data-simple-locator-input-longitude name="longitude" class="longitude" />
		<input type="hidden" data-simple-locator-input-formatted-location name="formatted_location" />
		<input type="hidden" name="page_num" value="1" />
		<input type="hidden" name="search_page" value="' . $post->ID . '" />
		<input type="hidden" name="results_page" value="' . $options['resultspage'] . '" />
		<input type="hidden" data-simple-locator-input-limit name="per_page" value="' . $options['perpage'] . '" />
		<input type="hidden" name="simple_locator_results" value="true" />
		<input type="hidden" name="method" value="' . $options['formmethod'] . '" />
		<input type="hidden" name="mapheight" value="' . $options['mapheight'] . '" />
		<input type="hidden" data-simple-locator-input-geocode name="geolocation" />
		<input type="hidden" data-simple-locator-input-unit name="unit" value="' . $options['unit_raw'] . '" class="unit" />';
		// Fixes GET forms on sites without pretty permalinks
		if ( $options['formmethod'] == 'get' ) $out .= '<input type="hidden" name="page_id" value="' . $options['resultspage'] . '">';
		echo $out;
	}
}