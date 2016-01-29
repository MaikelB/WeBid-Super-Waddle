<?php
include '../common.php';
if (!$user->is_logged_in())
{
	//if your not logged in you shouldn't be here
	//Send error message to the file uploader
	$content = array(
	'files' => array(
	array(
	'name' => "ERROR",
	'size' => "1337",
	'deleteUrl' => "",
	'deleteType' => "delete",
	'error' => "You are not logged in"
	)));
	echo json_encode($content);
	exit;
}
else
{
include $include_path . 'UploadHandler.php';
$upload_handle = new UploadHandler($system->SETTINGS['maxuploadsize'], $system->SETTINGS['maxpictures']);
}
