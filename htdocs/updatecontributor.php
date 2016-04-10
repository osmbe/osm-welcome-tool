<?php

include_once('paths.php');
include_once(INCLUDES_PATH . '/files/update_contributor_info.php');

if (!isset($_GET['userid'])) {
	die('No user id!');
}
if ( !($userid = intval($_GET['userid'])) ) {
	die('Invalid user id "'.htmlentities($_GET['userid'], null, 'UTF-8').'"');
}

update_contributor_info($userid, null, DETECT_EDITORS);

header('Location: contributor.php?updated&userid='.$userid);

?>