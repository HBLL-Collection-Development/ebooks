<?php
/**
  * Kicks off processing of usage data
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
$database      = new db;
$db            = $database->connect();
$current_year  = config::$current_year;
$previous_year = config::$previous_year;

// After processing files in database (up to config::PROCESS_LIMIT), recreate books_search table (for full-text searching because INNODB does not currently support) and views for current and previous years of usage (in case usage loaded includes new dates)
$sql = <<<SQL
  DROP TABLE IF EXISTS books_search;
  CREATE TABLE books_search LIKE books;
  ALTER TABLE books_search ENGINE=MYISAM, ADD FULLTEXT (title), ADD FULLTEXT (isbn);
  INSERT INTO books_search SELECT * FROM books;
  DROP VIEW IF EXISTS current_br1;
  CREATE VIEW current_br1 AS SELECT counter_br1.book_id AS book_id, counter_br1.counter_br1 AS counter_br1 FROM counter_br1 WHERE (counter_br1.usage_year = $current_year);
  DROP VIEW IF EXISTS current_br2;
  CREATE VIEW current_br2 AS SELECT counter_br2.book_id AS book_id, counter_br2.counter_br2 AS counter_br2 FROM counter_br2 WHERE (counter_br2.usage_year = $current_year);
  DROP VIEW IF EXISTS previous_br1;
  CREATE VIEW previous_br1 AS SELECT counter_br1.book_id AS book_id, counter_br1.counter_br1 AS counter_br1 FROM counter_br1 WHERE (counter_br1.usage_year = $previous_year);
  DROP VIEW IF EXISTS previous_br2;
  CREATE VIEW previous_br2 AS SELECT counter_br2.book_id AS book_id, counter_br2.counter_br2 AS counter_br2 FROM counter_br2 WHERE (counter_br2.usage_year = $previous_year);
SQL;
$query = $db->prepare($sql);
$query->execute();
$db = NULL;

$time_end = microtime_float();
$seconds = round(($time_end - $time_start), 2);
$minutes = round(($seconds / 60), 2);

echo 'Success! Processing took ' . $seconds . ' seconds (' . $minutes . ' minutes).';

?>