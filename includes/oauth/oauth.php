<?php

$conskey = '';
$conssec = '';

if (!$conskey || !$conssec) die("Fill in OSM OAuth consumer credentials in includes/oauth/oauth.php");

$oauth = new OAuth($conskey, $conssec, OAUTH_SIG_METHOD_HMACSHA1, OAUTH_AUTH_TYPE_URI);

$conskey = null;
$conssec = null;

?>

