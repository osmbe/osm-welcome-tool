<?php

require_once('paths.php');
require_once(INCLUDES_PATH.'/page.php');
require_once(INCLUDES_PATH.'/action_log.php');

register_style('css/import.css');
page_start('Import from Google Sheet');
?>

	<article>
		<section>
		
			<p>Paste the CSV here.</p>
			
			<form action="doimport.php" method="POST">
				
				<textarea name="csv"></textarea>
				
				<p><input type="submit" value="Import"></input> (may take a long time, depending on the number of entries)</p>
				
			</form>
		
		</section>
	</article>

<?php
page_end();
?>