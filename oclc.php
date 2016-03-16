<?php
/**
  * Displays search screen for book usage database
  *
  * @author Jared Howland <book.usage@jaredhowland.com>
  * @version 2014-05-09
  * @since 2013-04-23
  *
  */
require_once 'config.php';

use OCLC\classify;

try {
  $classify = new classify('php_array');
  $data     = $classify->stdnbr('785871937');
  print_r($data);
} catch (Exception $e) {
  echo $e->getMessage();
}

?>
