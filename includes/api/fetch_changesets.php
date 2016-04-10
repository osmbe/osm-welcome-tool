<?php

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
			call_api('changesets', array('user'=>$userid), 'GET')
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
			call_api('changesets', array('display_name'=>$display_name), 'GET')
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