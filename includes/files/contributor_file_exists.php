<?php

include_once(INCLUDES_PATH.'/files/get_contributor_file_path.php');

function contributor_file_exists ($userid) {
	$file_path = get_contributor_file_path($userid);
	return file_exists($file_path);
}

?>
