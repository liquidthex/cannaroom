<?php
#
# Copyright © 2004-2012 Chat software by www.flashcoms.com
#
# This is the demo handler. Replace it with your custom logic.
#

$answer = Logout();
if(!$answer) $answer = '<invalid_request/>';

header('Content-type: text/xml');
echo $answer;

function Logout()
{
	# NOTE: you can`t use $_SESSION here,
	# because requests are sent by FMS and not by browser on user's side.
	$uid = GetParam('uid');
	if(!$uid) return false;

	// your logout logic here

	return '<logout/>';
}

function GetParam($name, $default = '')
{
	if(isset($_POST[$name])) return $_POST[$name];
	if(isset($_GET[$name])) return $_GET[$name];
	return $default;
}

?>
