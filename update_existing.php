<?php

define('INCLUDES_PATH', '/var/www/osmwelcome.unitedbashers.com/includes');

include_once('cli_log.php');
include_once(INCLUDES_PATH.'/api/detect_userid.php');
require_once(INCLUDES_PATH.'/update_existing_contributors.php');

update_existing_contributors();

?>
