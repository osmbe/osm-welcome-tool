<?php

require_once(INCLUDES_PATH . '/api/fetch_changesets.php');


function detect_language ($userid) {
	
	$changesets = fetch_changesets_by_user_id($userid); // This function has a cache, so we don't query the same from the API twice
	
	foreach ($changesets->changeset as $changeset) {
		$changeset_meta = call_api('changeset/'.$changeset['id'], array('include_discussion'=>false), 'GET');
		
		if (preg_match(';<tag k="locale" v="(.*?)"/>;i', $changeset_meta, $matches) === 1) {
			return $matches[1];
		}
	}

	return 'unknown';
}