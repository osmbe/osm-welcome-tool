<?php


if (!isset($_GET['userid'])) {
	die('No user id given');
}
if (!is_numeric($_GET['userid'])) {
	die('Invalid user id');
}
$userid = $_GET['userid'];

include_once('paths.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	include_once(INCLUDES_PATH.'/files/edit_contributor_file.php');
	$editsuccess = edit_contributor_file($_POST);
	
	header('Location: ?userid='.urlencode($userid).'&editsuccess='.($editsuccess?'1':'0'));
	die('<a href="?userid='.urlencode($userid).'&editsuccess='.($editsuccess?'1':'0').'">Continue</a>');
}

include_once(INCLUDES_PATH.'/page.php');
include_once(INCLUDES_PATH.'/files/print_contributor_file.php');

register_style('css/contributor.css');

$info = read_contributor_info($userid);
page_start('Contributor details', 'index.php', $info->display_name);
?>

	<article class="contributorfile">
		<?php
			if (isset($_GET['editsuccess'])) {
				if ($_GET['editsuccess'] === '1') {
					echo '<section class="action-success">Edited <a href="?userid='.urlencode($userid).'" class="button">Dismiss</a></section>';
				} else {
					echo '<section class="action-failure">Edit failed <a href="?userid='.urlencode($userid).'" class="button">Dismiss</a></section>';
				}
			} elseif (isset($_GET['updated'])) {
				echo '<section class="action-success">Information updated <a href="?userid='.urlencode($userid).'" class="button">Dismiss</a></section>';
			}
			
			echo print_contributor_file($userid, $info);
		?>
	</article>

<?php
page_end();
?>