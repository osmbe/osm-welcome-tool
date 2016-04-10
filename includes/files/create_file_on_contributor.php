<?php

include_once(INCLUDES_PATH.'/files/contributor_file_exists.php');
include_once(INCLUDES_PATH.'/files/update_contributor_info.php');

function create_file_on_contributor ($userid, $firstedit=null) {
	if (contributor_file_exists($userid)) return STATUS_OK_ALREADY_HAVE_FILE;
	
	if ( !($gooduserid = intval($userid)) ) {
		die('CF: Invalid user id "'.htmlentities($userid, null, 'UTF-8').'"');
	}
	
	$status = update_contributor_info($userid, $firstedit, DETECT_LANGUAGE|DETECT_EDITORS);
	
	if (($status & STATUS_ALL_FAILS) !== 0) {
		return $status;
	}
	
	return $status;
}

?>
