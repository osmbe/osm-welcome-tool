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

@session_start();

function get_user_auth () {
	if (!isset($_SESSION['access_key'])) {
		return false;
	}
	if (!isset($_SESSION['access_secret'])) {
		return false;
	}
	$access_key = $_SESSION['access_key'];
	$access_secret = $_SESSION['access_secret'];
	
	return [$access_key, $access_secret];
}

?>
