<?php 
namespace SimpleLocator\WPData;

/**
* Change the uploads directory for the file import
*/
class UploadFilter 
{

	public function __construct()
	{
		add_action('admin_init', array($this, 'changeUploadDirectory'));
	}

	/**
	* Change the upload directory for import uploads
	*/
	public function changeUploadDirectory()
	{
		if ( isset($_POST['action']) && $_POST['action'] == 'wpslimportupload' && !empty($_FILES['file']) ){
			add_filter( 'upload_dir', array($this, 'uploadFilter') );
    	}
	}

	/**
	* Upload Filter
	*/
	public function uploadFilter($upload)
	{
		$upload['subdir'] = '/simple-locator';
		$upload['path'] = $upload['basedir'] . $upload['subdir'];
		$upload['url']  = $upload['baseurl'] . $upload['subdir'];
		return $upload;
	}

}