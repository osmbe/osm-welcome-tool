<?php

require_once(INCLUDES_PATH.'/oauth/OAuth.php');
require_once(INCLUDES_PATH.'/oauth/get_user_auth.php');

$api_url = 'https://api.openstreetmap.org/api/0.6/';
$userAgent = 'NewOSMContributorsInBelgium, currently testing, contact at ruben@janmaes.com';

function call_api ($action, $params=null, $method='GET', OAuthConsumer $access_token_pair=null) {

	echo 'Calling API '.$action.PHP_EOL;
	
	if (is_null($access_token_pair)) {
		$access_token_pair = get_user_auth();
	}
	
	$endpoint = $GLOBALS['api_url'] . $action;
	
	$acc_req = OAuthRequest::from_consumer_and_token($GLOBALS['consumer'], $access_token_pair, $method, $endpoint, $params);
	$acc_req->sign_request($GLOBALS['sig_method'], $GLOBALS['consumer'], $access_token_pair);
	
	
	$opts = array(
		'http' => array(
			'method' => $method,
			'user_agent' => $GLOBALS['userAgent']
		)
	);
	$context = stream_context_create($opts);

	$response = file_get_contents($acc_req, false, $context);
	
	return $response;
}

?>