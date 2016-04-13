<?php

$verbose=5;

// logging like this makes it readable
// logtrace(3, "Pending updates: " . print_r($updates,true));

$home = dirname(__FILE__) . DIRECTORY_SEPARATOR;

$dirs = array ('users', 'userpics', 'contributors','updatelog');

// Creating dirs (and test)
logtrace(3, "Creating dirs: " . print_r($dirs,true));

foreach ($dirs as $dir) {
   if (!file_exists($dir)) {
      // mkdir ( string $pathname [, int $mode = 0777 [, bool $recursive = false [, resource $context ]]] )
      if(!mkdir ($dir, 0775)) {
         logtrace(0, "Problem creating dir: " . $php_errormsg);
      } else {
         logtrace(3, "Directory created : " . $dir);
      }
   } else {
      logtrace(3, "Directory already exists : " . $dir);
   }
}
function logtrace($level,$msg) {
   global $verbose;
   $DateTime=@date('Y-m-d H:i:s', time());
   if ( $level <= $verbose ) {
      $mylvl=NULL;
      switch($level) {
         case 0:
            $mylvl ="error";
            break;
         case 1:
            $mylvl ="core ";
            break;
         case 2:
            $mylvl ="info ";
            break;
         case 3:
            $mylvl ="notic";
            break;
         case 4:
            $mylvl ="verbs";
            break;
         case 5:
            $mylvl ="dtail";
            break;
         default :
            $mylvl ="exec ";
            break;
      }
      //"posix_getpid()=" . posix_getpid() . ", posix_getppid()=" . posix_getppid();
      $content = $DateTime. " [" .  posix_getpid() ."]:[" . $level . "]" . $mylvl . " - " . $msg . "\n";
      echo $content;
   }
}

