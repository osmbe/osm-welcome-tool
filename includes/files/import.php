<?php

require_once(INCLUDES_PATH . '/files/get_contributor_file_path.php');
require_once(INCLUDES_PATH . '/files/build_chronological_list.php');
require_once(INCLUDES_PATH . '/files/update_contributor_info.php');
require_once(INCLUDES_PATH . '/api/fetch_contributor_info.php');
require_once(INCLUDES_PATH . '/api/detect_first_edit.php');
require_once(INCLUDES_PATH . '/api/detect_userid.php');
require_once(INCLUDES_PATH . '/api/detect_editors.php');

function detected_field ($name, &$info) {
	echo '  importing '.$name.': ';
	if ($name !== 'first_edit') {
		echo @$info[$name];
	} else {
		echo date('r',@$info[$name]);
	}
	
	echo PHP_EOL;
}

function import ($csv) {
	echo 'Splitting up CSV by line ... ';
	
	$lines = explode(PHP_EOL, $csv);
	
	echo 'Done'.PHP_EOL;
	
	$firstlines = true;
	
	foreach ($lines as $i=>$line) {

		if ($firstlines) {
			if (preg_match('/^#?,/', $line) === 1) {
				echo 'Line '.$i.', nothing to see here'.PHP_EOL;
				continue;
			} else {
				$firstlines = false;
				echo 'First content on line '.$i.PHP_EOL;
			}
			
		}
		
		if ($line==='' || preg_match('/^ ,/', $line)) {
			echo PHP_EOL.'Encountered empty line: end of useful info'.PHP_EOL;
			break;
		}
		
		echo PHP_EOL.'Extracting CSV info from line '.$i.PHP_EOL;
		
		$line = str_getcsv($line);
		
		echo 'Building info array'.PHP_EOL;
		
		$info = array();
		
		// 0 1                2        3         4    5             6        7                8        9    10        11             12
		// #,date of 1st edit,username,user link,hdyc,welcome sent?,by whom?,received answer?,language,note,did what?,send a message,view history
		
		// Display name
		$info['display_name'] = $display_name = $line[2];
		
		// User id
		echo 'Detecting user id ... ';
		$userid = detect_userid($display_name);
		if (!$userid) {
			echo '<span class="fail">FAILED</span> User does not exist?'.PHP_EOL;
			continue;
		}
		echo 'Done: '.$userid.PHP_EOL;
		
		// Other info
		
		$first_edit = detect_first_edit($display_name);
		if (!$first_edit) {
			$first_edit = strtotime(
				str_replace(
					' at', '',
					$line[1]
				)
			);
		}
		
		if (!$first_edit) {
			echo '<span class="fail">FAILED</span> Unable to determine date of first edit'.PHP_EOL;
			continue;
		}
		
		$info['first_edit'] = $first_edit;
		detected_field('first_edit', $info);
		
		$welcomed_by = $line[6];
		if ($welcomed_by) {
			switch ($welcomed_by) {
				case 'joost' :
					$info['welcomed_by'] = 67832;
					break;
				
				case 'ruben' :
					$info['welcomed_by'] = 763799;
					break;
				
				case 'jo' :
					$info['welcomed_by'] = 15188;
					break;
					
				case 'no' :
					$info['welcomed_by'] = null;
					break;
				
				default :
					$info['welcomed_by'] = $welcomed_by;
					break;
			}
		}
		detected_field('welcomed_by', $info);
		
		$responded = $line[7];
		if ($responded === 'yes') {
			$info['responded'] = true;
		}
		detected_field('responded', $info);
		
		$language = $line[8];
		if ($language && preg_match('/possibl|probabl|maybe|unknown|\?/i',$language) !== 1) {
			$info['language'] = $language;
		}
		detected_field('language', $info);
		
		$note = $line[9];
		if ($note) {
			$info['note'] = $note;
		}
		detected_field('note', $info);
		
		$did_what = $line[10];
		if ($did_what) {
			$info['did_what'] = $did_what;
		}
		detected_field('did_what', $info);
		
		// Writing
		echo 'Writing file for '.$userid.' ('.$display_name.')'.PHP_EOL.PHP_EOL;
		update_contributor_info($userid, null, DETECT_EDITORS, $info);
	}
	
	build_chronological_list();
	
	echo PHP_EOL.'Import complete.';
}

?>
