<?php

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