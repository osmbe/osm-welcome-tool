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

$neis_api_url = 'http://hdyc.neis-one.org/';
$neis_user_agent = 'osmwelcome.unitedbashers.com, contact at ruben@janmaes.com';

function call_neis_api ($action, $params=null, $method='GET') {

	echo 'Calling Neis API '.$action.PHP_EOL;
	
	$endpoint = $GLOBALS['neis_api_url'] . $action;
	
	$opts = array(
		'http' => array(
			'method' => $method,
			'user_agent' => $GLOBALS['neis_user_agent']
		)
	);
	$context = stream_context_create($opts);

	$result = file_get_contents($endpoint, false, $context);
	
	return $result;
}

?>