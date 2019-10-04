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

class UserListWriter {

	private $fh;
	
	public function __construct ($truncate=false) {
		$mode = 'a';
		if ($truncate) {
			$mode = 'w';
		}
		$this->fh = fopen(INCLUDES_PATH . '/../contributors/@chronological.txt', $mode);
	}
	
	public function __destruct () {
		fclose($this->fh);
	}
	
	public function addUser ($userid) {
		if ( !($gooduserid = intval($userid)) ) {
			die('Trying to add invalid user id "'.htmlentities($userid, null, 'UTF-8').'" to chronological list of contributors');
		}
		return fwrite($this->fh, $gooduserid.PHP_EOL) !== false;
	}
	
}

?>
