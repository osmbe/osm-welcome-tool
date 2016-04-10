<?php

require_once('paths.php');
require_once(INCLUDES_PATH . '/page.php');
require_once(INCLUDES_PATH . '/action_log.php');

register_style('css/log.css');
page_start('Importing', 'import.php');
?>

	<article>
		
		<?php
		
		if (isset($_POST['csv']) && $_POST['csv']) {
			action_log('did an import');
			
require_once(INCLUDES_PATH . '/files/import.php');
			
			echo '<section class="log"><div>';
			import($_POST['csv']);
			echo '</div></section>';
			
		} else {
			action_log('attempted to do an import but sent no data');
			echo '<section><p>No data received.</p><p><a href="import.php" onclick="history.go(-1);return false">Go back</a></p></section>';
		}
		
		?>
		
	</article>

<?php
page_end();
?>