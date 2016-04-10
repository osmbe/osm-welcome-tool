<?php

function parse_note ($notes, $class="note", $show_author=true) {
	if (is_null($class)) $class="note";

	$result = '<ul class="'.$class.'">';
	
	if (is_array($notes)) {

		foreach ($notes as $note) {
			$result .= '<li>';
			if ($show_author && $note->userid) {
				$result .= '<div class="author"><a href="https://www.openstreetmap.org/user/'.rawurlencode($note->display_name).'" target="_blank">';
				$result .= '<img src="img/userpic.php?size=32&amp;user='.rawurlencode($note->userid).'" alt="" /> ';
				$result .= htmlentities($note->display_name, null, 'UTF-8');
				$result .= '</a></div>';
			}
			
			$result .= '<div class="content">';
			$result .= htmlentities($note->content, null, 'UTF-8');
			$result .= '</div>';
			
			$result .= '</li>';
		}
		

	} else {
		$result .= '<li>';
		$result .= htmlentities($notes, null, 'UTF-8');
		$result .= '</li>';
	}
	
	$result .= '</ul>';

	return $result;
}

?>