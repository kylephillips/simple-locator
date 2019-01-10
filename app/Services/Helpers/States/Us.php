<?php
namespace SimpleLocator\Services\Helpers\States;

/**
* United States
*/
class Us implements StateInterface
{
	public function states()
	{
		$states = [
			'al' => [
				'code' => 'al', 
				'name' => 'Alabama',
				'territory' => false,
			],
			'ak' => [
				'code' => 'ak', 
				'name' => 'Alaska',
				'territory' => false
			],
			'az' => [
				'code' => 'az', 
				'name' => 'Arizona',
				'territory' => false
			],
			'ar' => [
				'code' => 'ar', 
				'name' => 'Arkansas',
				'territory' => false
			],
			'as' => [
				'code' => 'ar', 
				'name' => 'American Samoa',
				'territory' => true
			],
			'ca' => [
				'code' => 'ca', 
				'name' => 'California',
				'territory' => false
			],
			'co' => [
				'code' => 'co', 
				'name' => 'Colorado',
				'territory' => false
			],
			'ct' => [
				'code' => 'ct', 
				'name' => 'Connecticut',
				'territory' => false
			],
			'de' => [
				'code' => 'de', 
				'name' => 'Delaware',
				'territory' => false
			],
			'fm' => [
				'code' => 'fm', 
				'name' => 'Federated States of Micronesiaa',
				'territory' => true
			],
			'fl' => [
				'code' => 'fl', 
				'name' => 'Florida',
				'territory' => false
			],
			'ga' => [
				'code' => 'ga', 
				'name' => 'Georgia',
				'territory' => false
			],
			'gu' => [
				'code' => 'gu', 
				'name' => 'Guam',
				'territory' => true
			],
			'hi' => [
				'code' => 'hi', 
				'name' => 'Hawaii',
				'territory' => false
			],
			'id' => [
				'code' => 'id', 
				'name' => 'Idaho',
				'territory' => false
			],
			'il' => [
				'code' => 'il', 
				'name' => 'Illinois',
				'territory' => false
			],
			'in' => [
				'code' => 'in', 
				'name' => 'Indiana',
				'territory' => false
			],
			'ia' => [
				'code' => 'ia', 
				'name' => 'Iowa',
				'territory' => false
			],
			'ks' => [
				'code' => 'ks', 
				'name' => 'Kansas',
				'territory' => false
			],
			'ky' => [
				'code' => 'ky', 
				'name' => 'Kentucky',
				'territory' => false
			],
			'la' => [
				'code' => 'la', 
				'name' => 'Louisiana',
				'territory' => false
			],
			'me' => [
				'code' => 'me', 
				'name' => 'Maine',
				'territory' => false
			],
			'md' => [
				'code' => 'md', 
				'name' => 'Maryland',
				'territory' => false
			],
			'mh' => [
				'code' => 'mh', 
				'name' => 'Marshall Islands',
				'territory' => true
			],
			'ma' => [
				'code' => 'ma', 
				'name' => 'Massachusetts',
				'territory' => false
			],
			'mi' => [
				'code' => 'mi', 
				'name' => 'Michigan',
				'territory' => false
			],
			'mn' => [
				'code' => 'mn', 
				'name' => 'Minnesota',
				'territory' => false
			],
			'ms' => [
				'code' => 'ms', 
				'name' => 'Mississippi',
				'territory' => false
			],
			'mo' => [
				'code' => 'mo', 
				'name' => 'Missouri',
				'territory' => false
			],
			'mt' => [
				'code' => 'mt', 
				'name' => 'Montana',
				'territory' => false
			],
			'mp' => [
				'code' => 'mp', 
				'name' => 'Northern Mariana Islands',
				'territory' => true
			],
			'ne' => [
				'code' => 'ne', 
				'name' => 'Nebraska',
				'territory' => false
			],
			'nv' => [
				'code' => 'nv', 
				'name' => 'Nevada',
				'territory' => false
			],
			'nh' => [
				'code' => 'nh', 
				'name' => 'New Hampshire',
				'territory' => false
			],
			'nj' => [
				'code' => 'nj', 
				'name' => 'New Jersey',
				'territory' => false
			],
			'nm' => [
				'code' => 'nm', 
				'name' => 'New Mexico',
				'territory' => false
			],
			'ny' => [
				'code' => 'ny', 
				'name' => 'New York',
				'territory' => false
			],
			'nc' => [
				'code' => 'nc', 
				'name' => 'North Carolina',
				'territory' => false
			],
			'nd' => [
				'code' => 'nd', 
				'name' => 'North Dakota',
				'territory' => false
			],
			'oh' => [
				'code' => 'oh', 
				'name' => 'Ohio',
				'territory' => false
			],
			'ok' => [
				'code' => 'ok', 
				'name' => 'Oklahoma',
				'territory' => false
			],
			'or' => [
				'code' => 'or', 
				'name' => 'Oregon',
				'territory' => false
			],
			'pw' => [
				'code' => 'pw', 
				'name' => 'Palau',
				'territory' => true
			],
			'pa' => [
				'code' => 'pa', 
				'name' => 'Pennsylvania',
				'territory' => false
			],
			'pr' => [
				'code' => 'pr', 
				'name' => 'Puerto Rico',
				'territory' => true
			],
			'ri' => [
				'code' => 'ri', 
				'name' => 'Rhode Island',
				'territory' => false
			],
			'sc' => [
				'code' => 'sc', 
				'name' => 'South Carolina',
				'territory' => false
			],
			'sd' => [
				'code' => 'sd', 
				'name' => 'South Dakota',
				'territory' => false
			],
			'tn' => [
				'code' => 'tn', 
				'name' => 'Tennessee',
				'territory' => false
			],
			'tx' => [
				'code' => 'tx', 
				'name' => 'Texas',
				'territory' => false
			],
			'um' => [
				'code' => 'um', 
				'name' => 'U.S. Minor Outlying Islands ',
				'territory' => true
			],
			'ut' => [
				'code' => 'ut', 
				'name' => 'Utah',
				'territory' => false
			],
			'vt' => [
				'code' => 'vt', 
				'name' => 'Vermont',
				'territory' => false
			],
			'vi' => [
				'code' => 'vi', 
				'name' => 'U.S. Virgin Islands',
				'territory' => true
			],
			'va' => [
				'code' => 'va', 
				'name' => 'Virginia',
				'territory' => false
			],
			'wa' => [
				'code' => 'wa', 
				'name' => 'Washington',
				'territory' => false
			],
			'wv' => [
				'code' => 'wv', 
				'name' => 'West Virginia',
				'territory' => false
			],
			'wi' => [
				'code' => 'wi', 
				'name' => 'Wisconsin',
				'territory' => false
			],
			'wy' => [
				'code' => 'wy', 
				'name' => 'Wyoming',
				'territory' => false
			],
		];
		return apply_filters('simple_locator_state_listing_us', $states);
	}
}