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

$authorization_required = false;
require_once('paths.php');
require_once(INCLUDES_PATH . '/oauth/oauth.php');
require_once(INCLUDES_PATH . '/is_sane_returnto.php');


if (isset($_GET['returnto']) && is_sane_returnto($_GET['returnto'])) {
	$_SESSION['returnto'] = $_GET['returnto'];
}



// Base url of this server
$domain = $_SERVER['HTTP_HOST'];
$protocol = (
	(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
	$_SERVER['SERVER_PORT'] == 443
) ? "https" : "http";
$base_url = "$protocol://$domain" . APP_HTTP_PATH;


/*** Request token ******************************************/

$endpoint = 'https://www.openstreetmap.org/oauth/request_token';

$request_token_info = $oauth->getRequestToken($endpoint);
$_SESSION['request_secret'] = $request_token_info['oauth_token_secret'];
$request_key = $request_token_info['oauth_token'];


/*** Have token authorized: redirect user *******************/

$endpoint = 'https://www.openstreetmap.org/oauth/authorize';

if (!$request_key) {
	die('<p>Could not generate request key. Try again. If this error persists, contact this service\'s administrator.</p>');
}

$callback_url = "${base_url}oauthcallback.php";
$auth_url = $endpoint . "?oauth_token=$request_key&oauth_callback=".urlencode($callback_url);

header("Location: $auth_url");
die("<p>You should be redirected to <a href='$auth_url'>$auth_url</a>.</p>");

?>
