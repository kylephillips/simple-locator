<?php
namespace SimpleLocator\Migrations;

/**
* Create the necessary DB Tables
*/
class CreateTables
{
	public function __construct()
	{
		$this->searchTable();
	}

	/**
	* Create the search records table if it doesn't exist
	*/
	private function searchTable()
	{
		$table_installed = get_option('wpsl_search_table_installed');
		if ( $table_installed ) return;

		global $wpdb;
		$tablename = $wpdb->prefix . 'simple_locator_history';
		if ( $wpdb->get_var('SHOW TABLES LIKE "' . $tablename . '"') != $tablename ) :
			$sql = 'CREATE TABLE ' . $tablename . '(
				id INTEGER(10) UNSIGNED AUTO_INCREMENT,
				time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
				user_ip VARCHAR(20),
				search_lat VARCHAR(20),
				search_lng VARCHAR(20),
				search_term VARCHAR(100),
				search_term_formatted VARCHAR(100),
				distance VARCHAR(20),
				PRIMARY KEY  (id) )';
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
			update_option('wpsl_search_table_installed', true);
		endif;
	}

}