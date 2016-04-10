<?php

include_once(INCLUDES_PATH . '/files/get_contributor_file_path.php');
include_once(INCLUDES_PATH . '/api/detect_userid.php');

function read_contributor_info ($userid) {
	$result = json_decode(
		@file_get_contents(get_contributor_file_path($userid))
	);
	
	if (isset($result->display_name) && !is_null($result->display_name)) {
		UserIdCache::setItem($result->display_name, $userid);
	}
	
	return $result;
}

?>
