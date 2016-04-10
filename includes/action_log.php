<?php

function action_log ($action) {
	$fh = fopen(INCLUDES_PATH . '/../actionlog.log', 'a');

	fwrite($fh, '['.date('Y-m-d (D) H:i:s').'] '.$_SESSION['displayname'].' '.$action."\n");
}

?>