<?php
namespace SimpleLocator\Services\CSVDownload;

use SimpleLocator\Repositories\SettingsRepository;
use League\Csv\Writer;

/**
* Generate a CSV Export of Locations and trigger a download
*/
class ExportCsvDownload
{
	/**
	* Settings Repository
	*/
	private $settings;

	/**
	* The Posts to Export
	*/
	private $posts = [];

	/**
	* The Formatted Array of Posts
	*/
	private $formatted_posts = [];

	public function __construct()
	{
		$this->settings = new SettingsRepository;
		$this->getPosts();
		$this->buildCsvArray();
		$this->generateCsv();
	}

	/**
	* Get the posts and add meta values
	* @todo add support for meta values that are arrays
	*/
	private function getPosts()
	{
		$q = new \WP_Query([
			'post_type' => $this->settings->getLocationPostType(),
			'post_status' => ['publish', 'draft', 'pending', 'future'],
			'posts_per_page' => -1
		]);
		if ( $q->have_posts() ) : $c = 0; while ( $q->have_posts() ) : $q->the_post();
			$this->posts[$c] = $q->posts[$c];
			$meta = get_post_meta(get_the_id());
			foreach ( $meta as $key => $value ){
				if ( count($value) == 1 ){
					$this->posts[$c]->$key  = $value[0];
				}
			}
		$c++; endwhile; endif; wp_reset_postdata();
	}

	/**
	* Build the array to create the CSV from
	*/
	private function buildCsvArray()
	{
		$standard_columns = $_POST['standard_columns'];
		$custom_columns = $_POST['custom_columns'];

		foreach ( $this->posts as $k => $post ) :
			$this->formatted_posts[$k] = [];
			foreach ( $standard_columns as $column ) :
				$this->formatted_posts[$k][$column] = ( isset($post->$column) ) ? $post->$column : null;
			endforeach;
			foreach ( $custom_columns as $column ) :
				$this->formatted_posts[$k][$column] = ( isset($post->$column) ) ? $post->$column : null;
			endforeach;
		endforeach;
	}

	/**
	* Generate the CSV
	*/
	private function generateCsv()
	{
		$csv = Writer::createFromFileObject(new \SplTempFileObject());

		// Header Row
		$include_header = ( isset($_POST['include_header_row']) && $_POST['include_header_row'] == 'true' ) ? true : false;
		if ( $include_header ) :
			$standard_columns = $_POST['standard_columns'];
			$custom_columns = $_POST['custom_columns'];
			$header = [];
			foreach ( $standard_columns as $column ) :
				$header[] = ( isset($_POST['column_name'][$column]) && $_POST['column_name'][$column] !== '' ) 
					? sanitize_text_field($_POST['column_name'][$column])
					: $column;
			endforeach;
			foreach ( $custom_columns as $column ) :
				$header[] = ( isset($_POST['column_name'][$column]) && $_POST['column_name'][$column] !== '' ) 
					? sanitize_text_field($_POST['column_name'][$column])
					: $column;
			endforeach;
			$csv->insertOne($header);
		endif;

		$csv->insertAll($this->formatted_posts);
		$filename = __('location-export', 'simple-locator') . '.csv';
		$csv->output($filename);
	}
}