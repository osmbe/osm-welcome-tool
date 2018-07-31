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

require_once('./paths.php');
require_once(INCLUDES_PATH.'/api/detect_userid.php');

echo '<form>Display name: <input name="display_name"></input><input type="submit" value="Lookup userid"></input></form> ';

if (isset($_GET['display_name']) && $_GET['display_name']) {
	$userid = detect_userid($_GET['display_name']);
	echo '<br/>';
	
	if (!$userid) {
		echo 'user not found';
	} else {
		echo $userid;
	}
}

?>