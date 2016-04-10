<?php

@session_start();

class UserAuthStorage {

	private static $oauth_consumer;
	
	public static function get_user_auth () {
		if (self::$oauth_consumer) {
			return self::$oauth_consumer;
		}
		
		if (!isset($_SESSION['access_key'])) {
			return false;
		}
		if (!isset($_SESSION['access_secret'])) {
			return false;
		}
		$access_key = $_SESSION['access_key'];
		$access_secret = $_SESSION['access_secret'];
		
		self::$oauth_consumer = new OAuthConsumer($access_key, $access_secret);
		return self::$oauth_consumer;
	}
	
}

function get_user_auth () {
	return UserAuthStorage::get_user_auth();
}

?>