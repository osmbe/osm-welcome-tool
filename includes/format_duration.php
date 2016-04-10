<?php

define('FORMAT_DURATION_AUTODETECT', 0);
define('FORMAT_DURATION_SECONDS', 1);
define('FORMAT_DURATION_MINUTES', 2);
define('FORMAT_DURATION_HOURS', 3);
define('FORMAT_DURATION_DAYS', 4);
define('FORMAT_DURATION_WEEKS', 5);
define('FORMAT_DURATION_MONTHS', 6);
define('FORMAT_DURATION_YEARS', 7);

function format_duration ($seconds, $unit=FORMAT_DURATION_AUTODETECT) {
	
	if (!$unit) {
		if ($seconds < 60) {
			$unit = FORMAT_DURATION_SECONDS;
			
		} elseif ($seconds < 60*60) {
			$unit = FORMAT_DURATION_MINUTES;
			
		} elseif ($seconds < 60*60*24) {
			$unit = FORMAT_DURATION_HOURS;
			
		} elseif ($seconds < 60*60*24*7) {
			$unit = FORMAT_DURATION_DAYS;
			
		} elseif ($seconds < 60*60*24*31) {
			$unit = FORMAT_DURATION_WEEKS;
			
		} elseif ($seconds < 60*60*24*365) {
			$unit = FORMAT_DURATION_MONTHS;
			
		} else {
			$unit = FORMAT_DURATION_YEARS;
		}
	}
	
	switch ($unit) {
			
		case FORMAT_DURATION_MINUTES :
			$minutes = round($seconds/60);
			if ($minutes === 1.0) return '1 minute';
			return $minutes.' minutes';
			
		case FORMAT_DURATION_HOURS :
			$hours = round($seconds/60/60);
			if ($hours === 1.0) return '1 hour';
			return $hours.' hours';
			
		case FORMAT_DURATION_DAYS :
			$days = round($seconds/60/60/24);
			if ($days === 1.0) return '1 day';
			return $days.' days';
			
		case FORMAT_DURATION_WEEKS :
			$weeks = round($seconds/60/60/24/7);
			if ($weeks === 1.0) return '1 week';
			return $weeks.' weeks';
			
		case FORMAT_DURATION_MONTHS :
			$months = round($seconds/60/60/24/31);
			if ($months === 1.0) return '1 month';
			return $months.' months';
			
		case FORMAT_DURATION_YEARS :
			$years = round($seconds/60/60/24/365);
			if ($years === 1.0) return '1 year';
			return $years.' years';
			
		case FORMAT_DURATION_SECONDS :
		default:
			$seconds = round($seconds);
			if ($seconds === 1.0) return '1 second';
			return $seconds.' seconds';
	}
}

?>