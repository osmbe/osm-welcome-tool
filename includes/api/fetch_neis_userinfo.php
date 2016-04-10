<?php

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