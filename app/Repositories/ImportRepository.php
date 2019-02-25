<?php 
namespace SimpleLocator\Repositories;

class ImportRepository
{
	/**
	* Import Transient
	* @var array
	*/
	private $transient;

	public function __construct()
	{
		$this->transient = get_transient('wpsl_import_file');
	}

	/**
	* Is there an incomplete import?
	* @return int
	*/
	public function incomplete()
	{
		if ( isset($this->transient['complete']) && !$this->transient['complete'] ){
			return $this->transient['row_count'] - $this->transient['complete_rows'] - count($this->transient['error_rows']);
		}
		return false;
	}

	/**
	* Get the import transient
	*/
	public function transient()
	{
		return $this->transient;
	}

	/**
	* Get imported post IDs from an import
	* @param int $id - The Import ID
	*/
	public function getImportedPostIDs($id)
	{
		$meta = get_post_meta($id, 'wpsl_import_data', true);
		return $meta['post_ids'];
	}

	/**
	* Get Import Data for a specific import
	* @param int $id - The Import ID
	*/
	public function getImportData($id)
	{
		return get_post_meta($id, 'wpsl_import_data', true);
	}

	/**
	* Get all import templates
	*/
	public function getAllTemplates()
	{
		$posts = null;
		$q = new \WP_Query([
			'post_type' => 'wpslimporttemplate',
			'posts_per_page' => -1
		]);
		if ( $q->have_posts() ) : $c = 0; while ( $q->have_posts() ) : $q->the_post();
			$posts[$c] = new \stdClass;
			$posts[$c]->ID = get_the_id();
			$posts[$c]->title = get_the_title();
			$import_data = $this->getImportData(get_the_id());
			$posts[$c]->import_post_type = $import_data['post_type'];
			$posts[$c]->import_columns = $import_data['columns'];
			$posts[$c]->import_status = $import_data['import_status'];
			$posts[$c]->import_skip_first = $import_data['skip_first'];
			$posts[$c]->import_skip_geocode = $import_data['skip_geocode'];
			$posts[$c]->import_duplicate_handling = $import_data['duplicate_handling'];
			$posts[$c]->import_taxonomy_separator = $import_data['taxonomy_separator'];
		$c++; endwhile; endif;
		wp_reset_postdata();
		return $posts;
	}
}