<?php

require_once(INCLUDES_PATH . '/api/fetch_neis_userinfo.php');


function detect_editors ($display_name) {
	
	$userinfo = fetch_neis_userinfo($display_name); // This function has a cache, so we don't query the same from the API twice
	
	$editors = @$userinfo->changesets->editors;
	
	if (!$editors) return false;
	
	$editors = explode(';', $editors);
	
	$editors_ass_array = array();
	
	foreach ($editors as $editor) {
		$editor_pair = explode('=', $editor);
		$editors_ass_array[$editor_pair[0]] = intval($editor_pair[1]);
	}
	
	return $editors_ass_array;
	
}


function detect_primary_editor ($display_name) {

	$editors = array('iD'=>0,'Potlatch'=>0,'other'=>0,'JOSM'=>0, 'MAPS.ME'=>0);

	$changesets = fetch_changesets_by_display_name($display_name);
	
	$number_of_changesets = count($changesets);

	$weight = 10 + ($number_of_changesets/3);
	$treshold = max( 0, $number_of_changesets/4 - 10 ); // Will stop when weight is below this
	
	foreach ($changesets->changeset as $cs) {
		foreach ($cs->tag as $tag) {
		
			if ($weight < $treshold) break;
		
			if ($tag['k'] == 'created_by') { // Don't do strict comparison, in SimpleXMLObject the attributes are not really strings
				$editor = $tag['v'];
				
				if (preg_match('/iD/i', $editor) === 1) $editor = 'iD';
				elseif (preg_match('/Potlatch/i', $editor) === 1) $editor = 'Potlatch';
				elseif (preg_match('/JOSM/i', $editor) === 1) $editor = 'JOSM';
				elseif (preg_match('/MAPS.ME/i', $editor) === 1) $editor = 'MAPS.ME';
				else $editor = 'other';
				
				$editors[$editor] += ($weight--);
				
				break;
			}
			
		}
	}
	
	$highest_score = 0; $highest_name = 'other';
	foreach ($editors as $name=>$score) {
		if ($score > $highest_score) {
			$highest_score = $score;
			$highest_name = $name;
		}
	}
	
	return $highest_name;
	
}


