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

include_once(INCLUDES_PATH . '/files/read_contributor_info.php');
include_once(INCLUDES_PATH . '/files/read_user_info.php');
include_once(INCLUDES_PATH . '/files/get_all_welcomers.php');
include_once(INCLUDES_PATH . '/format_duration.php');
include_once(INCLUDES_PATH . '/parse_note.php');

// TODO Read and save edits
// TODO Make welcomed_by, responded, note, did_what editable

define('EDIT_LANGUAGE',1);
define('EDIT_WELCOMED',1<<1);
define('EDIT_RESPONDED',1<<2);
define('EDIT_NEWNOTE',1<<3);
define('EDIT_NEWDIDWHAT',1<<4);

$editing = 0;
if (isset($_GET['edit'])) {
	$editing = $_GET['edit'];
}
function edit_link ($item_to_edit, $userid) {
	echo ' <a href="?userid=';
	echo $userid;
	echo '&amp;edit=';
	echo $item_to_edit|$GLOBALS['editing'];
	echo '" class="edit">Edit</a>';
}
function edit_button ($item_to_edit, $userid, $text) {
	echo '<a href="?userid=';
	echo $userid;
	echo '&amp;edit=';
	echo $item_to_edit|$GLOBALS['editing'];
	echo '" class="button">';
	echo $text;
	echo '</a>';
}

$languages = array('Dutch', 'French', 'German', 'English');
function language_selector ($current=null) {

	if (is_null($current)) {
		$current = 'unknown';
		$other = false;
	} else $other = true;
	
	echo '<ul>';
	
	foreach ($GLOBALS['languages'] as $language) {
		echo '<li><input type="radio" name="lang" id="lang-'.$language.'" value="'.$language.'" ';
		if ($language === $current) {
			echo ' checked="checked"';
			$other = false;
		}
		echo '></input><label for="lang-'.$language.'">'.$language.'</label></li>';
	}
	
	// Other language
	echo '<li><input type="radio" name="lang" id="lang-ot" value="other"';
		if ($other) echo ' checked="checked"';
	echo '></input><label for="lang-ot">other: ';
	echo '<input type="text" name="otherlang" onfocus="document.getElementById(\'lang-ot\').checked=true;" tabindex="-1" value="';
	if ($other) echo htmlentities($current, null, 'UTF-8');
	echo '"></input></li></label>';
	
	echo '<li><input type="radio" name="lang" id="lang-unknown" value="unknown" ';
	if ('unknown' === $current) {
		echo ' checked="checked"';
	}
	echo '></input><label for="lang-unknown">unknown</label></li>';
	echo '</ul>';
}

