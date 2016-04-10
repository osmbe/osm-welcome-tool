<?php

require_once('./paths.php');
require_once(INCLUDES_PATH.'/api/detect_userid.php');

echo '<form>Display name: <input name="display_name"></input><input type="submit" value="Lookup userid"></input></form> ';

if (isset($_GET['display_name']) && $_GET['display_name']) {
	$userid = detect_userid($_GET['display_name']);
	echo '<br/>';
	
	if (!$userid) {
		echo 'user not found';
	} else {
		echo $userid;
	}
}

?>