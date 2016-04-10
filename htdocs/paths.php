<?php
define('APP_HTTP_PATH', '/');
define('INCLUDES_PATH', '/var/www/osmwelcome/includes');

// Stay logged in for a month
session_set_cookie_params(60*60*24*31, APP_HTTP_PATH, null, false, true);
session_start();

if (!isset($authorization_required) || $authorization_required !== false) {
	$logged_in = isset($_SESSION['userid']) && isset($_SESSION['displayname']);
	
	if (!$logged_in && !$accessible_without_login) {
		header('Location: login.php?returnto='.rawurlencode($_SERVER['REQUEST_URI']));
		die('You are not logged in. Please <a href="dologin.php?returnto='.rawurlencode($_SERVER['REQUEST_URI']).'">log in with your OSM account</a>.');
	}
}

if (@$_SESSION['corrupted']) {
	do_logout(true);
	die('We have detected a possible attempt to take over your session. You have been logged out as a precaution. If you have a user agent spoofer, please set it to a constant value.<br/><br/><a href="login.php?returnto='.$_SERVER['REQUEST_URI'].'">Click here to proceed to the login page.</a>');
}

if (isset($_SESSION['useragent']) || isset($_SESSION['httpaccept']) || isset($_SESSION['ip'])) {
	if (
		$_SERVER['HTTP_USER_AGENT'] !== $_SESSION['useragent'] ||
		$_SERVER['REMOTE_ADDR'] !== $_SESSION['ip']
	) {
		require(INCLUDES_PATH.'/do_logout.php');
		
		$_SESSION['corrupted'] = true;
		do_logout(true);
		die('We have detected a possible attempt to take over your session. You have been logged out as a precaution. If you have a user agent spoofer, please set it to a constant value.<br/><br/><a href="login.php?returnto='.$_SERVER['REQUEST_URI'].'">Click here to proceed to the login page.</a>');
	}
} else {
	$_SESSION['useragent'] = $_SERVER['HTTP_USER_AGENT'];
	$_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
}

date_default_timezone_set('Europe/Brussels');


?>
