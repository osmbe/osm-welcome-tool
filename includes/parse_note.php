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

function parse_note ($notes, $class="note", $show_author=true) {
	if (is_null($class)) $class="note";

	$result = '<ul class="'.$class.'">';
	
	if (is_array($notes)) {

		foreach ($notes as $note) {
			$result .= '<li>';
			if ($show_author && $note->userid) {
				$result .= '<div class="author"><a href="https://www.openstreetmap.org/user/'.rawurlencode($note->display_name).'" target="_blank">';
				$result .= '<img src="img/userpic.php?size=32&amp;user='.rawurlencode($note->userid).'" alt="" /> ';
				$result .= htmlentities($note->display_name, null, 'UTF-8');
				$result .= '</a></div>';
			}
			
			$result .= '<div class="content">';
			$result .= htmlentities($note->content, null, 'UTF-8');
			$result .= '</div>';
			
			$result .= '</li>';
		}
		

	} else {
		$result .= '<li>';
		$result .= htmlentities($notes, null, 'UTF-8');
		$result .= '</li>';
	}
	
	$result .= '</ul>';

	return $result;
}

?>