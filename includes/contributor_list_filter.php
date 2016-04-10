<?php

define('FILTER_WELCOMED', 1);
define('FILTER_NOT_WELCOMED', 1<<1);
define('FILTER_RESPONDED', 1<<2);
define('FILTER_NOT_RESPONDED', 1<<3);
define('FILTER_LANG_DUTCH', 1<<4);
define('FILTER_LANG_FRENCH', 1<<5);
define('FILTER_LANG_GERMAN', 1<<6);
define('FILTER_LANG_ENGLISH', 1<<7);
define('FILTER_LANG_OTHER', 1<<8);
define('FILTER_LANG_UNKNOWN', 1<<9);
define('FILTER_ANY_LANGUAGE', ((1<<10) -1) ^ ((1<<4) -1));
define('FILTER_SHOW_20', 1<<10);
define('FILTER_SHOW_100', 1<<11);
define('FILTER_SHOW_ALL', 1<<12);

include_once(INCLUDES_PATH.'/Filter.php');

$filter = new Filter(
	array(
		array('mode'=>Filter::MODE_EXCLUDING, FILTER_WELCOMED=>'welcomed', FILTER_NOT_WELCOMED=>'not welcomed'),
		array('mode'=>Filter::MODE_EXCLUDING, FILTER_RESPONDED=>'responded', FILTER_NOT_RESPONDED=>'not responded'),
		array(
			'mode'=>Filter::MODE_COMBINING,
			'name'=>'language',
			FILTER_LANG_DUTCH=>'Dutch',
			FILTER_LANG_FRENCH=>'French',
			FILTER_LANG_GERMAN=>'German',
			FILTER_LANG_ENGLISH=>'English',
			FILTER_LANG_OTHER=>array('other', 'other language'),
			FILTER_LANG_UNKNOWN=>array('unknown', 'unknown language')
		),
		array('mode'=>Filter::MODE_EXCLUDING, 'default'=>FILTER_SHOW_20, FILTER_SHOW_20=>'show 20', FILTER_SHOW_100=>'show 100', FILTER_SHOW_ALL=>'show all')
	)
);

function print_filter_controls () {
	get_filter_object()->printControls();
}

function get_filter_object () {
	return $GLOBALS['filter'];
}

?>