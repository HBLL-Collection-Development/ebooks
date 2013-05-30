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

$process_br1 = new process_counter_rpt1();
$process_br2 = new process_counter_rpt2();

// Connect to database
$database = new db;
$db       = $database->connect();
$sql      = <<<SQL
  DROP TABLE IF EXISTS books_search;
  CREATE TABLE books_search LIKE books;
  ALTER TABLE books_search ENGINE=MYISAM, ADD FULLTEXT (title), ADD FULLTEXT (isbn);
  INSERT INTO books_search SELECT * FROM books;
SQL;
$query    = $db->prepare($sql);
$query->execute();
$db = NULL;

$time_end = microtime_float();
$seconds = round(($time_end - $time_start), 2);
$minutes = round(($seconds / 60), 2);

echo 'Success! Processing took ' . $seconds . ' seconds (' . $minutes . ' minutes).';

?>