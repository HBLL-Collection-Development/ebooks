<?php
/**
  * Grabs call numbers from books table and sorts them
  *
  * @author Jared Howland <book.usage@jaredhowland.com>
  * @version 2013-06-14
  * @since 2013-06-14
  *
  */
require_once 'config.php';
ini_set('max_execution_time', 0);

$call_nums       = get_call_nums();
$call_num_ranges = get_call_num_ranges();

foreach($call_nums as $book) {
  $book_id  = $book['book_id'];
  $call_num = $book['call_num'];
  $fund_id  = get_fund_id($call_num, $call_num_ranges);
  // show_data($call_num, $fund_id);
  update_books($book_id, $fund_id);
}

// function show_data($call_num, $fund_id) {
//   echo 'call: ' . $call_num . ' fund: ' . get_fund($fund_id) . '<br/>';
// }
// 
// function get_fund($fund_id) {
//   // Connect to database
//   $database = new db;
//   $db       = $database->connect();
//   $sql      = "SELECT fund_code FROM funds WHERE id = :fund_id";
//   $query    = $db->prepare($sql);
//   $query->bindParam(':fund_id', $fund_id);
//   $query->execute();
//   $results  = $query->fetchAll(PDO::FETCH_ASSOC);
//   $db       = NULL;
//   return $results[0]['fund_code'];
// }

function get_fund_id($call_num, $call_num_ranges) {
  foreach($call_num_ranges as $call_num_range) {
    $fund_id     = $call_num_range['fund_id'];
    $start_range = $call_num_range['start_range'];
    $end_range   = $call_num_range['end_range'];

    $array       = array($start_range, $end_range, $call_num);
    $sort        = new sort_lc($array);
    $sorted      = $sort->call_nums();

    // If $call_num falls between the start and end range then return the fund_id
    if($call_num === $sorted[1]) {
      return $fund_id;
    }
  // Assign fund_id of 0 if the call number cannot be parsed appropriately
  } return '0';
}

function normalize_call_num($call_num) {
  //Convert all alpha to uppercase
  $lc_call_no = strtoupper($call_num);

  // define special trimmings that indicate integer
  $integer_markers = array("C.","BD.","DISC","DISK","NO.","PT.","V.","VOL.");
  foreach ($integer_markers as $mark) {
    $mark = str_replace(".", "\.", $mark);
    $lc_call_no = preg_replace("/$mark(\d+)/","$mark$1;",$lc_call_no);
  } // end foreach int marker

  // Remove any inital white space
  $lc_call_no = preg_replace ("/\s*/","",$lc_call_no);

  if (preg_match("/^([A-Z]{1,3})\s*(\d+)\s*\.*(\d*)\s*\.*\s*([A-Z]*)(\d*)\s*([A-Z]*)(\d*)\s*(.*)$/",$lc_call_no,$m)) {
    $initial_letters = $m[1];
    $class_number    = $m[2];
    $decimal_number  = $m[3];
    $cutter_1_letter = $m[4];
    $cutter_1_number = $m[5];
    $cutter_2_letter = $m[6];
    $cutter_2_number = $m[7];
    $the_trimmings   = $m[8];
  } //end if call number match
  
  if ($class_number) {
    $class_number = sprintf("%5s", $class_number);
  }
  $decimal_number = sprintf("%-12s", $decimal_number);
  return array('initial_letters' => $initial_letters, 'class_number' => $class_number, 'decimal_number' => $decimal_number);
}

// $sort = new sort_lc($array);
// 
// print_r($sort->call_nums());
// template::display('generic.tmpl', '<p>File successfully loaded!</p>');

function get_call_nums() {
  // Connect to database
  $database = new db;
  $db       = $database->connect();
  $sql      = "SELECT id AS book_id, call_num FROM books WHERE call_num IS NOT NULL AND fund_id IS NULL";
  $query    = $db->prepare($sql);
  $query->execute();
  $results  = $query->fetchAll(PDO::FETCH_ASSOC);
  $db       = NULL;
  return $results;
}

function get_call_num_ranges() {
  // Connect to database
  $database = new db;
  $db       = $database->connect();
  $sql      = "SELECT fund_id, start_range, end_range FROM call_nums";
  $query    = $db->prepare($sql);
  $query->execute();
  $results  = $query->fetchAll(PDO::FETCH_ASSOC);
  $db       = NULL;
  $call_num_ranges = NULL;
  // Adjust start and end ranges so that true ranges are established for sorting later
  foreach($results as $call_num_range) {
    $fund_id     = $call_num_range['fund_id'];
    $start_range = $call_num_range['start_range'];
    $adjust_start_range = normalize_call_num($start_range);
    if(is_null($adjust_start_range['class_number'])) {
      $start_range = $start_range . '1';
    }
    $end_range   = $call_num_range['end_range'];
    $adjust_end_range = normalize_call_num($end_range);
    if(is_null($adjust_end_range['class_number'])) {
      $end_range = $end_range . '9999.9999';
    } else if($adjust_end_range['decimal_number'] == '            ') {
      $end_range = $end_range . '.9999';
    }
    $call_num_ranges[] = array('fund_id' => $fund_id, 'start_range' => $start_range, 'end_range' => $end_range);
  }
  return $call_num_ranges;
}

function update_books($book_id, $fund_id) {
  // Connect to database
  $database = new db;
  $db       = $database->connect();
  $sql      = 'SET foreign_key_checks = 0;UPDATE `books` SET fund_id = :fund_id WHERE id = :book_id;SET foreign_key_checks = 1;';
  $query    = $db->prepare($sql);
  $query->bindParam(':fund_id', $fund_id);
  $query->bindParam(':book_id', $book_id);
  $query->execute();
  $db       = NULL;
}

function pa($array) {
  echo '<pre>';
  print_r($array);
  echo '</pre>';
}

?>
