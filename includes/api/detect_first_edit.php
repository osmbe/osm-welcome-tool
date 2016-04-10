<?php

require_once(INCLUDES_PATH . '/api/fetch_neis_userinfo.php');


function detect_first_edit ($display_name) {
	
	$userinfo = fetch_neis_userinfo($display_name); // This function has a cache, so we don't query the same from the API twice
	
	$first_edit = @$userinfo->changesets->f_tstamp;
	
	$first_edit = strtotime($first_edit);
	
	if (!$first_edit) return false;
	
	return $first_edit;
	
}