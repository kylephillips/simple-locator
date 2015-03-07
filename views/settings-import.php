<div class="simple-locator-import-tab">
	<?php 
	// Form Errors
	if ( isset($_GET['error']) ) echo '<div class="error"><p>' . $_GET['error'] . '</p></div>';
	
	// Steps
	if ( isset($_GET['step']) && in_array($_GET['step'], array('1', '2', '3')) ) {
		include 'settings-import-' . $_GET['step'] . '.php';
	} else {
		include 'settings-import-1.php';
	}
	?>
</div><!-- .simple-locator-import-tab" -->