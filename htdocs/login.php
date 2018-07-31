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
require_once(INCLUDES_PATH . '/page.php');
require_once(INCLUDES_PATH . '/is_sane_returnto.php');

if (isset($_SESSION['userid']) && isset($_SESSION['displayname'])) {
	if (isset($_GET['returnto']) && is_sane_returnto($_GET['returnto'])) {
		header('Location: '.$_GET['returnto']);
	} else {
		header('Location: index.php');
	}
}


page_start('Log in');
?>

<article>
	<section>
		<?php
			if (@$_GET['loggedout'] === 'true') {
				echo '<div class="action-success">You have been logged out from this service. Note: you have <strong>not been logged out from <a href="https://www.openstreetmap.org">openstreetmap.org</a></strong> itself, we can\'t do that for you.</div>';
			}
		?>
		
		<p>Welcome to the <i>New OSM Contributors in Belgium</i> Panel. You need to log in with your OSM account to use this service.</p>
		<p>Don't worry, we're using OAuth so you don't have to trust us with your password.</p>
		<p>Click the button below to proceed. You will be sent to openstreetmap.org where you can log in if you're not already logged in over there. Next, you will have to grant us permission to use your account.</p>
		<p><a href="dologin.php<?php
if (isset($_GET['returnto']) && is_sane_returnto($_GET['returnto'])) {
	echo '?returnto=';
	echo rawurlencode($_GET['returnto']);
}
		?>" class="large button">Log in with OSM</a></p>
	</section>
</article>

<?
page_end();
?>
