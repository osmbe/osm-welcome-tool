<?php

require_once(INCLUDES_PATH . '/api/api.php');

function fetch_user_info () {
	return call_api('user/details', null, 'GET');
}

?>