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

require_once(INCLUDES_PATH . '/api/fetch_neis_userinfo.php');


class UserIdCache {

	private static $users = array();
	
	public static function getItem ($key) {
		if (isset(self::$users[$key])) {
			return self::$users[$key];
		} else {
			return null;
		}
	}
	
	public static function setItem ($key, $value) {
		self::$users[$key] = $value;
	}

}

function detect_userid ($display_name, $force=false) {
	
	if (!$force) {
		$cachedUserId = UserIdCache::getItem($display_name);
		if (!is_null($cachedUserId)) {
			return $cachedUserId;
		}
	}
	
	$neis_userinfo = fetch_neis_userinfo($display_name); // This function also has a cache of its own, so we don't query the same from the API twice
	
	if (!$neis_userinfo) {
		return false;
	}
	
	$userid = intval(@$neis_userinfo->contributor->uid);
	
	if (!$userid) {
		return false;
	}
	
	UserIdCache::setItem($display_name, $userid);
	
	return $userid;

}