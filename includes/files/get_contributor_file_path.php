<?php

function get_contributor_file_path ($userid) {
	if ( !($gooduserid = intval($userid)) ) {
		die('FP: Invalid user id "'.htmlentities($userid, null, 'UTF-8').'"');
	}
	
	return INCLUDES_PATH . '/../contributors/' . $gooduserid . '.json';
}

function get_contributor_correspondence_path ($userid) {
	if ( !($gooduserid = intval($userid)) ) {
		die('FP: Invalid user id "'.htmlentities($userid, null, 'UTF-8').'"');
	}
	
	return INCLUDES_PATH . '/../contributors/' . $gooduserid . '-correspondence.json';
}

?>
