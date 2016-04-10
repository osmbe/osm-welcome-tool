<?php

include_once(INCLUDES_PATH . '/files/read_contributor_info.php');
include_once(INCLUDES_PATH . '/api/fetch_changesets.php');
include_once(INCLUDES_PATH . '/api/detect_editors.php');


function markdown_link ($text, $url) {
	return '[' . $text . '](' . $url . ')';
}
/*function markdown_link ($text, $url) {
	return '<span class="invisible">[</span>' .
		'<a href="'.$url.'">' . $text . '</a>' .
		'<span class="invisible">](' . $url . ')</span>';
}*/

function generate_message ($type, $userid, $language=null) {

	$info = read_contributor_info($userid);
	
	$display_name = $info->display_name;
	if (!$display_name) echo 'Could not create message: username not found';

	echo '<p id="copy-message"></p>
	<form action="registercorrespondence.php" method="post">
		<input type="hidden" name="userid" value="'.$userid.'"></input>
		<input type="hidden" name="type" value="'.$type.'"></input>
		<textarea name="messagetext" id="copyablemessage">';

	switch ($type) {
		
		case 'welcome' :
			
			ob_start();
			$editor = detect_primary_editor($display_name);
			ob_end_clean();
			
			$welcome = array();
			$welcome_bottom = array();
			
			include(INCLUDES_PATH . '/messages/welcome.php');
			
			if (!is_null($language)) {
				$info->language = $language;
			}
			
			if (isset($info->language)) {
				if ($info->language === 'Dutch') {
					include(INCLUDES_PATH . '/messages/welcome-nl.php');
					
				} elseif ($info->language === 'French') {
					include(INCLUDES_PATH . '/messages/welcome-fr.php');
					
				} elseif ($info->language === 'English') {
					include(INCLUDES_PATH . '/messages/welcome-en.php');
					
				} elseif ($info->language === 'German') {
					include(INCLUDES_PATH . '/messages/welcome-de.php');
					
				} else {
					include(INCLUDES_PATH . '/messages/welcome-en.php');
					include(INCLUDES_PATH . '/messages/welcome-nl.php');
					include(INCLUDES_PATH . '/messages/welcome-fr.php');
				}
			} else {
				include(INCLUDES_PATH . '/messages/welcome-en.php');
				include(INCLUDES_PATH . '/messages/welcome-nl.php');
				include(INCLUDES_PATH . '/messages/welcome-fr.php');
			}
			
			$multiple_langs = false;
			if (count($welcome) > 1) $multiple_langs = true;
			
			if ($multiple_langs) {
				foreach ($welcome as $messages) {
					echo '**'.$messages['language_name'].'**: *'.$messages['multiple_langs'].'*'.PHP_EOL.PHP_EOL;
				}
			}
			
			foreach ($welcome as $lang=>$messages) {
			
				if ($multiple_langs) {
					echo '# '.$messages['language_name'].PHP_EOL.PHP_EOL;
				}
					
				echo sprintf($messages['hi'], $display_name).PHP_EOL.PHP_EOL;
				
				echo $messages['bravo'].PHP_EOL.PHP_EOL;
				echo $messages['reality'].PHP_EOL.PHP_EOL;
				echo $messages['questions'].PHP_EOL.PHP_EOL;
				echo $messages['helpintro'].PHP_EOL.PHP_EOL;
				echo '* '.$messages['info_wiki'].' ';
				switch ($editor) {
					case 'iD' :      echo $messages['info_iD'];      break;
					case 'Potlatch' : echo $messages['info_Potlatch']; break;
					case 'JOSM' :    echo $messages['info_JOSM'];    break;
					default:         echo $messages['info_other'];   break;
				}
				echo ' '.$messages['info_solution'].PHP_EOL;
				echo '* '.$messages['news'].PHP_EOL;
				echo '* '.$messages['resultmaps'].PHP_EOL;
				echo '* '.$messages['weeklyosm'].PHP_EOL.PHP_EOL;
				echo $messages['endingsentence'].PHP_EOL.PHP_EOL;
				echo $_SESSION['displayname'].PHP_EOL.PHP_EOL;
				echo $messages['osm-be'].PHP_EOL.PHP_EOL;
				
				if (!$multiple_langs) {
					foreach ($welcome_bottom as $messages) {
						echo '*'.sprintf($messages['single_lang'], $messages[$lang]).' '.$messages['if_wrong_sorry'].'*'.PHP_EOL.PHP_EOL;
//						echo '*'.sprintf($messages['single_lang'], $messages[$lang]).' '.$messages['tell_us_pref_lang'].'*'.PHP_EOL.PHP_EOL;
					}
				}
				
/*				if ($multiple_langs) {
					echo '<h3><span class="invisible"># </span>'.$language_names[$lang].'</h3>';
				}
					
				echo '<p>'.sprintf($messages['hi'], $display_name).'</p>';
				
				echo $messages['bravo'].'</p>';
				echo '<p>'.$messages['reality'].'</p>';
				echo '<p>'.$messages['questions'].'</p>';
				echo '<p>'.$messages['helpintro'].'</p>';
				echo '<p class="ul">';
					echo '<div class="li"><span class="invisible">* </span>'.$messages['info_wiki'].' ';
					switch ($editor) {
						case 'iD' :      echo $messages['info_iD'];      break;
						case 'Potlach' : echo $messages['info_Potlach']; break;
						case 'JOSM' :    echo $messages['info_JOSM'];    break;
						default:         echo $messages['info_other'];   break;
					}
					echo ' '.$messages['info_solution'].'</div>';
					echo '<div class="li"><span class="invisible">* </span>'.$messages['news'].'</div>';
					echo '<div class="li"><span class="invisible">* </span>'.$messages['resultmaps'].'</div>';
					echo '<div class="li"><span class="invisible">* </span>'.$messages['weeklyosm'].'</div>';
				echo '</p>';
				echo '<p>'.$messages['endingsentence'].'</p>';
				echo '<p>'.$_SESSION['displayname'].'</p>';
				echo '<p>'.$messages['osm-be'].'</p>';
				
				echo '<p>&nbsp;</p>';
				
				if ($multiple_langs) {
					echo '<p>'.$messages['multiple_langs'].'</p>';
				} else {
					foreach ($welcome_bottom as $messages) {
						echo '<p>'.sprintf($messages['single_lang'], $messages[$lang]).' '.$messages['tell_us_pref_lang'].'</p>';
					}
				}*/
					
			}
		
	}
	
	echo '</textarea>
		<p>Go to <a href="https://www.openstreetmap.org/message/new/'.rawurlencode($display_name).'">osm.org</a>, send this message and then click the button below.</p>
		<p><input type="submit" value="I have sent this message"></input></p>
	</form>
	';
	
	echo '<script type="text/javascript">
	var messageUrl = "https://www.openstreetmap.org/message/new/'.rawurlencode($display_name).'";
</script>';
	
}

?>