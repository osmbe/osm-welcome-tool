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
require_once(INCLUDES_PATH . '/page.php');
require_once(INCLUDES_PATH . '/action_log.php');

register_style('css/log.css');
page_start('Importing', 'import.php');
?>

	<article>
		
		<?php
		
		if (isset($_POST['csv']) && $_POST['csv']) {
			action_log('did an import');
			
require_once(INCLUDES_PATH . '/files/import.php');
			
			echo '<section class="log"><div>';
			import($_POST['csv']);
			echo '</div></section>';
			
		} else {
			action_log('attempted to do an import but sent no data');
			echo '<section><p>No data received.</p><p><a href="import.php" onclick="history.go(-1);return false">Go back</a></p></section>';
		}
		
		?>
		
	</article>

<?php
page_end();
?>