<?php
require_once('paths.php');
require_once(INCLUDES_PATH.'/page.php');
require_once(INCLUDES_PATH.'/files/build_chronological_list.php');
require_once(INCLUDES_PATH.'/action_log.php');

register_style('css/log.css');
page_start('Rebuilding list of contributors', 'index.php');

?>

	<article>
		
		<section id="logcontainer" class="log">
			<div id="log"><?php build_chronological_list(); ?></div>
		</section>
		
	</article>

<?php
page_end();
?>