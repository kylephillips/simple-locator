<?php
namespace SimpleLocator\WPData\Fields;

/**
* Output the location form fields and apply filters
*/
class FormFields extends FieldBase
{
	/**
	* Get all fields, in order
	*/
	public function allFields()
	{
		$fields_ordered = $this->order();
		$fields = [];
		foreach ( $fields_ordered as $method )
		{
			$fields[$method] = $this->$method();
		}
		return $fields;
	}

	/**
	* Get the order of the fields
	* Allows reordering through filter. References method names for fields
	*/
	public function order()
	{
		$order = [
			'address', 
			'address_two',
			'city',
			'state',
			'zip',
			'country',
			'map',
			'latlng',
			'phone',
			'website',
			'additionalInfo'
		];
		return apply_filters('simple_locator_fields_ordering', $order);
	}

	/**
	* Address Field (Line 1)
	*/
	public function address($value = '', $post_id = null)
	{
		$field = [
			'key' => 'address',
			'label' => __('Street Address', 'simple-locator'),
			'type' => 'text',
			'name' => 'wpsl_address',
			'id' => 'wpsl_address',
			'attributes' => [
				'data-quickedit-address', 
				['attr' => 'data-value', 'value' => $value]
			],
			'choices' => [],
			'css-class' => ['full', 'wpsl-address-field']
		];
		return apply_filters('simple_locator_fields_address', $field, $post_id);
	}

	/**
	* Address Field (Line 2)
	*/
	public function address_two($value = '', $post_id = null)
	{
		$field = [
			'key' => 'address_two',
			'label' => __('Street Address Line 2', 'simple-locator'),
			'type' => 'text',
			'name' => 'wpsl_address_two',
			'id' => 'wpsl_address_two',
			'attributes' => [
				'data-quickedit-address_two', 
				['attr' => 'data-value', 'value' => $value]
			],
			'choices' => [],
			'css-class' => ['full', 'wpsl-address-two-field']
		];
		return apply_filters('simple_locator_fields_address_two', $field, $post_id);
	}

	/**
	* City Field
	*/
	public function city($value = '', $post_id = null)
	{
		$field = [
			'key' => 'city',
			'label' => __('City', 'simple-locator'),
			'type' => 'text',
			'name' => 'wpsl_city',
			'id' => 'wpsl_city',
			'attributes' => [
				'data-quickedit-city', 
				['attr' => 'data-value', 'value' => $value]
			],
			'choices' => [],
			'css-class' => ['city', 'wpsl-city-field']
		];
		return apply_filters('simple_locator_fields_city', $field, $post_id);
	}

	/**
	* State Field
	*/
	public function state($value = '', $post_id = null)
	{
		$field = [
			'key' => 'state',
			'label' => __('State', 'simple-locator'),
			'type' => 'text',
			'name' => 'wpsl_state',
			'id' => 'wpsl_state',
			'attributes' => [
				'data-quickedit-state', 
				['attr' => 'data-value', 'value' => $value]
			],
			'choices' => [],
			'css-class' => ['state', 'wpsl-state-field']
		];
		return apply_filters('simple_locator_fields_state', $field, $post_id);
	}

	/**
	* Postal/Zip Code Field
	*/
	public function zip($value = '', $post_id = null)
	{
		$field = [
			'key' => 'zip',
			'label' => __('Zip/Postal Code', 'simple-locator'),
			'type' => 'text',
			'name' => 'wpsl_zip',
			'id' => 'wpsl_zip',
			'attributes' => [
				'data-quickedit-zip', 
				['attr' => 'data-value', 'value' => $value]
			],
			'choices' => [],
			'css-class' => ['zip', 'wpsl-zip-field']
		];
		return apply_filters('simple_locator_fields_postal_code', $field, $post_id);
	}

	/**
	* Country Field
	*/
	public function country($value = '', $post_id = null)
	{
		$field = [
			'key' => 'country',
			'label' => __('Country', 'simple-locator'),
			'type' => 'text',
			'name' => 'wpsl_country',
			'id' => 'wpsl_country',
			'attributes' => [
				'data-quickedit-country', 
				['attr' => 'data-value', 'value' => $value]
			],
			'choices' => [],
			'css-class' => ['full', 'wpsl-country-field']
		];
		return apply_filters('simple_locator_fields_country', $field, $post_id);
	}

	/**
	* Website Field
	*/
	public function website($value = '', $post_id = null)
	{
		$field = [
			'key' => 'website',
			'label' => __('Website', 'simple-locator'),
			'type' => 'text',
			'name' => 'wpsl_website',
			'id' => 'wpsl_website',
			'attributes' => [
				'data-quickedit-website', 
				['attr' => 'data-value', 'value' => $value]
			],
			'choices' => [],
			'css-class' => ['half', 'right', 'wpsl-website-field']
		];
		return apply_filters('simple_locator_fields_website', $field, $post_id);
	}

	/**
	* Phone # Field
	*/
	public function phone($value = '', $post_id = null)
	{
		$field = [
			'key' => 'phone',
			'label' => __('Phone Number', 'simple-locator'),
			'type' => 'text',
			'name' => 'wpsl_phone',
			'id' => 'wpsl_phone',
			'attributes' => [
				'data-quickedit-phone', 
				['attr' => 'data-value', 'value' => $value]
			],
			'choices' => [],
			'css-class' => ['half', 'wpsl-phone-field']
		];
		return apply_filters('simple_locator_fields_phone', $field, $post_id);
	}

	/**
	* Additional Info
	*/
	public function additionalInfo($value = '', $post_id = null)
	{
		$field = [
			'key' => 'additionalInfo',
			'label' => __('Additional Info', 'simple-locator'),
			'type' => 'textarea',
			'name' => 'wpsl_additionalinfo',
			'id' => 'wpsl_additionalinfo',
			'attributes' => [
				'data-quickedit-additionalinfo', 
				['attr' => 'data-value', 'value' => $value]
			],
			'choices' => [],
			'css-class' => ['full', 'wpsl-additional-field']
		];
		return apply_filters('simple_locator_fields_additional_info', $field, $post_id);
	}

	/**
	* Map
	*/
	public function map()
	{

	}

	/**
	* Lat/Lng
	*/
	public function latlng()
	{

	}
}