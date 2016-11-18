<?php
$total_count = $this->search_repo->getTotalCount();
$all_searches = $this->search_repo->getAllSearches();
$date_format = get_option('date_format');
$is_search = ( isset($_GET['q']) ) ? true : false;
$page = admin_url('options-general.php?page=wp_simple_locator&tab=search-history');
if ( $all_searches ) :
?>

<h2>
	<?php 
		echo ( $is_search ) ? __('Search Results Count', 'wpsimplelocator') : __('Total Search Count', 'wpsimplelocator');
		echo ': ' . $total_count;
	?>
</h2>
<?php if ( $is_search ) : ?>
	<p><a href="<?php echo $page; ?>"><?php _e('View All', 'wpsimplelocator'); ?></a></p>
<?php endif; ?>

<div class="wpsl-search-history-form">
	<form action="<?php echo admin_url('admin-post.php'); ?>" method="post">
		<input type="hidden" name="action" value="wpslhistorysearch">
		<input type="hidden" name="page" value="<?php echo $page; ?>">
		<?php wp_nonce_field('wpsl-nonce', 'nonce'); ?>
		<input type="text" name="search_term" placeholder="<?php _e('Search Terms', 'wpsimplelocator'); ?>" />
		<input type="submit" name="" class="button" value="Search">
	</form>
</div>

<table class="wpsl-search-history-table">
	<thead>
		<tr>
			<th><?php _e('Date', 'wpsimplelocator'); ?></th>
			<th><?php _e('User IP', 'wpsimplelocator'); ?></th>
			<th><?php _e('Search Term', 'wpsimplelocator'); ?></th>
			<th><?php _e('Search Term - Formatted', 'wpsimplelocator'); ?></th>
			<th><?php _e('Distance', 'wpsimplelocator'); ?></th>
			<th><?php _e('View on Google Maps', 'wpsimplelocator'); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php 
			foreach ( $all_searches as $search ) : 
			$date = date_i18n( $date_format, strtotime( $search->time ) );
			$link = 'http://maps.google.com/maps?q=loc:' . $search->search_lat . ',' . $search->search_lng;
			?>
			<tr>
				<td><?php echo $date; ?></td>
				<td><?php echo $search->user_ip; ?></td>
				<td><?php echo $search->search_term; ?></td>
				<td><?php echo $search->search_term_formatted; ?></td>
				<td><?php echo $search->distance; ?></td>
				<td>
					<a href="<?php echo $link; ?>" class="google-maps-link" target="_blank">
						<?php _e('View', 'wpsimplelocator'); ?>
					</a>
				</td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>

<?php else : // No searches yet ?>
<h2>
	<?php 
	echo ( $is_search ) 
		? __('0 Results for ', 'wpsimplelocator') . sanitize_text_field($_GET['q'])
		: __('There are currently no logged searches.', 'wpsimplelocator');
	?>
</h2>
<?php if ( $is_search ) : ?>
	<p><a href="<?php echo $page; ?>"><?php _e('View All', 'wpsimplelocator'); ?></a></p>
<?php endif; ?>
<?php endif; ?>