<?php
namespace SimpleLocator\Services\CSVDownload;

use SimpleLocator\Repositories\SettingsRepository;
use League\Csv\Writer;
use SimpleLocator\Services\ExportTemplates\TemplateCreator;

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
	* Template Creator
	*/
	private $template_creator;

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
		$this->template_creator = new TemplateCreator;
		$this->getPosts();
		$this->buildCsvArray();
		$this->createTemplate();
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
		$standard_columns = ( isset($_POST['standard_columns']) ) ? $_POST['standard_columns'] : [];
		$custom_columns = ( isset($_POST['custom_columns']) ) ? $_POST['custom_columns'] : [];
		$taxonomies = ( isset($_POST['taxonomies']) ) ? $_POST['taxonomies'] : [];

		$taxonomy_separator = ( isset($_POST['taxonomy_separator']) ) ? $_POST['taxonomy_separator'] : 'comma';
		$separator = ( $taxonomy_separator == 'comma' ) ? ',' : '|';

		foreach ( $this->posts as $k => $post ) :
			$this->formatted_posts[$k] = [];
			foreach ( $standard_columns as $column ) :
				$column = sanitize_text_field($column);
				$this->formatted_posts[$k][$column] = ( isset($post->$column) ) ? $post->$column : null;
			endforeach;
			foreach ( $custom_columns as $column ) :
				$column = sanitize_text_field($column);
				$this->formatted_posts[$k][$column] = ( isset($post->$column) ) ? $post->$column : null;
			endforeach;
			foreach ( $taxonomies as $tax ) :
				$terms = wp_get_post_terms($post->ID, $tax);
				$term_value = '';
				if ( $terms ) :
					$c = 0;
					foreach ( $terms as $term ){
						$term_value .= $term->slug;
						$c++;
						if ( $c < count($terms) ) $term_value .= $separator;
					}
				endif;
				$this->formatted_posts[$k][$tax] = $term_value;
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
			$standard_columns = ( isset($_POST['standard_columns']) ) ? $_POST['standard_columns'] : [];
			$custom_columns = ( isset($_POST['custom_columns']) ) ? $_POST['custom_columns'] : [];
			$taxonomies = ( isset($_POST['taxonomies']) ) ? $_POST['taxonomies'] : [];
			$header = [];
			foreach ( $standard_columns as $column ) :
				$column = sanitize_text_field($column);
				$header[] = ( isset($_POST['column_name'][$column]) && $_POST['column_name'][$column] !== '' ) 
					? sanitize_text_field($_POST['column_name'][$column])
					: $column;
			endforeach;
			foreach ( $custom_columns as $column ) :
				$column = sanitize_text_field($column);
				$header[] = ( isset($_POST['column_name'][$column]) && $_POST['column_name'][$column] !== '' ) 
					? sanitize_text_field($_POST['column_name'][$column])
					: $column;
			endforeach;
			foreach ( $taxonomies as $tax ) :
				$header[] = ( isset($POST['column_name'][$tax]) && $_POST['column_name'][$tax] !== '' ) 
					? sanitize_text_field($_POST['column_name'][$tax])
					: $tax;
			endforeach;
			$csv->insertOne($header);
		endif;

		$csv->insertAll($this->formatted_posts);
		$filename = __('location-export', 'simple-locator') . '.csv';
		if ( isset($_POST['file_name']) && $_POST['file_name'] !== '' ) 
			$filename = sanitize_text_field($_POST['file_name']) . '.csv';
		$csv->output($filename);
	}

	/**
	* Create the template
	*/
	private function createTemplate()
	{
		if ( !isset($_POST['save_template']) || $_POST['save_template'] !== 'true' ) return;
		$this->template_creator->create($_POST);
	}
}