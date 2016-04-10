<?php

include_once(INCLUDES_PATH . '/log.php');
include_once(INCLUDES_PATH . '/files/get_contributor_file_path.php');
include_once(INCLUDES_PATH . '/api/fetch_contributor_info.php');
include_once(INCLUDES_PATH . '/api/detect_editors.php');
include_once(INCLUDES_PATH . '/api/detect_language.php');

$language_codes = array(
	'nl' => 'Dutch',
	'fr' => 'French',
	'de' => 'German'
);
define('DETECT_LANGUAGE', 1);
define('DETECT_EDITORS', 2);

function update_contributor_info ($userid, $firstedit=null, $detect=0, $import=null) {
	if ( !($gooduserid = intval($userid)) ) {
		die('UI: Invalid user id "'.htmlentities($userid, null, 'UTF-8').'"');
	}
	$userid = $gooduserid;

	$apiresponse = fetch_contributor_info($userid);
	if (!$apiresponse) {
		return STATUS_INVALID_API_RESPONSE;
	}
	$status = STATUS_OK;
	
	$file_path = get_contributor_file_path($userid);
	if (file_exists($file_path)) {
		$info = json_decode(file_get_contents($file_path), true);
		if (!$info) {
			$info = array();
			$status = STATUS_OK_FILE_WAS_BAD;
		} else {
			$status = STATUS_OK_FILE_EXISTED;
		}
	} else {
		$status = STATUS_OK_FILE_CREATED;
	}
	
	$displayname = (string) ($apiresponse->user['display_name']);
	$GLOBALS['last_contributor_displayname'] = $displayname;
	
	$info['display_name'] = $displayname;
	$info['description'] = (string) $apiresponse->user->description;
	$info['account_created'] = strtotime(
		str_replace(
			'Z',
			'+02:00',
			(string) ($apiresponse->user['account_created'])
		)
	);
	$info['img'] = (string) ($apiresponse->user->img['href']);
	$info['changesets'] = (string) ($apiresponse->user->changesets['count']);
	$info['blocks'] = intval(
		(string) ($apiresponse->user->blocks->received['count'])
	);
	
	if ($detect & DETECT_EDITORS) {
		// Detect editors
		$info['editors'] = detect_editors($displayname);
	}
	if ($detect & DETECT_LANGUAGE) {
		// Detect language
		$info['language'] = @$GLOBALS['language_codes'][detect_language($userid)];
	}
	
	if (!is_null($firstedit)) {
		if (!is_numeric($firstedit)) return STATUS_NO_FIRST_EDIT;
		$info['first_edit'] = $firstedit;
	}
	
	// Import
	if (!is_null($import)) {
		foreach ($import as $key=>$value) {
		
			if ($key === 'welcomed_by') continue;
			
			if (isset($info[$key]) && $info[$key] != $value) {
				echo 'Key '.htmlentities($key, null, 'UTF-8').' was already present ("'.htmlentities($info[$key], null, 'UTF-8').'"), <span class="warn">overriden</span> with "'.htmlentities($value).'"'.PHP_EOL;
			}
			$info[$key] = $value;
		}
	
		if (isset($import['welcomed_by']) && is_int($import['welcomed_by'])) {
			if (!isset($info['welcomed_by'])) {
				$info['welcomed_by'] = $import['welcomed_by'];
			}
		}
	}
	
	$file_contents = json_encode($info);
	if ($file_contents) {
		if (!file_put_contents($file_path, $file_contents)) {
			return STATUS_COULD_NOT_WRITE_FILE;
		}
	} else {
		return STATUS_COULD_NOT_WRITE_FILE;
	}
	return $status;
}

function get_last_contributor_displayname () {
	return $GLOBALS['last_contributor_displayname'];
}

?>
