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
session_regenerate_id(true);
require_once(INCLUDES_PATH . '/oauth/oauth.php');
require_once(INCLUDES_PATH . '/is_sane_returnto.php');





if(!isset($_GET['oauth_token'])) {
	die('An error occurred in the OAuth process. <a href="index.php">Click here to return to the main page.</a><br/><br/>Error: There is no OAuth token!');
}
if(!isset($_SESSION['request_secret'])) {
	die('An error occurred in the OAuth process. <a href="index.php">Click here to return to the main page.</a><br/><br/>Error: There is no OAuth secret!');
}

$request_key = $_GET['oauth_token'];
$request_secret = $_SESSION['request_secret'];

unset($_SESSION['request_secret']);


try {
	$oauth->setToken($request_key, $request_secret);

	/*** Exchange request token for access token ****************/

	$endpoint = 'https://www.openstreetmap.org/oauth/access_token';
	$access_token_info = $oauth->getAccessToken($endpoint);

	$access_key = $access_token_info["oauth_token"];
	$access_secret = $access_token_info["oauth_token_secret"];

	$oauth->setToken($access_key, $access_secret);

} catch(OAuthException $e) {
	die('Could not complete login procedure. <a href="index.php">Click here to return to the main page.</a>');
}


/*** Get user details and save authentication ***************/

require_once(INCLUDES_PATH . '/api/api.php');
require_once(INCLUDES_PATH . '/oauth/put_session.php');


$response = call_api('user/details', null, 'GET', [$access_key, $access_secret]);

if (!$response) {
	die('An error occurred while querying the API. <a href="index.php">Click here to return to the main page.</a><br/><br/>Error: No valid response');
}

$user_info = simplexml_load_string($response);
$displayname = (string) $user_info->user['display_name'];
$userid = (string) $user_info->user['id'];
$image = (string) $user_info->user->img['href'];

if (!$userid) {
	die('An error occurred while querying the API. <a href="index.php">Click here to return to the main page.</a><br/><br/>Error: No user id in response');
}


$success = put_session($userid, $displayname, $image, $access_key, $access_secret);


if ($success && $_SESSION['userid']) {
	$returnto = 'index.php';
	if (isset($_SESSION['returnto']) && is_sane_returnto($_SESSION['returnto'])) {
		$returnto = $_SESSION['returnto'];
	}
	die('<noscript><meta http-equiv="refresh" content="0; url='.$returnto.'"></noscript><script type="text/javascript">window.location="'.$returnto.'";</script>Authentication successful. <a href="'.$returnto.'">Click here to return to the page you were on.</a>');
} else {
	die('Failed to store authentication. <a href="index.php">Click here to return to the main page.</a>');
}


?>
