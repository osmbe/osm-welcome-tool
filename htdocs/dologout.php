<?php

$authorization_required = false;
require_once('paths.php');

if (!$_SESSION['userid']) {
	header('Location: index.php');
	die('Not logged in');
}


require_once(INCLUDES_PATH . '/do_logout.php');
do_logout();


require_once(INCLUDES_PATH . '/is_sane_returnto.php');

$returnto = '';
if ($_GET['returnto'] && is_sane_returnto($_GET['returnto'])) {
	$returnto = rawurlencode($_GET['returnto']);
}

header('Location: login.php?loggedout=true'.(
	$returnto
	? '&returnto='.$returnto
	: ''
));
die('You have been logged out from this service. Note: you have not been logged out from openstreetmap.org itself.<br/><br/><a href="dologin.php'.(
	$returnto
	? '?returnto='.$returnto
	: ''
).'">Click here to log in again.</a>');


?>