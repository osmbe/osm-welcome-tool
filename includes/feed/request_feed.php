<?php

function request_feed () {
	$rssUrl = 'http://resultmaps.neis-one.org/newestosmcountryfeed?c=Belgium';
	$userAgent = 'osmwelcome.unitedbashers.com, contact at ruben@janmaes.com';


	// Create a stream
	$opts = array(
		'http' => array(
			'method' => 'GET',
			'user_agent' => $userAgent
		)
	);
	$context = stream_context_create($opts);

	// Open the file using the HTTP headers set above
	return file_get_contents($rssUrl, false, $context);
}

?>