<?php

function get_all_welcomers () {
	$welcomers_files = scandir(INCLUDES_PATH . '/../users/');
	$welcomers = array();
	foreach ($welcomers_files as $welcomer_file) {
		if (preg_match('/^([0-9]+)\.json$/', $welcomer_file, $matches))
		array_push($welcomers, $matches[1]);
	}
	return $welcomers;
}

?>
