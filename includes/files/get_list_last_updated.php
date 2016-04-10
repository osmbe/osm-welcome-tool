<?php

function get_list_last_updated () {
	return intval(file_get_contents(INCLUDES_PATH.'/../contributors/@last_updated.txt'));
}

?>
