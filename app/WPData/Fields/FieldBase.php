<?php
namespace SimpleLocator\WPData\Fields;

abstract class FieldBase
{
	/**
	* Output the Field and Label
	*/
	public function output($field, $value)
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
	protected function attributes($field)
	{
		$out = '';
		foreach ( $field['attributes'] as $attr ){
			if ( is_array($attr) ) $out .= ' ' . $attr[0] . '="' . $attr[1] . '"';
			if ( !is_array($attr) ) $out .= ' ' . $attr;
		}
		return $out;
	}
}