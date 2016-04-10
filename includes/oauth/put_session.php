<?php

function put_session ($userid, $displayname, $image, $access_key, $access_secret) {
	$_SESSION['userid'] = $userid;
	$_SESSION['displayname'] = $displayname;
	$_SESSION['image'] = $image;
	$_SESSION['access_key'] = $access_key;
	$_SESSION['access_secret'] = $access_secret;
	
	
	file_put_contents(INCLUDES_PATH . '/../users/' . rawurlencode($userid) . '.json', json_encode(
		array(
			'display_name' => $displayname,
			'img' => $image,
			'last_logged_in_here' => time()
		)
	));
	
	return true;
}

?>