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

define('FILTER_WELCOMED', 1);
define('FILTER_NOT_WELCOMED', 1<<1);
define('FILTER_RESPONDED', 1<<2);
define('FILTER_NOT_RESPONDED', 1<<3);
define('FILTER_LANG_DUTCH', 1<<4);
define('FILTER_LANG_FRENCH', 1<<5);
define('FILTER_LANG_GERMAN', 1<<6);
define('FILTER_LANG_ENGLISH', 1<<7);
define('FILTER_LANG_OTHER', 1<<8);
define('FILTER_LANG_UNKNOWN', 1<<9);
define('FILTER_ANY_LANGUAGE', ((1<<10) -1) ^ ((1<<4) -1));
define('FILTER_SHOW_20', 1<<10);
define('FILTER_SHOW_100', 1<<11);
define('FILTER_SHOW_ALL', 1<<12);

include_once(INCLUDES_PATH.'/Filter.php');

$filter = new Filter(
	array(
		array('mode'=>Filter::MODE_EXCLUDING, FILTER_WELCOMED=>'welcomed', FILTER_NOT_WELCOMED=>'not welcomed'),
		array('mode'=>Filter::MODE_EXCLUDING, FILTER_RESPONDED=>'responded', FILTER_NOT_RESPONDED=>'not responded'),
		array(
			'mode'=>Filter::MODE_COMBINING,
			'name'=>'language',
			FILTER_LANG_DUTCH=>'Dutch',
			FILTER_LANG_FRENCH=>'French',
			FILTER_LANG_GERMAN=>'German',
			FILTER_LANG_ENGLISH=>'English',
			FILTER_LANG_OTHER=>array('other', 'other language'),
			FILTER_LANG_UNKNOWN=>array('unknown', 'unknown language')
		),
		array('mode'=>Filter::MODE_EXCLUDING, 'default'=>FILTER_SHOW_20, FILTER_SHOW_20=>'show 20', FILTER_SHOW_100=>'show 100', FILTER_SHOW_ALL=>'show all')
	)
);

function print_filter_controls () {
	get_filter_object()->printControls();
}

function get_filter_object () {
	return $GLOBALS['filter'];
}

?>