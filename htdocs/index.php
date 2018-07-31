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
include_once(INCLUDES_PATH.'/page.php');
include_once(INCLUDES_PATH.'/contributor_list_filter.php');
include_once(INCLUDES_PATH.'/format_duration.php');
include_once(INCLUDES_PATH.'/files/print_list_of_contributors.php');
include_once(INCLUDES_PATH.'/files/get_list_last_updated.php');

register_style('css/list.css');
register_style('css/filter.css');
page_start('List');
?>

	<article>
		<section>
			<p>Welcome to the <i>New OSM Contributors in Belgium</i> Panel. Below you can find a list of the latest new contributors. Click one to open their file.</p>
		</section>
		
		<section class="filtercontrols">
			<?php print_filter_controls(); ?>
		</section>
		
		<section class="tablecontainer">
			<div>
				<?php print_list_of_contributors(); ?>
			</div>
		</section>
		
		<section>
			<p>Last updated: <?php
$lastupdated = time()-get_list_last_updated();
if ($lastupdated < 60) {
	echo 'just now';
} else {
	echo format_duration( time()-get_list_last_updated() );
	echo ' ago';
}
			?></p>
		</section>
	</article>

<?php
page_end();
?>
