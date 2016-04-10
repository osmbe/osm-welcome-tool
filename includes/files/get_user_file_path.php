<?php

function get_user_file_path ($userid) {
	if ( !($gooduserid = intval($userid)) ) {
		die('Invalid user id "'.htmlentities($userid, null, 'UTF-8').'"');
	}
	
	return INCLUDES_PATH . '/../users/' . $gooduserid . '.json';
}

?>
