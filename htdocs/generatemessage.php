<?php

include_once('paths.php');
include_once(INCLUDES_PATH . '/page.php');
include_once(INCLUDES_PATH . '/messages/generate_message.php');


if (!isset($_GET['userid']) ||
	!( $userid = intval($_GET['userid']) )
) {
	error_page(400, 'No (valid) user id');
}
if (!isset($_GET['type'])) {
	error_page(400, 'No message type');
}
$type = $_GET['type'];

$language = null;
if (isset($_GET['l'])) {
	$language = $_GET['l'];
}


register_style('css/message.css');
page_start('Generate message', 'contributor.php?userid='.$userid);
?>

	<article>
		<section id="message">
			<?php generate_message($type, $userid, $language); ?>
		</section>
	</article>
	
	<script type="text/javascript">
		if (window.addEventListener && document.getElementById("copyablemessage").focus && document.getElementById("copyablemessage").select) {
			(function () {
				document.getElementById("copy-message").innerHTML="Just hit Ctrl+C or Cmd+C to copy this message and open the message page! <span class='deemphasize'>(You may have to allow this site to show pop-ups)</span>";
				
				function down (event) {
					if (event.keyCode === 17 || event.keyCode === 91) { // Ctrl key or Meta key resp.
						document.getElementById("copyablemessage").focus();
						document.getElementById("copyablemessage").select();
					}
				};
				function up (event) {
					if (event.keyCode === 17 || event.keyCode === 91) { // Ctrl key or Meta key resp.
						document.getElementById("copyablemessage").blur();
					} else if ( (event.keyCode === 67||event.key === "C") && (event.ctrlKey||event.metaKey) ) {
						window.open(messageUrl);
					}
				};
				window.addEventListener("keydown", down, false);
				window.addEventListener("keyup", up, false);
				
			})();
		}
	</script>

<?php
page_end();
?>