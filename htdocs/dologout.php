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

$authorization_required = false;
require_once('paths.php');

if (!$_SESSION['userid']) {
	header('Location: index.php');
	die('Not logged in');
}


require_once(INCLUDES_PATH . '/do_logout.php');
do_logout();


require_once(INCLUDES_PATH . '/is_sane_returnto.php');

$returnto = '';
if ($_GET['returnto'] && is_sane_returnto($_GET['returnto'])) {
	$returnto = rawurlencode($_GET['returnto']);
}

header('Location: login.php?loggedout=true'.(
	$returnto
	? '&returnto='.$returnto
	: ''
));
die('You have been logged out from this service. Note: you have not been logged out from openstreetmap.org itself.<br/><br/><a href="dologin.php'.(
	$returnto
	? '?returnto='.$returnto
	: ''
).'">Click here to log in again.</a>');


?>