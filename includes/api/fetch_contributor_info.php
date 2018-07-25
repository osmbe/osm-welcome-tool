<?php

require_once(INCLUDES_PATH . '/api/api.php');

function fetch_contributor_info ($userid) {
	if ( !($gooduserid = intval($userid)) ) {
		die('Invalid user id "'.htmlentities($userid, null, 'UTF-8').'"');
	}
	return simplexml_load_string(
		call_api('user/' . $gooduserid, null, 'GET', NO_AUTH)
	);
}

?>
