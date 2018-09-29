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

include_once(INCLUDES_PATH . '/files/chronological_contributor_list.php');
include_once(INCLUDES_PATH . '/files/read_contributor_info.php');
include_once(INCLUDES_PATH . '/files/read_user_info.php');
include_once(INCLUDES_PATH . '/parse_note.php');

function user_link ($userid, $content, $focusable=false) {
	echo '<a href="contributor.php?userid=';
	echo $userid;
	echo '"';
	if (!$focusable) echo ' tabindex="-1"';
	echo '>';
	echo $content;
	echo '</a>';
}

function pagination_links($current_page, $maxlen, $filter, $class="") {
	$current_page ++;

	$url = $filter->getUrl() . '&page=';
	echo "
				<nav class='pagination_links $class'>
";
	if (!is_null($maxlen)) {
		if ($current_page > 1) {
			echo "					<a href='${url}".($current_page-1)."'>◀</a>".PHP_EOL;
		} else {
			echo "					<a class='disabled'>◀</a>".PHP_EOL;
		}
		echo "					Page $current_page".PHP_EOL;
		echo "					<a href='${url}".($current_page+1)."'>▶</a>".PHP_EOL;
	}
	echo '
				</nav>
';
}

function print_list_of_contributors () {
	$filter = get_filter_object();

	$maxlen = null;
	if ($filter->isEnabled(FILTER_SHOW_20)) $maxlen = 20;
	elseif ($filter->isEnabled(FILTER_SHOW_100)) $maxlen = 100;

	$users = chronological_contributor_list();

	$current_page = isset($_GET["page"]) ? (int)($_GET["page"]) - 1 : 0;
	if ($current_page < 0) $current_page = 0;

	$num_users = count($users);

	if (!is_null($maxlen)) {
		$skip = $current_page * $maxlen;
	}

	pagination_links($current_page, $maxlen, $filter, 'top');
	echo '
				<table class="fullwidth">
<tr class="heading">
	<th></th>
	<th class="dn">user</th>
	<th class="cs"><span class="info" title="number of changesets">#CSs</span></th>
	<th class="ac">signed up</th>
	<th class="we">welcomed?</th>
	<th class="ln">lang</th>
	<th class="nt">notes</th>
	<th class="dw">did what</th>
</tr>
';

	$firsteditday = '';
	$rownumber = 0;
	foreach ($users as $userid) {

		if (!$userid) continue;

		$userid = intval($userid);
		if (!$userid) {
			die('Invalid user id in list');
		}

		$info = read_contributor_info($userid);

		/*** Filters **************************************************************/

		$welcomed = isset($info->welcomed_by) && !is_null($info->welcomed_by);
		if ($filter->isEnabled(FILTER_WELCOMED) && !$welcomed) continue;
		if ($filter->isEnabled(FILTER_NOT_WELCOMED) && $welcomed) continue;

		$responded = isset($info->responded) && !is_null($info->responded);
		if ($filter->isEnabled(FILTER_RESPONDED) && !$responded) continue;
		if ($filter->isEnabled(FILTER_NOT_RESPONDED) && $responded) continue;

		$lang = ' ';
		if (!isset($info->language) || is_null($info->language)) {
			$lang = '?';
			if ($filter->isEnabled(FILTER_ANY_LANGUAGE) && !$filter->isEnabled(FILTER_LANG_UNKNOWN)) continue;
		} else {
			switch ($info->language) {
				case 'Dutch' :
					if ($filter->isEnabled(FILTER_ANY_LANGUAGE) && !$filter->isEnabled(FILTER_LANG_DUTCH)) continue 2;
					$lang = 'NL';
					break;
				case 'French' :
					if ($filter->isEnabled(FILTER_ANY_LANGUAGE) && !$filter->isEnabled(FILTER_LANG_FRENCH)) continue 2;
					$lang = 'FR';
					break;
				case 'English' :
					if ($filter->isEnabled(FILTER_ANY_LANGUAGE) && !$filter->isEnabled(FILTER_LANG_ENGLISH)) continue 2;
					$lang = 'EN';
					break;
				case 'German' :
					if ($filter->isEnabled(FILTER_ANY_LANGUAGE) && !$filter->isEnabled(FILTER_LANG_GERMAN)) continue 2;
					$lang = 'DE';
					break;
				default :
					if ($filter->isEnabled(FILTER_ANY_LANGUAGE) && !$filter->isEnabled(FILTER_LANG_OTHER)) continue 2;
					$lang = 'o';
					break;
			}
		}

		if ($skip-- > 0) {
			continue;
		}

		/**************************************************************************/

		if (isset($info->first_edit)) {
			$thisuser_firsteditday = date('j F Y (l)', $info->first_edit);

			if ($thisuser_firsteditday !== $firsteditday) {
				echo '<tr class="firsteditheader"><td colspan="8"><h3>';
				echo $thisuser_firsteditday;
				echo '</h3></td></tr>
';

				$firsteditday = $thisuser_firsteditday;
			}

		}

		echo '<tr class="';
		// Bitwise AND for super-fast modulus 2
		if (((++$rownumber)&1) === 0) {
			echo 'e';
		} else {
			echo 'o';
		}
		echo '">';
		
		// User picture
		echo '<td class="up">';
		if (isset($info->img) && $info->img) {
			user_link($userid, '<img src="img/userpic.php?size=32&amp;user='.$userid.'" alt="" />');
		} else {
			user_link($userid, '<img src="avatars/silhouette-32.jpg" alt="" />');
		}
		echo '</td>';
		
		// Display name
		echo '<td class="dn">';
		user_link($userid, htmlentities($info->display_name, null, 'UTF-8'), true);
		echo '</td>';
		
		// Changesets
		echo '<td class="cs">';
		$changesets = ' ';
		
		if (isset($info->changesets)) {
			$changesets = $info->changesets;
		}
		
		user_link($userid, $changesets);
		echo '</td>';
		
		// Account created
		echo '<td class="ac">';
		$accountcreated = ' ';
		
		if (isset($info->account_created)) {
			$accountcreated = '<time datetime="'.date('c', @$info->account_created).'">' .
				date('Y-m', @$info->account_created) .
				'</time>';
		}
		
		user_link($userid, $accountcreated);
		echo '</td>';
		
		// Welcomed by
		echo '<td class="we">';
		$welcomedby = ' ';
		
		if (!$welcomed) {
			$welcomedby = 'no';
		} else {
			$welcomedby = 'yes';
			if ($info->welcomed_by !== true) {
				
				if (is_int($info->welcomed_by)) {
					$welcomer = read_user_info($info->welcomed_by)->display_name;
				} else {
					$welcomer = $info->welcomed_by;
				}
				
				$welcomedby .= ', by '.$welcomer;
			}
		}
		
		user_link($userid, $welcomedby);
		echo '</td>';
		
		// Language
		echo '<td class="ln">';
		user_link($userid, $lang);
		echo '</td>';
		
		// Note
		echo '<td class="nt">';
		$note = ' ';
		
		if (isset($info->note)) {
			$note = parse_note($info->note, null, false);
		}
		
		user_link($userid, $note);
		echo '</td>';
		
		// Did what
		echo '<td class="dw">';
		$didwhat = ' ';
		
		if (isset($info->did_what)) {
			$didwhat = parse_note($info->did_what, null, false);
		}
		
		user_link($userid, $didwhat);
		echo '</td></tr>
';

		if ($maxlen !== 0 && $rownumber > $maxlen) break;
	}
	unset($ulr);
	
	echo '
				</table>
';
	pagination_links($current_page, $maxlen, $filter, 'bottom');
}

?>
