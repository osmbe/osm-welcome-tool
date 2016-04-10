<?php

function do_logout ($dont_destroy_session=false) {
	unset($_SESSION['display_name']);
	unset($_SESSION['user_id']);
	unset($_SESSION['access_key']);
	unset($_SESSION['access_secret']);

	if (!$dont_destroy_session) {
		session_destroy();
	}
	setcookie ('PHPSESSID', 'session ended', time()-3600, APP_HTTP_PATH, null, false, true);
}

?>