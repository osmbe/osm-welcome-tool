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

include_once('paths.php');
include_once(INCLUDES_PATH . '/files/update_contributor_info.php');

if (!isset($_GET['userid'])) {
	die('No user id!');
}
if ( !($userid = intval($_GET['userid'])) ) {
	die('Invalid user id "'.htmlentities($_GET['userid'], null, 'UTF-8').'"');
}

update_contributor_info($userid, null, DETECT_EDITORS);

header('Location: contributor.php?updated&userid='.$userid);

?>