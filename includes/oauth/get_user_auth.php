<?php

@session_start();

function get_user_auth () {
	if (!isset($_SESSION['access_key'])) {
		return false;
	}
	if (!isset($_SESSION['access_secret'])) {
		return false;
	}
	$access_key = $_SESSION['access_key'];
	$access_secret = $_SESSION['access_secret'];
	
	return [$access_key, $access_secret];
}

?>
