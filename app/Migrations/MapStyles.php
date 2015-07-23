<?php 

namespace SimpleLocator\Migrations;

/**
* Add the Google Maps Style Choices
*/
class MapStyles 
{

	/**
	* The directory where the styles are
	* @var string
	*/
	private $directory;

	/**
	* Map ID
	* @var int
	*/
	private $file_id;


	public function __construct()
	{
		$this->setDirectory();
		$this->loopFiles();
	}

	/**
	* Set the directory
	*/
	private function setDirectory()
	{
		$this->directory = dirname(__FILE__) . '/map_styles';
	}

	/**
	* Set the File ID
	* @param object - DirectoryInterator Object
	*/
	private function setFileID($file)
	{
		$filename = $file->getFilename();
		$file_id = explode('-', $filename);
		$this->file_id = $file_id[0];
	}

	/**
	* Loop through the maps and create them
	* @todo check if post exists
	*/
	private function loopFiles()
	{
		$files = new \DirectoryIterator($this->directory);
		foreach ($files as $file){
			if ( !$file->isDot() ){
				$this->setFileID($file);
				$this->importPost($file);
			}
		}
	}

	/**
	* Import the Map
	* @param object - file to import
	*/
	private function importPost($file)
	{
		if ( !$this->mapExists() ) include($this->directory . '/' . $file);
	}

	/**
	* Check if Map Post Exists before import
	*/
	private function mapExists()
	{
		$map_query = new \WP_Query(array(
			'post_type' => 'wpslmaps',
			'posts_per_page' => -1,
			'meta_key' => 'wpsl_map_id',
			'meta_value' => $this->file_id
		));
		$exists = ( $map_query->have_posts() ) ? true : false;
		wp_reset_postdata();
		return $exists;
	}

}