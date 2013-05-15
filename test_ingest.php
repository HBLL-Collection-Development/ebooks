<?php
/**
  * Ingests file into temp tables
  *
  * @author Jared Howland <book.usage@jaredhowland.com>
  * @version 2013-05-03
  * @since 2013-05-01
  *
  */
require_once 'config.php';

$report = $_POST['report'];
switch ($report) {
  case '1':
    $ingest = new ingest_counter_rpt1($_FILES);
    break;
    
  case '2':
    $ingest = new ingest_counter_rpt2($_FILES);
    break;
  
  default:
    echo 'Error. You may only upload Project COUNTER Book Reports #1 and #2. Go back and try again.';
    break;
}

?>