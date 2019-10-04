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

require_once(INCLUDES_PATH . '/api/api.php');
require_once(INCLUDES_PATH . '/api/detect_userid.php');


class FetchChangesets {
	
	private static $changesets;
	
	public static function ByUserId ($userid, $force=false) {
		if ( !($gooduserid = intval($userid)) ) {
			die('CS: Invalid user id "'.htmlentities($userid, null, 'UTF-8').'"');
		}
		$userid = $gooduserid;
		
		if (!$force && isset(self::$changesets[$userid])) {
			return self::$changesets[$userid];
		}
		
		$changesets = simplexml_load_string(
			call_api('changesets', array('user'=>$userid), 'GET', NO_AUTH)
		);
		self::$changesets[$userid] = $changesets;
		
		return $changesets;
	}
		
	public static function ByDisplayName ($display_name) {
	
		$chached_userid = UserIdCache::getItem($display_name);
		if (!is_null($chached_userid)) {
			return self::ByUserId($chached_userid);
		}
		
		$changesets = simplexml_load_string(
			call_api('changesets', array('display_name'=>$display_name), 'GET', NO_AUTH)
		);
		
		if (!isset($changesets->changeset[0])) {
			return null;
		}
		
		$userid = intval(
			(string) ($changesets->changeset[0]['uid'])
		);
		
		self::$changesets[$userid] = $changesets;
		UserIdCache::setItem($display_name, $userid);
		
		return $changesets;
	}
	
}

$changesets = array();

function fetch_changesets_by_display_name ($display_name) {
	return FetchChangesets::ByDisplayName($display_name);
}
function fetch_changesets_by_user_id ($userid) {
	return FetchChangesets::ByUserId($userid);
}
