<?php
/**
  * Kicks off background processing of usage data
  *
  * @author Jared Howland <book.usage@jaredhowland.com>
  * @version 2013-05-08
  * @since 2013-05-04
  *
  */
require_once 'config.php';

function microtime_float() {
  list($usec, $sec) = explode(' ', microtime());
  return ((float)$usec + (float)$sec);
}

$time_start = microtime_float();

$process = new parse();

$time_end = microtime_float();
$seconds = round(($time_end - $time_start), 2);
$minutes = round(($seconds / 60), 2);

echo 'Success! Processing took ' . $seconds . ' seconds (' . $minutes . ' minutes).';
?>