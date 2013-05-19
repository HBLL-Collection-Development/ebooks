<?php
/**
  * Ingests file into temp tables
  *
  * @author Jared Howland <book.usage@jaredhowland.com>
  * @version 2013-05-15
  * @since 2013-05-01
  *
  */
require_once 'config.php';

$report = $_POST['report'];
switch ($report) {
  case '1':
    $ingest = new ingest_counter_rpt1($_FILES);
    if($ingest) {
      template::display('generic.tmpl', '<p>File successfully loaded!</p>');
    }
    break;
    
  case '2':
    $ingest = new ingest_counter_rpt2($_FILES);
    if($ingest) {
      template::display('generic.tmpl', '<p>File successfully loaded!</p>');
    }
    break;
  
  default:
    error::trigger('Error. You may only upload Project COUNTER Book Reports #1 and #2. Please go back and try again.');
    break;
}

?>
