<?php
/**
  * Displays search screen for book usage database
  *
  * @author Jared Howland <book.usage@jaredhowland.com>
  * @version 2013-05-03
  * @since 2013-05-01
  *
  */
require_once 'config.php';

$ingest = new ingest_counter_rpt2;
$success = $ingest->ingest($_FILES);

?>