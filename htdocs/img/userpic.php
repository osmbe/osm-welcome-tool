<?php
require_once('../paths.php');

function error_img ($message) {
	header ('Content-Type: image/jpeg');
	$im = @imagecreatetruecolor(256, 256) or
		die($message);
	$text_color = imagecolorallocate($im, 255, 0, 0);
	imagestring($im, 4, 10, 140, $message, $text_color);
	imagejpeg($im);
	imagedestroy($im);
	exit;
}

function send_img ($filename) {
	header('Content-Type: image/jpeg');
	header('Content-Transfer-Encoding: binary');
	header('Expires: '.date('r',time()+60*60*24)); // Cache for 1 day
	header('Cache-Control: public');
	header('Content-Length: '.filesize($filename));
	readfile($filename);
	exit;
}

function create_thumb ($full, $thumb, $mime, $new_dimensions, $orig_dimensions, $quality) {
	// Resample
	$image_p = imagecreatetruecolor($new_dimensions[0], $new_dimensions[1]);
	switch ($mime) {
		case 'image/jpg': case 'image/jpeg':
			$image = imagecreatefromjpeg($full);
			break;
			
		case 'image/png':
			$white = imagecolorallocate($image_p, 255, 255, 255);
			imagefill($image_p, 0, 0, $white);
			
			$image = imagecreatefrompng($full);
			break;
			
		default:
			error_img('Invalid mime: '.$mime);
			break;
	}
	
	imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_dimensions[0], $new_dimensions[1], $orig_dimensions[0], $orig_dimensions[1]);
	
	
	// Create directory
	$dir = substr($thumb,0,16);
	if (!file_exists($dir)) {
		mkdir($dir, 0777);
		$fhindex = fopen($dir.'index.html','a');
		fclose($fhindex);
	}
	
	imagejpeg($image_p, $thumb, $quality);
	
	imagedestroy($image_p);
}

if (!isset($_GET['user'])) {
	header('HTTP/1.1 404 Not Found');
	error_img('No user given');
}

$id = intval($_GET['user']);


$size = 256;
if (@$_GET['size'] === '32') {
	$size = 32;
}

if ($id === 0) {
	if ($size === 32) {
		send_img(INCLUDES_PATH . '/../userpics/silhouette-32.jpg');
	} else {
		send_img(INCLUDES_PATH . '/../userpics/silhouette-256.jpg');
	}
}


require_once(INCLUDES_PATH . '/files/read_contributor_info.php');
require_once(INCLUDES_PATH . '/files/read_user_info.php');

$info = read_contributor_info($id);
if (!$info) {
	$info = read_user_info($id);
	if (!$info) {
		header('HTTP/1.1 404 Not Found');
		error_img('No file for that user found');
	}
}

if (!@$info->img) {
	error_img('No image associated with account');
}
if (!preg_match(';^(http://www.gravatar.com/avatar/|http://api.openstreetmap.org/attachments/users/images/);', $info->img) === 1) {
	error_img('');
}



$orig = $info->img;
$full = INCLUDES_PATH . '/../userpics/'.$id.'-full.'; // Extension added later
$thumb256 = INCLUDES_PATH . '/../userpics/'.$id.'-256.jpg';
$thumb32 = INCLUDES_PATH . '/../userpics/'.$id.'-32.jpg';

$uptodate = true;
$lastupdated = @file_get_contents(INCLUDES_PATH . '/../userpics/'.$id.'-last-updated.txt');
if (($lastupdated-time()) > 60*60*24) { // If the thumbnail is older than 1 day, make a new thumb
	$uptodate = false;
}

if ($uptodate && $size === 32 && file_exists($thumb32)) {
	send_img($thumb32);
	
} if ($uptodate && file_exists($thumb256)) {
	send_img($thumb256);

} else {

	$headers = get_headers($orig, 1);

	if ($headers[0] === 'HTTP/1.1 301 Moved Permanently' || $headers[0] === 'HTTP/1.0 302 Found') {
		$headermime = $headers['Content-Type'][1];
	} else {
		$headermime = $headers['Content-Type'];
	}
	
	$mimes = array('image/jpg' => 'jpg', 'image/jpeg' => 'jpg', 'image/png' => 'png');

	if (!isset($mimes[$headermime])) {
		error_img('Invalid mime in response header: '.$headermime);
	}
	
	$full .= $mimes[$headermime];
	if (!copy($orig, $full)) {
		error_img('Couldn\'t fetch image');
	}
	
	
	$orig_dimensions = getimagesize($full);
	
	
	// Get mime type
	$finfo = finfo_open(FILEINFO_MIME);
	if ($finfo) {
		$mime = explode(';', finfo_file($finfo, $full));
		if (count($mime)>2) error_img('File corrupted');
		$mime = $mime[0];

		finfo_close($finfo);
	} else error_img('Opening fileinfo database failed');
	
	if ($headermime !== $mime) {
		error_img('Mime sent by remote server does not equal real mime of file');
	}
	
	
	create_thumb($full, $thumb256, $mime, array(256, 256), $orig_dimensions, 70);
	create_thumb($full, $thumb32, $mime, array(32, 32), $orig_dimensions, 90);
	
	$lastupdated = file_put_contents(INCLUDES_PATH . '/../userpics/'.$id.'-last-updated.txt', time());
	
	// Delete full size image
	unlink($full);
	
	if ($size === 32) {
		send_img($thumb32);
	} else {
		send_img($thumb256);
	}
	
}


?>