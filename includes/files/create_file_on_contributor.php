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

include_once(INCLUDES_PATH.'/files/contributor_file_exists.php');
include_once(INCLUDES_PATH.'/files/update_contributor_info.php');

function create_file_on_contributor ($userid, $firstedit=null) {
	if (contributor_file_exists($userid)) return STATUS_OK_ALREADY_HAVE_FILE;
	
	if ( !($gooduserid = intval($userid)) ) {
		die('CF: Invalid user id "'.htmlentities($userid, null, 'UTF-8').'"');
	}
	
	$status = update_contributor_info($userid, $firstedit, DETECT_LANGUAGE|DETECT_EDITORS);
	
	if (($status & STATUS_ALL_FAILS) !== 0) {
		return $status;
	}
	
	return $status;
}

?>
