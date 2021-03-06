<?php
/***************************************************************************
 *   copyright				: (C) 2008 - 2016 WeBid
 *   site					: http://www.webidsupport.com/
 ***************************************************************************/

/***************************************************************************
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version. Although none of the code may be
 *   sold. If you have been sold this script, get a refund.
 ***************************************************************************/

define('InAdmin', 1);
$current_page = 'users';
include '../common.php';
include $include_path . 'functions_admin.php';
include 'loggedin.inc.php';
include $main_path . 'ckeditor/ckeditor.php';

unset($ERR);

$subject = (isset($_POST['subject'])) ? stripslashes($_POST['subject']) : '';
$content = (isset($_POST['content'])) ? stripslashes($_POST['content']) : '';
$is_preview = false;

if (isset($_POST['action']) && $_POST['action'] == 'submit')
{
	if (empty($subject) || empty($content))
	{
		$ERR = $ERR_5014;
	}
	else
	{
		$COUNTER = 0;
		$query = "SELECT email FROM " . $DBPrefix . "users WHERE nletter = 1";
		switch($_POST['usersfilter'])
		{
			case 'active':
				$query .= ' AND suspended = 0';
				break;
			case 'admin':
				$query .= ' AND suspended = 1';
				break;
			case 'fee':
				$query .= ' AND suspended = 9';
				break;
			case 'confirmed':
				$query .= ' AND suspended = 8';
				break;
		}
		$headers = 'From:' . $system->SETTINGS['sitename'] . ' <' . $system->SETTINGS['adminmail'] . '>' . "\n" . 'Content-Type: text/html; charset=' . $CHARSET;
		$res = $db->direct_query($query);
		while ($row = $db->fetch())
		{
			if (mail($row['email'], $subject, $content, $headers))
			{
				$COUNTER++;
			}
		}
		$ERR = $COUNTER . $MSG['5300'];
	}
}
elseif (isset($_POST['action']) && $_POST['action'] == 'preview')
{
	$is_preview = true;
}

$USERSFILTER = array('all' => $MSG['5296'],
	'active' => $MSG['5291'],
	'admin' => $MSG['5294'],
	'fee' => $MSG['5293'],
	'confirmed' => $MSG['5292']);

$selectsetting = (isset($_POST['usersfilter'])) ? $_POST['usersfilter'] : '';

$CKEditor = new CKEditor();
$CKEditor->basePath = $main_path . 'ckeditor/';
$CKEditor->returnOutput = true;
$CKEditor->config['width'] = 550;
$CKEditor->config['height'] = 400;

$template->assign_vars(array(
		'ERROR' => (isset($ERR)) ? $ERR : '',
		'SITEURL' => $system->SETTINGS['siteurl'],
		'SELECTBOX' => generateSelect('usersfilter', $USERSFILTER),
		'SUBJECT' => $subject,
		'EDITOR' => $CKEditor->editor('content', stripslashes($content)),
		'PREVIEW' => $content,

		'B_PREVIEW' => $is_preview
		));

$template->set_filenames(array(
		'body' => 'newsletter.tpl'
		));
$template->display('body');
?>
