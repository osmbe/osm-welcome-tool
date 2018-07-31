<?php
/* This file is part of osm-welcome: a platform to coordinate welcoming of OpenStreetMap mappers
 * Copyright © 2018  Midgard and osm-welcome contributors
 *
 * This program is free software: you can redistribute it and/or modify it under the terms of the
 * GNU Affero General Public License as published by the Free Software Foundation, either version 3
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License along with this
 * program. If not, see <https://www.gnu.org/licenses/>.
 */

include_once(INCLUDES_PATH.'/log.php');
include_once(INCLUDES_PATH.'/feed/request_feed.php');
include_once(INCLUDES_PATH.'/feed/rss_helpers.php');
include_once(INCLUDES_PATH.'/files/create_file_on_contributor.php');
include_once(INCLUDES_PATH.'/api/detect_userid.php');
include_once(INCLUDES_PATH.'/api/detect_language.php');

function get_new_contributors () {
	
	$ac = start_action('Fetching RSS feed');

		$feedString = request_feed();
		// For testing:
		//$feedString = file_get_contents(INCLUDES_PATH.'/example.xml');

	end_action($ac);
	$ac = start_action('Parsing RSS feed');

		$feed = simplexml_load_string($feedString);

	end_action($ac);
	
	
	$ac = start_action('Reading new contributors and creating files');
	
	
		$ulw = new UserListWriter();
		

		$success = true;
		for ($i = count($feed->entry)-1 ; $i !== -1 ; $i--) {
			
			$entry = $feed->entry[$i];
			
			$display_name = extract_display_name_from_rss_entry($entry, $ac);
			
			$userid = detect_userid($display_name);
			
			if (!is_int($userid)) {
				$success = false;
				$ac->log_userfile_status(
					STATUS_COULD_NOT_DETECT_USERID,
					0,
					$display_name
				);
				
				continue;
			}
			
			$firstedit = extract_firstedit_from_rss_entry($entry);
			
			$success = $ac->log_userfile_status(
				create_file_on_contributor($userid, $firstedit),
				$userid
			) && $success;
			
		}
		
		unset($ulw);
	
	end_action($ac, $success);
}

?>