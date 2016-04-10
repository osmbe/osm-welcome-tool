<?php

include_once('paths.php');
include_once(INCLUDES_PATH . '/files/update_contributor_info.php');

if (@$_POST['type'] === 'welcome') {
	var_dump(update_contributor_info(@$_POST['userid'], null, 0, array('welcomed_by'=>(int) $_SESSION['userid'])));
	//die();
} else {
	//die('not welcome');
}

header('Location: index.php');

?>