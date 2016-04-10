<?php

if (!function_exists('start_action')) {
	include_once(INCLUDES_PATH.'/log.php');
}
require_once(INCLUDES_PATH.'/files/UserListWriter.php');
require_once(INCLUDES_PATH.'/files/read_contributor_info.php');

function build_chronological_list () {
	
	$ac = start_action('Building chronological list of contributors');
		$success = true;
	
		$dh = opendir(INCLUDES_PATH.'/../contributors');
		
		$ulw = new UserListWriter(true);
		
		$contributors = array();
		
		while (($file = readdir($dh)) !== false) {
			if (substr($file,0,1) === '@') continue;
			
			if (preg_match('/^(\d{1,15})\.json$/', $file, $matches) === 1) {
				$userid = $matches[1];
				
				$info = read_contributor_info($userid);
				
				if ($info) {
				
					if (!isset($info->first_edit)) {
						$success = false;
						$ac->log_userfile_status(STATUS_NO_FIRST_EDIT, $userid);
						
					} else {
						$contributors[$info->first_edit] = $userid;
					}
					
				} else {
					$success = false;
					$ac->log_userfile_status(STATUS_COULD_NOT_FETCH_INFO, $userid);
				}
			}
		}
		krsort($contributors); // Sort array by key in reverse order

		foreach ($contributors as $userid) {
			if (!$ulw->addUser($userid)) {
				$success = $ac->log_userfile_status(STATUS_COULD_NOT_ADD_TO_LIST, $userid) && $success;
			}
		}
	
	end_action($ac, $success);
	
}

?>
