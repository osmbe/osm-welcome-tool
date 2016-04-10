<?php

include_once(INCLUDES_PATH . '/files/get_contributor_file_path.php');
include_once(INCLUDES_PATH . '/api/fetch_contributor_info.php');

function edit_contributor_file ($data) {
	if (!isset($data['userid'])) {
		return false;
	}
	$unsafeuserid = $data['userid'];
	if ( !($userid = intval($unsafeuserid)) ) {
		die('UI: Invalid user id "'.htmlentities($unsafeuserid, null, 'UTF-8').'"');
	}
	
	$file_path = get_contributor_file_path($userid);
	if (!file_exists($file_path) || !($info = json_decode(file_get_contents($file_path), true))) {
		return false;
	}

	if (isset($data['lang'])) {
		$info['language'] = $data['lang'];
	}
	
	if (isset($data['welcomed_by'])) {
		$welcomed_by = intval($data['welcomed_by']);
		if (!$welcomed_by) {
			return false;
		}
		if ($welcomed_by == -1) {
			$info['welcomed_by'] = null;
		} else {
			$info['welcomed_by'] = $welcomed_by;
		}
		
		$info['responded'] = ((isset($data['responded']) && $data['responded']==='on')?true:false);
	}
	
	if (isset($data['newnote'])) {
		$note = array(
			userid => $_SESSION['userid'],
			display_name => $_SESSION['displayname'],
			content => $data['newnote'],
			timestamp => time()
		);
		
		if (!isset($info['note'])) {
			$info['note'] = array($note);
		} elseif (is_array($info['note'])) {
			array_push($info['note'], $note);
		} else {
			$info['note'] = array($info['note'], $note);
		}
	}
	
	if (isset($data['newdidwhat'])) {
		$didwhat = array(
			userid => $_SESSION['userid'],
			display_name => $_SESSION['displayname'],
			content => $data['newdidwhat'],
			timestamp => time()
		);
		
		if (!isset($info['did_what'])) {
			$info['did_what'] = array($didwhat);
		} elseif (is_array($info['did_what'])) {
			array_push($info['did_what'], $didwhat);
		} else {
			$info['did_what'] = array($info['did_what'], $didwhat);
		}
	}
	
	$file_contents = json_encode($info);
	if ($file_contents) {
		if (file_put_contents($file_path, $file_contents)) {
			return true;
		}
	}
	return false;
}

?>
