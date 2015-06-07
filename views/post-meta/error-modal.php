<?php
/**
* Error Modal
*/
?>
<div class="wpsl-modal wpsl-error-modal fade" id="wpsl-error-modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body">
				<h3><?php _e('The address could not be found at this time.', 'wpsimplelocator'); ?></h3>
				<a href="#" class="wpsl-cancel-trash button" data-dismiss="modal"><?php _e('Cancel', 'wpsimplelocator'); ?></a>
				<a href="#" class="wpsl-address-confirm button-primary"><?php _e('Save without location', 'wpsimplelocator'); ?></a>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->