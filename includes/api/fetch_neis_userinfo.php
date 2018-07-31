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

require_once(INCLUDES_PATH . '/api/neis-api.php');

class NeisUserInfoCache {

	private static $cache = array();
	
	public static function getItem ($key) {
		if (isset(self::$cache[$key])) {
			return self::$cache[$key];
		} else {
			return null;
		}
	}
	
	public static function setItem ($key, $value) {
		self::$cache[$key] = $value;
	}

}

function fetch_neis_userinfo ($display_name, $force=false) {
	error_log('fetch#1');

	if (!$force) {
		$actionResult = NeisUserInfoCache::getItem($display_name);
		if (!is_null($actionResult)) {
			return $actionResult;
		}
	}
	
	error_log('fetch#2');
	$response = json_decode(
		call_neis_api(
			'user/'.rawurlencode($display_name), // action
			null, // params
			'GET' // method
		) // This function has a cache, so we don't query the same from the API twice
	);
	
	if (@$response->blablub === 'baaaaaaaaaam') {
		return false;
	}
	
	NeisUserInfoCache::setItem($display_name, $response);
	
	return $response;
	
}

?>