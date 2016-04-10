<?php

$neis_api_url = 'http://hdyc.neis-one.org/';
$neis_user_agent = 'osmwelcome.unitedbashers.com, contact at ruben@janmaes.com';

function call_neis_api ($action, $params=null, $method='GET') {

	echo 'Calling Neis API '.$action.PHP_EOL;
	
	$endpoint = $GLOBALS['neis_api_url'] . $action;
	
	$opts = array(
		'http' => array(
			'method' => $method,
			'user_agent' => $GLOBALS['neis_user_agent']
		)
	);
	$context = stream_context_create($opts);

	$result = file_get_contents($endpoint, false, $context);
	
	return $result;
}

?>