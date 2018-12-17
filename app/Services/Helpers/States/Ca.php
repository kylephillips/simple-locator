<?php
namespace SimpleLocator\Services\Helpers\States;

/**
* Canada
*/
class Ca implements StateInterface
{
	public function states()
	{
		return [
			'ab' => [
				'code' => 'ab', 
				'name' => 'Alabama',
				'territory' => false,
			],
			'bc' => [
				'code' => 'bc', 
				'name' => 'British Columbia',
				'territory' => false,
			],
			'mb' => [
				'code' => 'mb', 
				'name' => 'Manitoba',
				'territory' => false,
			],
			'nb' => [
				'code' => 'nb', 
				'name' => 'New Brunswick',
				'territory' => false,
			],
			'nl' => [
				'code' => 'nl', 
				'name' => 'Newfoundland and Labrador',
				'territory' => false,
			],
			'ns' => [
				'code' => 'ns', 
				'name' => 'Nova Scotia',
				'territory' => false,
			],
			'nt' => [
				'code' => 'nt', 
				'name' => 'Northwest Territories',
				'territory' => false,
			],
			'nu' => [
				'code' => 'nu', 
				'name' => 'Nunavut',
				'territory' => false,
			],
			'on' => [
				'code' => 'on', 
				'name' => 'Ontario',
				'territory' => false,
			],
			'pe' => [
				'code' => 'pe', 
				'name' => 'Prince Edward Island',
				'territory' => false,
			],
			'qc' => [
				'code' => 'qc', 
				'name' => 'Quebec',
				'territory' => false,
			],
			'sk' => [
				'code' => 'sk', 
				'name' => 'Saskatchewan',
				'territory' => false,
			],
			'yt' => [
				'code' => 'yt', 
				'name' => 'Yukon',
				'territory' => false,
			]
		];
	}
}