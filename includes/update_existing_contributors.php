<?php

include_once(INCLUDES_PATH.'/log.php');

include_once(INCLUDES_PATH.'/files/chronological_contributor_list.php');
require_once(INCLUDES_PATH.'/files/update_contributor_info.php');

function update_existing_contributors () {

	$ac = start_action('Updating existing contributors');
		
		$success = STATUS_OK;
		
		$users = chronological_contributor_list();
		
		foreach ($users as $userid) {
			
			if (!$userid) continue;
			
			if ( !($userid = intval($userid)) ) {
				$ac->log_userfile_status(
					STATUS_INVALID_USERID,
					$userid
				);
				$success = false;
			} else {
			
				$success = $ac->log_userfile_status(
					update_contributor_info($userid),
					$userid,
					get_last_contributor_displayname()
				) && $success;
				
				@UserIdCache::setItem(get_last_contributor_displayname(), $userid);
				
			}
		}
		
	end_action($ac, $success);
}