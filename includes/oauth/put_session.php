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

function put_session ($userid, $displayname, $image, $access_key, $access_secret) {
	$_SESSION['userid'] = $userid;
	$_SESSION['displayname'] = $displayname;
	$_SESSION['image'] = $image;
	$_SESSION['access_key'] = $access_key;
	$_SESSION['access_secret'] = $access_secret;
	
	
	file_put_contents(INCLUDES_PATH . '/../users/' . rawurlencode($userid) . '.json', json_encode(
		array(
			'display_name' => $displayname,
			'img' => $image,
			'last_logged_in_here' => time()
		)
	));
	
	return true;
}

?>