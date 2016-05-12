<?php

function extract_display_name_from_rss_entry ($entry, $ac) {
	$user_url = (string) ($entry->id);
	if (!preg_match(';openstreetmap.org/user/(.*);', $user_url, $matches)) {
		$ac->log_something('Could not recognize display name in url "'.$user_url.'"');
		$success = false;
	}
	return $matches[1];
}

function extract_firstedit_from_rss_entry ($rss_entry) {
	return strtotime($rss_entry->updated);
}

?>
