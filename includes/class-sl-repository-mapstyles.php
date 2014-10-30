<?php
/**
* Get the default map style included with plugin
*/
class WPSL_Repository_MapStyles {

	/**
	* Output the necessary JS object
	*/
	public function getAllStyles()
	{
		$styles = array();
		$map_query = new WP_Query(array(
			'post_type' => 'wpslmaps',
			'posts_per_page' => -1
		));
		if ( $map_query->have_posts() ) : $i = 0; while ( $map_query->have_posts() ) : $map_query->the_post();
			$styles[$i]['title'] = get_the_title();
			$styles[$i]['styles'] = get_the_content();
			$styles[$i]['id'] = get_the_id();
			$styles[$i]['selected'] = ( get_option('wpsl_map_styles_choice') == get_the_id() ) ? true : false;
			$i++;
		endwhile; endif; wp_reset_postdata();
		return $styles;
	}

}