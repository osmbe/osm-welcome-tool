<?php

define('STATUS_OK',1);
define('STATUS_OK_ALREADY_HAVE_FILE',1<<1);
define('STATUS_OK_FILE_WAS_BAD',1<<2);
define('STATUS_OK_FILE_EXISTED',1<<3);
define('STATUS_OK_FILE_CREATED',1<<4);
define('STATUS_ALL_OKS',(1<<5)-1);

define('STATUS_INVALID_API_RESPONSE',1<<12);
define('STATUS_COULD_NOT_WRITE_FILE',1<<13);
define('STATUS_COULD_NOT_ADD_TO_LIST',1<<14);
define('STATUS_COULD_NOT_FETCH_INFO',1<<15);
define('STATUS_COULD_NOT_DETECT_USERID',1<<16);
define('STATUS_INVALID_USERID',1<<17);
define('STATUS_NO_FIRST_EDIT',1<<18);
define('STATUS_ALL_FAILS', ((1<<19)-1) ^ ((1<<12)-1) );

define('STATUS_ALL', STATUS_ALL_OKS|STATUS_ALL_FAILS);

class Action {
	
	const NOT_STARTED = 0;
	const RUNNING = 1;
	const OK = 2;
	const FAIL = 4;
	
	const COMPLETE = 6;
	
	private $start_time;
	private $time;
	
	private $name;
	
	private $log;
	
	private $status = self::NOT_STARTED;
	
	public function __construct ($name) {
		$this->name = $name;
	}
	
	public function start_action () {
		$this->status = static::RUNNING;
		$this->start_time = microtime(true);
		return true;
	}
	
	public function end_action ($success) {
		$this->time = microtime(true) - $this->start_time;
		if ($success) $this->status = static::OK;
		else $this->status = static::FAIL;
		return true;
	}
	
	public function action_log ($columns=80, $time=true) {
		if ( !($this->status&static::COMPLETE) ) return false;
		
		return '* ' . str_pad($this->name, $columns-7) . static::status_text($this->status) . PHP_EOL .
			"    Time: " . number_format($this->time, 5, '.', '') . 's' .
			PHP_EOL.PHP_EOL;
	}
	
	
	public function log_something ($logtext, $symbol='*') {
		echo '      ' . $symbol . ' ' . $logtext.PHP_EOL;
	}
	
	public function log_userfile_status ($status, $userid, $displayname=null) {
		if (!is_null($displayname)) {
			$userid .= ' ('.$displayname.')';
		}
		
		$success = true;
		if ($status & STATUS_OK) {
			$this->log_something($userid.' OK. No more information on the status, weird');
		}
		if ($status & STATUS_OK_ALREADY_HAVE_FILE) {
			$this->log_something('Already had file for '.$userid);
		}
		if ($status & STATUS_OK_FILE_CREATED) {
			$this->log_something('Created '.$userid);
		}
		if ($status & STATUS_OK_FILE_EXISTED) {
			$this->log_something('Updated '.$userid);
		}
		if ($status & STATUS_OK_FILE_WAS_BAD) {
			$this->log_something('Updated '.$userid.'. It was corrupted before');
		}
		if ($status & STATUS_INVALID_API_RESPONSE) {
			$this->log_something('Invalid API response for '.$userid, '!');
			$success = false;
		}
		if ($status & STATUS_COULD_NOT_WRITE_FILE) {
			$this->log_something('Could not write file for for '.$userid, '!');
			$success = false;
		}
		if ($status & STATUS_COULD_NOT_ADD_TO_LIST) {
			$this->log_something('Could not add '.$userid.' to list', '!');
			$success = false;
		}
		if ($status & STATUS_COULD_NOT_FETCH_INFO) {
			$this->log_something('Could not fetch contributor info for '.$userid, '!');
			$success = false;
		}
		if ($status & STATUS_COULD_NOT_DETECT_USERID) {
			$this->log_something('Could not detect user id for '.$displayname, '!');
			$success = false;
		}
		if ($status & STATUS_INVALID_USERID) {
			$this->log_something('Invalid user id '.$userid, '!');
			$success = false;
		}
		if ($status & STATUS_NO_FIRST_EDIT) {
			$this->log_something('No first edit for '.$userid, '!');
			$success = false;
		}
		if (! ($status & STATUS_ALL) ) {
			$this->log_something('Other status while creating file for '.$userid.': '.$status, '!');
			$success = false;
		}
		return $success;
	}
	
	
	private static function status_text ($status) {
		switch ($status) {
			case static::OK   : return '[ OK ]'; break;
			case static::FAIL : return '[FAIL]'; break;
		}
	}
	
}

$log = '';

function start_action ($name) {
	$action = new Action($name);
	$action->start_action();
	return $action;
}

function end_action ($action, $success = true) {
	$action->end_action($success);
	echo $action->action_log().PHP_EOL;
}

?>