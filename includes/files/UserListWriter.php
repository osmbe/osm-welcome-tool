<?php

class UserListWriter {

	private $fh;
	
	public function __construct ($truncate=false) {
		$mode = 'a';
		if ($truncate) {
			$mode = 'w';
		}
		$this->fh = fopen(INCLUDES_PATH . '/../contributors/@chronological.txt', $mode);
	}
	
	public function __destruct () {
		fclose($this->fh);
	}
	
	public function addUser ($userid) {
		if ( !($gooduserid = intval($userid)) ) {
			die('Trying to add invalid user id "'.htmlentities($userid, null, 'UTF-8').'" to chronological list of contributors');
		}
		return fwrite($this->fh, $gooduserid.PHP_EOL) !== false;
	}
	
}

?>
