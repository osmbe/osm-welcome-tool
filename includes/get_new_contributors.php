<?php

include_once(INCLUDES_PATH.'/log.php');
include_once(INCLUDES_PATH.'/feed/request_feed.php');
//include_once(INCLUDES_PATH.'/feed/rss_helpers.php');
include_once(INCLUDES_PATH.'/files/create_file_on_contributor.php');
//include_once(INCLUDES_PATH.'/api/detect_userid.php');
include_once(INCLUDES_PATH.'/api/detect_language.php');

function get_new_contributors () {
	
	$ac = start_action('Fetching RSS feed');

		//$feedString = request_feed();
		// For testing:
		$feedString = file_get_contents(INCLUDES_PATH.'/new2.xml');

	end_action($ac);
	$ac = start_action('Parsing RSS feed');

		$feed = simplexml_load_string($feedString);

	end_action($ac);
	
	
	$ac = start_action('Reading new contributors and creating files');
	
	
		$ulw = new UserListWriter();
		
    //normalize feed entry content keys
    $keyMap = [
        'UID' => 'user_id',
        'Contributor' => 'username',
        'Mapper since' => 'registration_date',
        'Registration' => 'first_edit_date',
        'First active near' => 'first_edit_location',
        'First Changeset' => 'first_changeset_id',
        'Editor used' => 'first_changeset_editor',
    ];

		$success = true;
		$entries =$feed->entry;

		foreach ($entries as $entry) {

            $href = (string)$entry->link['href'];
            $userUpdated = (string)$entry->updated;
            $content = (string)$entry->content;

            preg_match_all('/<b>(.+):<\/b>(.+)<\/br>/', $content, $matches);

            $tempUser = [];
            $count = count($matches[1]);
            for ($j = 0; $j < $count; $j++) {
                $value = $matches[2][$j];
                $regValue = '';
                if (preg_match('/<a.*>(.*)<\/a>/', $value, $regValue)) {
                    $value = $regValue[1];
                }
                $key = $keyMap[$matches[1][$j]];
                $tempUser[$key] = $value;
            }

			//$display_name = extract_display_name_from_rss_entry($entry, $ac);
			//$userid = detect_userid($display_name);
			$display_name = trim($tempUser['username']);
			$userid = (int)trim($tempUser['user_id']);
			
			if (!is_int($userid)) {
				$success = false;
				$ac->log_userfile_status(
					STATUS_COULD_NOT_DETECT_USERID,
					0,
					$display_name
				);
				
				continue;
			}
			
			//$firstedit = extract_firstedit_from_rss_entry($entry);
			$firstedit = strtotime($userUpdated);
			
			$success = $ac->log_userfile_status(
				create_file_on_contributor($userid, $firstedit),
				$userid
			) && $success;
			
		}
		
		unset($ulw);
	
	end_action($ac, $success);
}

?>
