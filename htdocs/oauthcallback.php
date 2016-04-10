<?php

$authorization_required = false;

require_once('paths.php');
session_regenerate_id(true);
require_once(INCLUDES_PATH . '/oauth/OAuth.php');
require_once(INCLUDES_PATH . '/is_sane_returnto.php');





if(!isset($_GET['oauth_token'])) {
	die('An error occurred in the OAuth process. <a href="index.php">Click here to return to the main page.</a><br/><br/>Error: There is no OAuth token!');
}
if(!isset($_SESSION['request_secret'])) {
	die('An error occurred in the OAuth process. <a href="index.php">Click here to return to the main page.</a><br/><br/>Error: There is no OAuth secret!');
}

$request_key = $_GET['oauth_token'];
$request_secret = $_SESSION['request_secret'];

unset($_SESSION['request_secret']);


$request_token = new OAuthConsumer($request_key, $request_secret);


/*** Exchange request token for access token ****************/

$endpoint = 'https://www.openstreetmap.org/oauth/access_token';
$params = array();

$acc_req = OAuthRequest::from_consumer_and_token($consumer, $request_token, "GET", $endpoint, $params);
$acc_req->sign_request($sig_method, $consumer, $request_token);

$contents = @file_get_contents($acc_req);
if (!$contents) {
	die('Could not validate access token. <a href="index.php">Click here to return to the main page.</a>');
}
parse_str($contents, $access_token);

$access_key = $access_token["oauth_token"];
$access_secret = $access_token["oauth_token_secret"];
$access_token_pair = new OAuthConsumer($access_key, $access_secret);


/*** Get user details and save authentication ***************/

require_once(INCLUDES_PATH . '/api/api.php');
require_once(INCLUDES_PATH . '/oauth/put_session.php');


$response = call_api('user/details', null, 'GET', $access_token_pair);

if (!$response) {
	die('An error occurred while querying the API. <a href="index.php">Click here to return to the main page.</a><br/><br/>Error: No valid response');
}

$user_info = simplexml_load_string($response);
$displayname = (string) $user_info->user['display_name'];
$userid = (string) $user_info->user['id'];
$image = (string) $user_info->user->img['href'];

if (!$userid) {
	die('An error occurred while querying the API. <a href="index.php">Click here to return to the main page.</a><br/><br/>Error: No user id in response');
}


$success = put_session($userid, $displayname, $image, $access_key, $access_secret);


if ($success && $_SESSION['userid']) {
	$returnto = 'index.php';
	if (isset($_SESSION['returnto']) && is_sane_returnto($_SESSION['returnto'])) {
		$returnto = $_SESSION['returnto'];
	}
	die('<noscript><meta http-equiv="refresh" content="0; url='.$returnto.'"></noscript><script type="text/javascript">window.location="'.$returnto.'";</script>Authentication successful. <a href="'.$returnto.'">Click here to return to the page you were on.</a>');
} else {
	die('Failed to store authentication. <a href="index.php">Click here to return to the main page.</a>');
}


?>