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
include_once(INCLUDES_PATH . '/page.php');
include_once(INCLUDES_PATH . '/messages/generate_message.php');


if (!isset($_GET['userid']) ||
	!( $userid = intval($_GET['userid']) )
) {
	error_page(400, 'No (valid) user id');
}
if (!isset($_GET['type'])) {
	error_page(400, 'No message type');
}
$type = $_GET['type'];

$language = null;
if (isset($_GET['l'])) {
	$language = $_GET['l'];
}


register_style('css/message.css');
page_start('Generate message', 'contributor.php?userid='.$userid);
?>

	<article>
		<section id="message">
			<?php generate_message($type, $userid, $language); ?>
		</section>
	</article>
	
	<script type="text/javascript">
		if (window.addEventListener && document.getElementById("copyablemessage").focus && document.getElementById("copyablemessage").select) {
			(function () {
				document.getElementById("copy-message").innerHTML="Just hit Ctrl+C or Cmd+C to copy this message and open the message page! <span class='deemphasize'>(You may have to allow this site to show pop-ups)</span>";
				
				function down (event) {
					if (event.keyCode === 17 || event.keyCode === 91) { // Ctrl key or Meta key resp.
						document.getElementById("copyablemessage").focus();
						document.getElementById("copyablemessage").select();
					}
				};
				function up (event) {
					if (event.keyCode === 17 || event.keyCode === 91) { // Ctrl key or Meta key resp.
						document.getElementById("copyablemessage").blur();
					} else if ( (event.keyCode === 67||event.key === "C") && (event.ctrlKey||event.metaKey) ) {
						window.open(messageUrl);
					}
				};
				window.addEventListener("keydown", down, false);
				window.addEventListener("keyup", up, false);
				
			})();
		}
	</script>

<?php
page_end();
?>