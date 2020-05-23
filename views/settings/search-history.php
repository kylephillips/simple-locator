<?php
$total_count = $this->search_repo->setSearch('GET', false);
$total_count = $this->search_repo->getTotalCount();

$search_rows = $this->search_repo->setSearch('GET', true);
$search_rows = $this->search_repo->getResults();

$date_format = get_option('date_format');
$is_search = ( isset($_GET['q']) ) ? true : false;

if ( isset($_GET['date_start']) && $_GET['date_start'] !== '' ) 
	$date_start = date('F j, Y', strtotime('@' . sanitize_text_field($_GET['date_start'])));
if ( isset($_GET['date_end']) && $_GET['date_end'] !== '' ) 
	$date_end = date('F j, Y', strtotime('@' . sanitize_text_field($_GET['date_end'])));
if ( isset($_GET['q']) && $_GET['q'] !== '' ) $search_term = sanitize_text_field($_GET['q']);

$per_page = ( isset($_GET['per_page']) && $_GET['per_page'] !== '' ) ? intval(sanitize_text_field($_GET['per_page'])) : 20;
$current_page = ( isset($_GET['p']) && $_GET['p'] !== '' ) ? intval(sanitize_text_field($_GET['p'])) : 1;

$page = admin_url('options-general.php?page=wp_simple_locator&tab=search-history');
if ( $search_rows ) :
?>
<div class="wpsl-settings">
	<div class="row subhead">
		<h4><?php 
			$label = ( $is_search ) 
				? __('Search Results Count', 'simple-locator') 
				: __('Total Search Count', 'simple-locator');
			echo $label . ': ' . $total_count;
			?>
		</h4>
	</div><!-- .row -->
	<div class="row wpsl-search-history-actions">
		<?php if ( $is_search ) : ?>
		<a href="<?php echo $page; ?>" class="button"><?php _e('View All', 'simple-locator'); ?></a>
		<?php endif; ?>
		<form action="<?php echo admin_url('admin-post.php'); ?>" method="post">
			<input type="hidden" name="action" value="wpslhistorycsv">
			<input type="hidden" name="page" value="<?php echo $page; ?>">
			<input type="hidden" name="q" value="<?php if ( isset($search_term)  ) echo $search_term; ?>">
			<input type="hidden" name="date_start" value="<?php if ( isset($date_start) ) echo sanitize_text_field($_GET['date_start']); ?>">
			<input type="hidden" name="date_end" value="<?php if ( isset($date_end) ) echo sanitize_text_field($_GET['date_end']); ?>">
			<button class="button button-primary"><?php _e('Download CSV', 'simple-locator'); ?></button>
		</form>

		<form action="<?php echo admin_url('admin-post.php'); ?>" method="post" class="right">
			<input type="hidden" name="action" value="wpslhistoryclear">
			<input type="hidden" name="page" value="<?php echo $page; ?>">
			<button class="button-danger"><?php _e('Clear Log', 'simple-locator'); ?></button>
		</form>
	</div>

	<form action="<?php echo admin_url('admin-post.php'); ?>" method="post">
	
	<div class="row border-top">
		<div class="wpsl-search-history-form">
			<input type="hidden" name="action" value="wpslhistorysearch">
			<input type="hidden" name="page" value="<?php echo $page; ?>">
			<?php wp_nonce_field('wpsl-nonce', 'nonce'); ?>
			<div class="inputs">
				<div class="keyword">
					<label><?php _e('Keyword', 'simple-locator'); ?></label>
					<input type="text" name="search_term" placeholder="<?php _e('Search Terms', 'simple-locator'); ?>" value="<?php if ( isset($search_term) ) echo $search_term; ?>" />
				</div><!-- .keyword -->
				<div class="date-range">
					<label><?php _e('Date Range', 'simple-locator'); ?></label>
					<input type="text" name="date_start" data-date-picker placeholder="<?php _e('Start', 'simple-locator'); ?>" <?php if ( isset($date_start) ) echo 'value="' . $date_start . '"';?>>
					<input type="text" name="date_end" data-date-picker placeholder="<?php _e('End', 'simple-locator'); ?>" <?php if ( isset($date_end) ) echo 'value="' . $date_end . '"';?>>
				</div><!-- .date-range -->
			</div><!-- .inputs -->
			<input type="submit" name="" class="button" value="Search">
		</div><!-- .wpsl-search-history-form -->

		<div class="wpsl-search-history-table-header">
			<h3><?php echo __('Page', 'simple-locator') . ' ' . $current_page . ' ' . __('of', 'simple-locator') . ' ' . $this->search_repo->totalPages(); ?> </h3>
			<div class="wpsl-search-history-pagination">
				<?php echo $this->search_repo->pagination(__('Back', 'simple-locator'), __('Next', 'simple-locator')); ?>
			</div>
			<select name="per_page" data-simple-locator-history-per-page>
				<?php
				$page_options = array(200, 100, 50, 20, 10, 5, 3);
				foreach ( $page_options as $option ) :
					$out = '<option value="' . $option . '"';
					if ( $option == $per_page ) $out .= ' selected';
					$out .= '>' . $option . ' ' . __('Per Page', 'simple-locator') . '</option>';
					echo $out;
				endforeach;
				?>
			</select>
		</div><!-- .wpsl-search-history-table-header -->
	</div><!-- .row -->

	</form>

	<div id="wpsl-search-history-map" data-simple-locator-search-history-map></div>

	<table class="wpsl-search-history-table">
		<thead>
			<tr>
				<th><?php _e('Date', 'simple-locator'); ?></th>
				<th><?php _e('User IP', 'simple-locator'); ?></th>
				<th><?php _e('Search Term', 'simple-locator'); ?></th>
				<th><?php _e('Search Term - Formatted', 'simple-locator'); ?></th>
				<th><?php _e('Distance', 'simple-locator'); ?></th>
				<th>&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<?php 
				foreach ( $search_rows as $search ) : 
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
							<?php _e('View', 'simple-locator'); ?>
						</a>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<div class="wpsl-search-history-pagination bottom">
		<?php echo $this->search_repo->pagination(__('Back', 'simple-locator'), __('Next', 'simple-locator')); ?>
	</div>

	<script>
		var search_history_locations = [
			<?php 
			$i = 1;
			$out = "";
			foreach ( $search_rows as $search ) :
				$date = date_i18n( $date_format, strtotime( $search->time ) );
				$out .= "{";
				$out .= 'search_term : "' . $search->search_term . '",';
				$out .= 'search_term_formatted : "' . $search->search_term_formatted . '",';
				$out .= 'user_ip : "' . $search->user_ip . '",';
				$out .= 'latitude : ' . $search->search_lat . ',';
				$out .= 'longitude : ' . $search->search_lng . ',';
				$out .= 'date : "' . $date. '",';
				$out .= 'distance : ' . $search->distance;
				$out .= ( $i < count($search_rows) ) ? "}," : "}";
			$i++; endforeach; 
			echo $out;
			?>
		];
	</script>
</div><!-- .wpsl-settings -->

<?php else : // No searches yet ?>
<h2>
	<?php 
	echo ( $is_search ) 
		? __('0 Results for ', 'simple-locator') . sanitize_text_field($_GET['q'])
		: __('There are currently no logged searches.', 'simple-locator');
	?>
</h2>
<?php endif; ?>