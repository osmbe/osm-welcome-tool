<?php

$authorization_required = false;
require_once('paths.php');
require_once(INCLUDES_PATH . '/oauth/OAuth.php');
require_once(INCLUDES_PATH . '/is_sane_returnto.php');


if (isset($_GET['returnto']) && is_sane_returnto($_GET['returnto'])) {
	$_SESSION['returnto'] = $_GET['returnto'];
}



// Base url of this server
$domain = $_SERVER['HTTP_HOST'];
$protocol = (
	(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
	$_SERVER['SERVER_PORT'] == 443
) ? "https" : "http";
$base_url = "$protocol://$domain$base";


/*** Request token ******************************************/

$endpoint = 'https://www.openstreetmap.org/oauth/request_token';
$params = array();

$req_req = OAuthRequest::from_consumer_and_token($consumer, NULL, "GET", $endpoint, $params);
$req_req->sign_request($sig_method, $consumer, NULL);

parse_str(file_get_contents($req_req), $token);

$request_key = $token["oauth_token"];
$request_secret = $token["oauth_token_secret"];

$_SESSION['request_secret'] = $request_secret;


/*** Have token authorized: redirect user *******************/

$endpoint = 'https://www.openstreetmap.org/oauth/authorize';

if (!$request_key) {
	die('<p>Could not generate request key. Try again?</p>');
}

$callback_url = "$base_url/oauthcallback.php";
$auth_url = $endpoint . "?oauth_token=$request_key&oauth_callback=".urlencode($callback_url);

//die("Callback: $callback_url<br />Auth URL: $auth_url");
header("Location: $auth_url");



?>
