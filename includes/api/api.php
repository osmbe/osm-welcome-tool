<?php

require_once(INCLUDES_PATH.'/oauth/oauth.php');
require_once(INCLUDES_PATH.'/oauth/get_user_auth.php');

define("NO_AUTH", 1);

$api_url = 'https://api.openstreetmap.org/api/0.6/';
$userAgent = 'NewOSMContributorsInBelgium, currently testing, contact at osm.midgard@janmaes.com';

function call_api ($action, $params=null, $method='GET', $access_token_pair=NO_AUTH) {

	global $oauth;
	global $userAgent;

	echo 'Calling API '.$action.PHP_EOL;

	$endpoint = $GLOBALS['api_url'] . $action;

	if ($access_token_pair == NO_AUTH) {
		$opts = [
			'http' => [
				'method' => $method,
				'user_agent' => $userAgent
			]
		];
		$context = stream_context_create($opts);
		return file_get_contents($endpoint, false, $context);

	} else {
		if (is_null($access_token_pair)) {
			$access_token_pair = get_user_auth();
		}
		if (!$access_token_pair) {
			die("Not authorized");
		}

		$oauth->setToken($access_token_pair[0], $access_token_pair[1]);
		$oauth->fetch($endpoint, $params, $method, ["User-Agent: $userAgent"]);

		return $oauth->getLastResponse();
	}
}

?>
