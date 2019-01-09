<?php
namespace SimpleLocator\WPData\Fields;

/**
* Output the location form fields and apply filters
*/
class FormFields
{
	/**
	* Address Field (Line 1)
	*/
	public function address($value = '', $post_id = null)
	{
		$field = [
			'label' => __('Street Address', 'simple-locator'),
			'type' => 'text',
			'name' => 'wpsl_address',
			'id' => 'wpsl_address',
			'attributes' => ['data-quickedit-address'],
			'choices' => []
		];
		return $this->output(apply_filters('simple_locator_fields_address', $field), $value, $post_id);
	}

	/**
	* Address Field (Line 2)
	*/
	public function address_two($value = '', $post_id = null)
	{
		$field = [
			'label' => __('Street Address Line 2', 'simple-locator'),
			'type' => 'text',
			'name' => 'wpsl_address_two',
			'id' => 'wpsl_address_two',
			'attributes' => ['data-quickedit-address_two'],
			'choices' => []
		];
		return $this->output(apply_filters('simple_locator_fields_address_two', $field), $value, $post_id);
	}

	/**
	* City Field
	*/
	public function city($value = '', $post_id = null)
	{
		$field = [
			'label' => __('City', 'simple-locator'),
			'type' => 'text',
			'name' => 'wpsl_city',
			'id' => 'wpsl_city',
			'attributes' => ['data-quickedit-city'],
			'choices' => []
		];
		return $this->output(apply_filters('simple_locator_fields_city', $field), $value, $post_id);
	}

	/**
	* State Field
	*/
	public function state($value = '', $post_id = null)
	{
		$field = [
			'label' => __('State', 'simple-locator'),
			'type' => 'text',
			'name' => 'wpsl_state',
			'id' => 'wpsl_state',
			'attributes' => ['data-quickedit-state'],
			'choices' => []
		];
		return $this->output(apply_filters('simple_locator_fields_state', $field), $value, $post_id);
	}

	/**
	* Postal/Zip Code Field
	*/
	public function postalCode($value = '', $post_id = null)
	{
		$field = [
			'label' => __('Zip/Postal Code', 'simple-locator'),
			'type' => 'text',
			'name' => 'wpsl_zip',
			'id' => 'wpsl_zip',
			'attributes' => ['data-quickedit-zip'],
			'choices' => []
		];
		return $this->output(apply_filters('simple_locator_fields_postal_code', $field), $value, $post_id);
	}

	/**
	* Country Field
	*/
	public function country($value = '', $post_id = null)
	{
		$field = [
			'label' => __('Country', 'simple-locator'),
			'type' => 'text',
			'name' => 'wpsl_country',
			'id' => 'wpsl_country',
			'attributes' => ['data-quickedit-country'],
			'choices' => []
		];
		return $this->output(apply_filters('simple_locator_fields_country', $field), $value, $post_id);
	}

	/**
	* Website Field
	*/
	public function website($value = '', $post_id = null)
	{
		$field = [
			'label' => __('Website', 'simple-locator'),
			'type' => 'text',
			'name' => 'wpsl_website',
			'id' => 'wpsl_website',
			'attributes' => ['data-quickedit-website'],
			'choices' => []
		];
		return $this->output(apply_filters('simple_locator_fields_website', $field), $value, $post_id);
	}

	/**
	* Phone # Field
	*/
	public function phone($value = '', $post_id = null)
	{
		$field = [
			'label' => __('Phone Number', 'simple-locator'),
			'type' => 'text',
			'name' => 'wpsl_phone',
			'id' => 'wpsl_phone',
			'attributes' => ['data-quickedit-phone'],
			'choices' => []
		];
		return $this->output(apply_filters('simple_locator_fields_phone', $field), $value, $post_id);
	}

	/**
	* Additional Info
	*/
	public function additionalInfo($value = '', $post_id = null)
	{
		$field = [
			'label' => __('Additional Info', 'simple-locator'),
			'type' => 'textarea',
			'name' => 'wpsl_additionalinfo',
			'id' => 'wpsl_additionalinfo',
			'attributes' => ['data-quickedit-additionalinfo'],
			'choices' => []
		];
		return $this->output(apply_filters('simple_locator_fields_phone', $field), $value, $post_id);
	}

	/**
	* Output the Field
	*/
	private function output($field, $value)
	{
		$out = '';
		if ( $field['type'] == 'text' ){
			$out .= '<label for="' . $field['id'] . '">' . $field['label'] . '</label>';
			$out .= '<input type="text" id="' . $field['id'] . '" name="' . $field['name'] . '" value="' . $value . '"';
			$out .= $this->attributes($field);
			$out .= ' />';
		}
		if ( $field['type'] == 'select' ){
			$out .= '<label for="' . $field['id'] . '">' . $field['label'] . '</label>';
			$out .= '<select id="' . $field['id'] . '" name="' . $field['name'] . '"';
			$out .= $this->attributes($field) . '>';
			foreach ( $field['choices'] as $choice => $label ){
				$out .= '<option value="' . $choice . '"';
				if ( $choice == $value ) $out .= ' selected';
				$out .= '>' . $label . '</option>';
			}
			$out .= '</select>';
		}
		if ( $field['type'] == 'textarea' ){
			$out .= '<label for="' . $field['id'] . '">' . $field['label'] . '</label>';
			$out .= '<textarea id="' . $field['id'] . '" name="' . $field['name'] . '"';
			$out .= $this->attributes($field);
			$out .= '>' . $value . '</textarea>';
		}
		return $out;
	}

	/**
	* Add Attributes to the Output
	*/
	private function attributes($field)
	{
		$out = '';
		foreach ( $field['attributes'] as $attr ){
			if ( is_array($attr) ) $out .= ' ' . $attr[0] . '="' . $attr[1] . '"';
			if ( !is_array($attr) ) $out .= ' ' . $attr;
		}
		return $out;
	}
}