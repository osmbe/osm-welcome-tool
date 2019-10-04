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

require_once(INCLUDES_PATH . '/api/fetch_changesets.php');


function detect_language ($userid) {
	
	$changesets = fetch_changesets_by_user_id($userid); // This function has a cache, so we don't query the same from the API twice
	
	foreach ($changesets->changeset as $changeset) {
		$changeset_meta = call_api('changeset/'.$changeset['id'], array('include_discussion'=>false), 'GET', NO_AUTH);
		
		if (preg_match(';<tag k="locale" v="(.*?)"/>;i', $changeset_meta, $matches) === 1) {
			return $matches[1];
		}
	}

	return 'unknown';
}
