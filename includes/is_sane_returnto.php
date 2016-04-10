<?php

function is_sane_returnto ($returnto) {
	return preg_match(';^/[a-z].*$;i', $returnto) === 1;
}

?>