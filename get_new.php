<?php

require_once('htdocs/defines.php');

include_once('cli_log.php');
//include_once(INCLUDES_PATH.'/api/detect_userid.php');
require_once(INCLUDES_PATH.'/get_new_contributors.php');
require_once(INCLUDES_PATH.'/files/build_chronological_list.php');

get_new_contributors();

file_put_contents(INCLUDES_PATH.'/../contributors/@last_updated.txt', time());

build_chronological_list();

?>
