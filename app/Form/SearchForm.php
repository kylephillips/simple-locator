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
		
		$taxonomy_fields = [];
		foreach ( $taxonomies as $tax_name => $taxonomy ){
			$taxonomy_fields[] = $this->taxonomyField($tax_name, $taxonomy, $options);
		}

		$out = '<div class="wpsl-taxonomy-filters">';

		foreach ( $taxonomy_fields as $field ) :

			if ( $field->type == 'select' ) :
				$out .= '<div class="wpsl-taxonomy-filter taxonomy-' . $field->taxonomy . '">';
				$out .= '<label for="wpsl_taxonomy_' . $field->taxonomy . '" class="taxonomy-label">' . $field->label . '</label>';
				$out .= '<select id="wpsl_taxonomy_' . $field->taxonomy . '" name="taxfilter[' . $field->taxonomy . ']" data-simple-locator-taxonomy-select="' . $field->taxonomy . '">';
				if ( $field->select_default && $field->select_default !== '' ) $out .= '<option value="">' . $field->select_default . '</option>';
				foreach ( $field->options as $option ) :
					$out .= '<option value="' . $option['value'] . '"';
					if ( $option['selected'] ) $out .= ' selected';
					$out .= '>' . $option['label'] . '</option>';
				endforeach;
				$out .= '</select></div><!-- .taxonomy -->';
			endif; // select

			if ( $field->type == 'checkbox' ) :
				$out .= '<div class="wpsl-taxonomy-filter checkboxes">';
				$out .= '<label class="taxonomy-label">' . $field->label . '</label>';
				$out .= '<ul class="simple-locator-taxonomy-checkboxes">';
				foreach ( $field->options as $key => $option ) :
					$out .= '<li class="simple-locator-checkbox"><label for="wpsl_taxonomy_' . $field->taxonomy . '_' . $key . '"><input type="checkbox" id="wpsl_taxonomy_' . $field->taxonomy . '" name="taxfilter[' . $field->taxonomy . '][]" value="' . $option['value'] . '" data-simple-locator-taxonomy-checkbox="' . $field->taxonomy . '"';
					if ( $option['selected'] ) $out .= ' checked';
					$out .= ' />' .$option['label'] . '</label></li>';
				endforeach;
				$out .= '</ul>';
				$out .= '</div><!-- .wpsl-taxonomy-filter -->';
			endif; // checkbox

		endforeach;

		$out .= '</div><!-- .wpsl-taxonomy-filters -->';
		echo $out;
	}

	/**
	* Setup the taxonomy field object
	* Enables filtering on the field output
	*/
	private function taxonomyField($tax_name, $taxonomy, $options)
	{
		$tax_obj = get_taxonomy($tax_name);
		$tax_field = new \stdClass;
		$tax_field->label = $taxonomy['label'];
		$tax_field->taxonomy = $tax_name;
		$tax_field->select_default = sprintf(esc_html__('--Select %s--', 'simple-locator'), $tax_obj->labels->singular_name);
		$tax_field->type = ( $options['taxonomy_field_type'] == 'select' ) ? 'select' : 'checkbox';
		$tax_field->options = [];
		$c = 1;
		foreach ( $taxonomy['terms'] as $term ){
			$tax_field->options[$c]['value'] = $term->term_id;
			$tax_field->options[$c]['label'] = $term->name;
			$tax_field->options[$c]['selected'] = false;
			$c++;
		}
		return apply_filters("simple_locator_taxonomy_field_{$tax_name}", $tax_field);
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