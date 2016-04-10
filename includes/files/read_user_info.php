<?php

include_once(INCLUDES_PATH . '/files/get_user_file_path.php');

function read_user_info ($userid) {
	return json_decode(
		@file_get_contents(get_user_file_path($userid))
	);
}

?>
