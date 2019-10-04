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