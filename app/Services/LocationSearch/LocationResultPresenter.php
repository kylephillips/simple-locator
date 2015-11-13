<?php 

namespace SimpleLocator\Services\LocationSearch;

use SimpleLocator\Repositories\SettingsRepository;
use SimpleLocator\Helpers;

/**
* Formats a result to match defined format
*/
class LocationResultPresenter 
{

	/**
	* Result
	* @var object - WP SQL result
	*/
	private $result;

	/**
	* Count of this result
	* @var int
	*/
	private $count;

	/**
	* Results Fields from Settings
	* @var array
	*/
	private $results_fields;

	/**
	* Settings Repository
	*/
	private $settings_repo;

	/**
	* Formatted Output from Settings
	*/
	private $output;

	/**
	* Unit of Measurement
	*/
	private $distance_unit;


	public function __construct()
	{
		$this->settings_repo = new SettingsRepository;
		$this->output = $this->settings_repo->resultsFormatting();
		$this->results_fields = $this->settings_repo->getResultsFieldArray();
		$this->distance_unit = $this->settings_repo->measurementUnit();
	}

	/**
	* Primary Presenter Method
	* @return array
	*/
	public function present($result, $count)
	{
		$this->result = $result;
		$this->count = $count;
		return $this->setData();
	}

	/**
	* Set the primary result data
	* @return array
	*/
	private function setData()
	{
		$location = array(
			'id' => $this->result->id,
			'title' => $this->result->title,
			'permalink' => get_permalink($this->result->id),
			'latitude' => $this->result->latitude,
			'longitude' => $this->result->longitude,
			'output' => $this->formatOutput(),
			'infowindow' => $this->formatInfoWindow()
		);
		return $location;
	}

	/**
	* Set the formatted output
	*/
	private function formatOutput()
	{
		$output = $this->output;
		$output = $this->replacePostFields($output);
		foreach($this->results_fields as $field){
			$found = $this->result->$field; // WP result object property
			$output = str_replace('[' . $field . ']', $found, $output);
		}

		$output = $this->removeEmptyTags($output);
		$output = Helpers::replaceURLs($output);
		$output = wpautop($output);

		$output = apply_filters('simple_locator_result', $output, $this->result, $this->count);

		return $output;
	}

	/**
	* Render the info window output
	*/
	private function formatInfoWindow()
	{
		$infowindow = '<div data-result="' . $this->count . '"><h4>[post_title]</h4><p><a href="[post_permalink]" data-location-id="'.$this->result->id.'">'.__('View Location', 'wpsimplelocator').'</a></p></div>';
		$infowindow = $this->replacePostFields($infowindow);

		$infowindow = apply_filters('simple_locator_infowindow', $infowindow, $this->result, $this->count);

		return $infowindow;
	}

	/**
	* Replace post fields from settings
	*/
	private function replacePostFields($output)
	{
		if ( isset($this->result->distance) ) $output = str_replace('[distance]', round($this->result->distance, 2) . ' ' . $this->distance_unit, $output);
		$output = str_replace('[post_title]', $this->result->title, $output);

		if ( strpos($output, '[post_permalink]') !== false ){
			$output = str_replace('[post_permalink]', get_permalink($this->result->id), $output);
		}
		if ( strpos($output, '[post_excerpt]') !== false ){
			$output = str_replace('[post_excerpt]', Helpers::excerptByID($this->result->id), $output);
		}
		if ( strpos($output, '[post_thumbnail_') !== false ){
			$output = $this->addThumbnail($output);
		}

		// Show on Map Link
		$maplink = '<a href="#" class="infowindow-open map-link" onClick="event.preventDefault(); openInfoWindow(' . $this->count . ');">' . __('Show on Map', 'wpsimplelocator') . '</a>';
		$output = str_replace('[show_on_map]', $maplink, $output);

		return $output;
	}

	/**
	* Remove empty tags
	*/
	private function removeEmptyTags($output)
	{
		$output = preg_replace("/<p[^>]*><\\/p[^>]*>/", '', $output); // empty p tags
		$output = str_replace('<a href="http://">http://</a>', '', $output); // remove empty links
		$output = str_replace('<a href=""></a>', '', $output);
		$output = str_replace("\r\n\r\n", "\n", $output);
		return $output;
	}

	/**
	* Add the post thumbnail
	*/
	private function addThumbnail($output)
	{
		$sizes = get_intermediate_image_sizes();
		foreach ( $sizes as $size ){
			if ( strpos($output, '[post_thumbnail_' . $size) !== false ){
				$output = str_replace('[post_thumbnail_' . $size . ']', $this->getThumbnail($size), $output);
			}
		}
		return $output;		
	}

	/**
	* Get thumbnail
	*/
	private function getThumbnail($size)
	{
		return ( has_post_thumbnail($this->result->id) )
			? get_the_post_thumbnail($this->result->id, $size)
			: ' ';
	}

}