function print_contributor_file ($userid, $info=null) {

	if ( !($gooduserid = intval($userid)) ) {
		die('PF: Invalid user id "'.htmlentities($userid, null, 'UTF-8').'"');
	}

	if (is_null($info)) {
		$info = read_contributor_info($userid);
	}
	
	if (!$info) {
		echo '<section>No user with this ID found in local database.</section>';
	} else {

		
		$editing = $GLOBALS['editing'];
		
	
		echo '
			
			<div class="grid2">
			
			<header>
				<h3>';
				if (isset($info->img) && $info->img) {
					echo '<img src="img/userpic.php?user=';
					echo $userid;
					echo '" alt="" />';
				} else {
					echo '<img src="img/userpic.php?user=0" alt="" />';
				}
				echo '<a href="https://www.openstreetmap.org/user/';
				echo htmlentities($info->display_name, null, 'UTF-8');
				echo '" target="_blank">';
				echo htmlentities($info->display_name, null, 'UTF-8');
				echo '</a></h3>
				<div class="userdescription">';
				if (isset($info->description)) echo nl2br(htmlentities($info->description, null, 'UTF-8'));
				echo '</div>
			</header>
			
			</div>
			
			<div class="grid2">
			
			<section class="meta links">
				<a class="button" href="updatecontributor.php?userid=';
				echo $userid;
				echo '">Update this file</a>
			</section>
			
			</div>
			
			<div class="grid2">
			
			<section class="accountstats tablecontainer">
				
				<div>
					<table class="vertical">';

		// Account created
		$account_created = $info->account_created;
		
		echo '
						<tr class="o"><th>account made</th><td class="ac"><time datetime="';
		echo date('c', $account_created);
		echo '">';
		
		echo format_duration(time() - $account_created);
		echo ' ago <span class="deemphasize">(';
		
		echo date('Y-m-d H:i:s', $account_created);
		
		echo ')</span></time></td></tr>
		
						<tr class="e"><th>first edit</th><td class="fe">';
						
		// First edit
		if (isset($info->first_edit)) {
			$firstedit = $info->first_edit;
			echo '<time datetime="';
			echo date('c', $firstedit);
			echo '">';
			
			echo format_duration($firstedit - $account_created);
			echo ' later <span class="deemphasize">(';
			
			echo date('Y-m-d H:i:s', $firstedit);
			
			echo ')</span></time>';
		} else {
			echo 'unknown';
		}
			
		echo '</td></tr>
		
						<tr class="o"><th>number of changesets</th><td class="cs">';
		
		// Number of changesets
		if (isset($info->changesets)) {
			echo '<a href="https://www.openstreetmap.org/user/';
			echo rawurlencode($info->display_name);
			echo '/history" target="_blank">';
			echo $info->changesets;
			echo '</a>';
		}
		
		echo '</td></tr>
		
						<tr class="e"><th>language</th>';
		
		// Language
		
		if (EDIT_LANGUAGE & $editing) {
		
			echo '<td class="ln editing"><form method="post" action="?userid='.$userid.'&edit='.($editing & ~EDIT_LANGUAGE).'">';
			echo '<input type="hidden" name="userid" value='.$userid.' />';
			
			language_selector(@$info->language);
			echo '<input type="submit" value="Save"></input> <a href="?userid='.$userid.'&edit='.($editing & ~EDIT_LANGUAGE).'" class="button">Cancel</a></form>';
		
		} else {

			echo '<td class="ln editable">';
		
			if (!isset($info->language) || is_null($info->language)) {
				echo 'unknown';
			} else {
				switch ($info->language) {
					case 'Dutch' : case 'French' : case 'English' : case 'German' :
						echo $info->language;
						break;
					default :
						echo 'other: ';
						echo htmlentities($info->language, null, 'UTF-8');
						break;
				}
			}
			
			edit_link(EDIT_LANGUAGE, $userid);
			
		}
		
		echo '</td>
						</tr>
					</table>
				</div>
			</section>

			<section class="welcoming">
	';

		if (EDIT_WELCOMED & $editing) {
			$welcomed = isset($info->welcomed_by) && !is_null($info->welcomed_by);
			
			echo '<form method="post"><input type="hidden" name="userid" value='.$userid.' />';
			
			echo '<label for="welcomed_by">Welcomed by </label><select id="welcomed_by" name="welcomed_by">';
			
			echo '<option value="-1"';
			if (!$welcomed) {
				echo ' selected="selected"';
			}
			echo '>no-one yet</option>';
			
			foreach (get_all_welcomers() as $welcomer) {
				$user_info = read_user_info($welcomer);
				
				echo '<option value="'.$welcomer.'" ';
				if ($welcomed && $info->welcomed_by == $welcomer) {
					echo ' selected="selected"';
				}
				echo '>';
				if ($user_info) {
					echo read_user_info($welcomer)->display_name;
				} else {
					echo 'User #';
					echo $welcomer;
				}
				echo '</option>';
			}
			
			echo '</select><br/>';
			echo '<input type="checkbox" name="responded" id="responded"/><label for="responded"> Responded</label>';
			echo '<div><input type="submit" value="Save"></input> <a href="?userid='.$userid.'&edit='.($editing & ~EDIT_WELCOMED).'" class="button">Cancel</a></div>';
			echo '</form>';
			
		} else {
			// Welcomed by
			if (!isset($info->welcomed_by) || is_null($info->welcomed_by)) {
				echo '<div class="welcomed negative editable">not yet been welcomed <a href="generatemessage.php?userid=';
				echo $userid;
				echo '&amp;type=welcome" class="fill button">Welcome now</a> ';
				edit_link(EDIT_WELCOMED, $userid);
				echo '</div>';
			} else {
				echo '<div class="welcomed positive editable">has been welcomed';
				if ($info->welcomed_by !== true) {
					echo ' by ';
					
					if (is_int($info->welcomed_by)) {
						echo read_user_info($info->welcomed_by)->display_name;
					} else {
						echo $info->welcomed_by;
					}
				}
				edit_link(EDIT_WELCOMED, $userid);
				echo '</div>';
				
				// Responded
				echo (isset($info->responded)&&$info->responded)?'<div class="responded positive editable">has responded ':'<div class="responded negative editable">not yet responded ';
				echo '</div>';
			}
		}
		
		// Links
		echo '
			</section>
			
			<section class="links">
				<a class="button" href="https://www.openstreetmap.org/message/new/';
				echo rawurlencode($info->display_name);
				echo '" target="_blank">Send message</a>
				<a class="button" href="http://hdyc.neis-one.org/?';
				echo rawurlencode($info->display_name);
				echo '" target="_blank">How did they contribute?</a>
			</section>';
			if (isset($info->welcomed_by) && !is_null($info->welcomed_by)) {
				echo '
				<section class="correspondence">
					<h4>Correspondence</h4>
					Generate message: ';
						echo '<a class="small button" href="generatemessage.php?userid=';
						echo $userid;
						echo '&amp;type=welcome">welcome</a> ';
					/*
					echo '<a class="small button" href="generatemessage.php?userid=';
					echo $userid;
					echo '&amp;type=vandalism">vandalism</a>*/
					echo '
				</section>';
			}
			
			echo '
			</div>
			
			<div class="grid2">
			
			<section class="notes">';
		
		// Note
		echo '<h4>Notes</h4>';
		if (isset($info->note)) {
			echo parse_note($info->note, 'note');
		}
		if (EDIT_NEWNOTE & $editing) {
			echo '<form method="post">';
			echo '<input type="hidden" name="userid" value='.$userid.' />';
			echo '<textarea name="newnote"></textarea>';
			echo '<div><input type="submit" value="Save"></input> <a href="?userid='.$userid.'&edit='.($editing & ~EDIT_NEWNOTE).'" class="button">Cancel</a></div>';
			echo '</form>';
		} else {
			echo '<div>';
			edit_button(EDIT_NEWNOTE, $userid, 'Add note');
			echo '</div>';
		}
		
		// Did what
		echo '<h4>Did what</h4>';
		if (isset($info->did_what)) {
			echo parse_note($info->did_what, 'didwhat');
		}
		if (EDIT_NEWDIDWHAT & $editing) {
			echo '<form method="post">';
			echo '<input type="hidden" name="userid" value='.$userid.' />';
			echo '<textarea name="newdidwhat"></textarea>';
			echo '<div><input type="submit" value="Save"></input> <a href="?userid='.$userid.'&edit='.($editing & ~EDIT_NEWDIDWHAT).'" class="button">Cancel</a></div>';
			echo '</form>';
		} else {
			echo '<div>';
			edit_button(EDIT_NEWDIDWHAT, $userid, 'Add description');
			echo '</div>';
		}
		
		// Editor info
		if (isset($info->editors) && $info->editors) {
			$rownumber = 0;

			echo '<h4>Changesets by editor</h4>';
			
			$changeset_table = '			
			<table class="editors vertical">';
			
			$totalcount = 0;
			foreach ($info->editors as $editor=>$count) {
				$changeset_table .= '<tr class="';
				// Bitwise AND for super-fast modulus 2
				if (((++$rownumber)&1) === 0) {
					$changeset_table .= 'e';
				} else {
					$changeset_table .= 'o';
				}
				$changeset_table .= '"><th>'
					. $editor
					. '</th>'
					. '<td>'.$count.'</td>';
				
				$totalcount += $count;
			}
			
			if (isset($info->changesets)) {
				$changeset_table .= '<tr class="total"><th>Total</th><td>'.$info->changesets.'</td>';
			}
			
			$changeset_table .= '</table>';
			
			if ($totalcount == $info->changesets) {
				echo $changeset_table;
				
			} else {
				echo '<div class="grid2">';
				echo $changeset_table;
				echo '</div><div class="grid2">
					<span class="deemphasize">This information may be out of date, because it is generated only weekly by neis-one, and only gets pulled here when you update the file individually.</span>
				</div>';
			}
			
		}
		
		echo '</section>';
		
	}
	
	echo '
	</div>

';
}

?>
