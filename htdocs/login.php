<?php

$authorization_required = false;
require_once('paths.php');
require_once(INCLUDES_PATH . '/page.php');
require_once(INCLUDES_PATH . '/is_sane_returnto.php');

if (isset($_SESSION['userid']) && isset($_SESSION['displayname'])) {
	if (isset($_GET['returnto']) && is_sane_returnto($_GET['returnto'])) {
		header('Location: '.$_GET['returnto']);
	} else {
		header('Location: index.php');
	}
}


page_start('Log in');
?>

<article>
	<section>
		<?php
			if (@$_GET['loggedout'] === 'true') {
				echo '<div class="action-success">You have been logged out from this service. Note: you have <strong>not been logged out from <a href="https://www.openstreetmap.org">openstreetmap.org</a></strong> itself, we can\'t do that for you.</div>';
			}
		?>
		
		<p>Welcome to the <i>New OSM Contributors in Belgium</i> Panel. You need to log in with your OSM account to use this service.</p>
		<p>Don't worry, we're using OAuth so you don't have to trust us with your password.</p>
		<p>Click the button below to proceed. You will be sent to openstreetmap.org where you can log in if you're not already logged in over there. Next, you will have to grant us permission to use your account.</p>
		<p><a href="dologin.php<?php
if (isset($_GET['returnto']) && is_sane_returnto($_GET['returnto'])) {
	echo '?returnto=';
	echo rawurlencode($_GET['returnto']);
}
		?>" class="large button">Log in with OSM</a></p>
	</section>
</article>

<?
page_end();
?>
