<?php


$styles = '<style type="text/css">@import url("css/main.css");</style>';
function register_style ($file_name) {
	$GLOBALS['styles'] .= PHP_EOL . '	<style>@import url("' . $file_name . '");</style>';
}

$scripts = '';
function register_script ($file_name) {
	$GLOBALS['scripts'] .= PHP_EOL . '	<script type="text/javascript" src="' . $file_name . '"></script>';
}

function page_start ($title, $backto=null, $beforetitle=null) {
	
	
	echo '<!DOCTYPE html>
<html>
  <head>
	<meta charset="utf-8" />
	
	<title>';
	
	if ($beforetitle) {
		echo $beforetitle;
		echo ' – ';
	}
	
	echo $title;
	echo ' – New OSM Contributors in Belgium</title>
	
	';
	echo $GLOBALS['styles'];
	echo '
	
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	
	<link rel="shortcut icon" href="img/favicon.ico?v=1" />
	
  </head>
  
  <body>
  
	<header>
		<h1>New OSM Contributors in Belgium</h1>
		
		<nav>';
			
	if (isset($_SESSION['userid']) && isset($_SESSION['displayname'])) {
		echo '
			<ul>
				<li><a href="index.php">List</a></li>
				<li><a href="info.php">Help on welcoming</a></li>
				<!--<li><a href="stats.php">Statistics</a></li>-->
			</ul>
			<div class="account">
				<img src="img/userpic.php?size=32&amp;user=';
			echo $_SESSION['userid'];
			echo '" alt="">';
				
			echo $_SESSION['displayname'];
			echo ' <a href="dologout.php?returnto=';
			echo rawurlencode($_SERVER['REQUEST_URI']);
			echo '" class="logout">Log out</a>
			</div>';
	}
	echo '
		</nav>
		
		<h2>';
		$link = false;
		if (!is_null($backto)) {
			$link = true;
			echo '<a href="'.$backto.'">';
		}
		echo $title;
		if ($link) echo '</a>';
		echo '</h2>
		
	</header>
	
	<div class="articlewrapper">
';
	
}

function page_end () {
	echo '
	</div>
	
	<footer>
		<p>An <a href="http://osm.be/">OpenStreetMap Belgium</a> project. <a href="https://framagit.org/Midgard/osm-welcome-belgium/">Source code</a> available. Powered by <a href="http://resultmaps.neis-one.org/newestosm?c=Belgium">neis-one.org</a></p>
	</footer>
	
	';
	echo $GLOBALS['scripts'];
	echo '
  </body>
</html>';
}

function error_page ($status, $message) {
	switch ($status) {
		case 400 : $status .= ' Bad Request';  break;
		case 401 : $status .= ' Unauthorized'; break;
		case 404 : $status .= ' Not Found';    break;
		default  : return false;
	}
	header('HTTP/1.1 '.$status);
	
	page_start($status);
	echo '<article><section><p>That\'s an HTTP error.</p><p>Reason: ';
	echo $message;
	echo '</p><p><a href="https://www.openstreetmap.org/message/new/M!dgard">Contact M!dgard</a> about this.</p></section></article>';
	page_end();
	
	die();
}
