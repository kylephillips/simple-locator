<?php 

namespace SimpleLocator\Listeners;

use SimpleLocator\Services\LocationSearch\LocationSearch as Search;

/**
* Non Ajax Location Search
*/
class LocationSearch 
{
	/**
	* Location Search Object
	* @var SimpleLocator\Services\LocationSearch\LocationSearch
	*/
	private $location_search;

	/**
	* View Data
	* @var array
	*/
	private $data;

	public function __construct()
	{
		$this->location_search = new Search;
		// TODO: Validation
		$this->search();
		$this->setViewData();
	}

	/**
	* Set Additional Response Data
	*/
	private function setViewData()
	{
		$this->data = array(
			'address' => sanitize_text_field($_POST['address']),
			'formatted_address' => sanitize_text_field($_POST['formatted_address']),
			'distance' => sanitize_text_field($_POST['distance']),
			'latitude' => sanitize_text_field($_POST['latitude']),
			'longitude' => sanitize_text_field($_POST['longitude']),
			'unit' => sanitize_text_field($_POST['unit']),
			'geolocation' => sanitize_text_field($_POST['geolocation']),
			'limit' => sanitize_text_field(intval($_POST['limit'])),
			'max_num_pages' => ceil($this->resultCount() / sanitize_text_field(intval($_POST['limit'])) ),
			'page' => sanitize_text_field(intval($_POST['page'])) + 1
		);
	}

	/**
	* Perform the Search
	*/
	private function search()
	{
		$this->location_search->search();
	}

	/**
	* Get the Query Results
	* @return array
	*/
	public function results()
	{
		return $this->location_search->getResults();
	}

	/**
	* Get the Total Number of Results
	* @return array
	*/
	public function resultCount()
	{
		return $this->location_search->getTotalResultCount();
	}

	/**
	* Get the Additional Data
	* @param string $key - data key to retrieve
	* @return string
	*/
	public function data($key)
	{
		return $this->data[$key];
	}

	/**
	* Pagination
	*/
	public function pagination()
	{
		$out = '<div class="wpsl-pagination">';
		if ( $this->data['page'] !== 1) $out .= $this->previousButton();
		if ( $this->data['page'] < $this->data['max_num_pages'] ) $out .= $this->nextButton();
		$out .= '</div>';
		return $out;
	}

	/**
	* Pagination Fields
	*/
	public function paginationFields()
	{
		$out = '
			<input type="hidden" name="nonce" value="' . $_POST['nonce'] . '">
			<input type="hidden" name="address" value="' . $this->data['address'] . '">
			<input type="hidden" name="geolocation" value="' . $this->data['geolocation'] . '">
			<input type="hidden" name="limit" value="' . $this->data['limit'] . '" />
			<input type="hidden" name="latitude" value="' . $this->data['latitude'] . '" />
			<input type="hidden" name="longitude" value="' . $this->data['longitude'] . '" />
			<input type="hidden" name="distance" value="' . $this->data['distance'] . '" />
			<input type="hidden" name="unit" value="' . $this->data['unit'] . '" />
			<input type="hidden" name="formatted_address" value="' . $this->data['formatted_address'] . '" />
		';
		return $out;
	}

	/**
	* Next Button
	*/
	public function nextButton()
	{	
		$out = '<form action="" method="post">';
		$out .= $this->paginationFields();
		$out .= '
			<input type="hidden" name="page" value="' . $this->data['page'] . '" />
			<input type="submit" value="Next" class="button" />
			</form>
		';
		return $out;
	}

	/**
	* Previous Button
	*/
	public function previousButton()
	{	
		$out = '<form action="" method="post">';
		$out .= $this->paginationFields();
		$page = $this->data['page'] - 2;
		$out .= '
			<input type="hidden" name="page" value="' . $page . '" />
			<input type="submit" value="Previous" class="button" />
			</form>
		';
		return $out;
	}

}