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

function request_feed () {
	$rssUrl = 'http://resultmaps.neis-one.org/newestosmcountryfeed?c=Belgium';
	$userAgent = 'osmwelcome.unitedbashers.com, contact at ruben@janmaes.com';


	// Create a stream
	$opts = array(
		'http' => array(
			'method' => 'GET',
			'user_agent' => $userAgent
		)
	);
	$context = stream_context_create($opts);

	// Open the file using the HTTP headers set above
	return file_get_contents($rssUrl, false, $context);
}

?>