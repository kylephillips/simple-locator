<?php 

namespace SimpleLocator\Repositories;

/**
* Get the default map style included with plugin
*/
class MapStyles 
{

	/**
	* Output the necessary JS object
	*/
	public function getAllStyles()
	{
		$styles = array();
		$map_query = new \WP_Query(array(
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

	/**
	* Get Styles for JS Localization
	*/
	public function getLocalizedStyles()
	{
		$style_type = get_option('wpsl_map_styles_type');
		if ( $style_type == 'none' ) return '';
		if ( $style_type == 'choice' ) return $this->getSelectedStyles();
		return ( get_option('wpsl_map_styles') ) ? json_decode(get_option('wpsl_map_styles')) : '';
	}

	/**
	* Get the selected style
	*/
	private function getSelectedStyles()
	{
		$style_query = new \WP_Query(array(
			'post_type' => 'wpslmaps',
			'p' => get_option('wpsl_map_styles_choice'),
			'posts_per_page' => 1
		));
		if ( $style_query->have_posts() ) : while ( $style_query->have_posts() ) : $style_query->the_post();
			$style_content = json_decode(get_the_content());
		endwhile; endif;
		wp_reset_postdata();
		return $style_content;
	}

}