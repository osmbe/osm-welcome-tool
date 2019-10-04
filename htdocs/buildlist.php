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
require_once('paths.php');
require_once(INCLUDES_PATH.'/page.php');
require_once(INCLUDES_PATH.'/files/build_chronological_list.php');
require_once(INCLUDES_PATH.'/action_log.php');

register_style('css/log.css');
page_start('Rebuilding list of contributors', 'index.php');

?>

	<article>
		
		<section id="logcontainer" class="log">
			<div id="log"><?php build_chronological_list(); ?></div>
		</section>
		
	</article>

<?php
page_end();
?>