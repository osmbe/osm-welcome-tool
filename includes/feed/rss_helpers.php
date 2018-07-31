<?php
/* This file is part of osm-welcome: a platform to coordinate welcoming of OpenStreetMap mappers
 * Copyright © 2018  Midgard and osm-welcome contributors
 *
 * This program is free software: you can redistribute it and/or modify it under the terms of the
 * GNU Affero General Public License as published by the Free Software Foundation, either version 3
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License along with this
 * program. If not, see <https://www.gnu.org/licenses/>.
 */

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
