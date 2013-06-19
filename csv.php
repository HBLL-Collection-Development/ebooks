<?php
/**
  * Exports data to a CSV file
  *
  * @author Jared Howland <book.usage@jaredhowland.com>
  * @version 2013-05-31
  * @since 2013-05-30
  *
  */
require_once 'config.php';

$term = $_GET['term'];
$type = $_GET['type'];

switch ($type) {
  case 'vendor':
    $browse = new browse;
    $array = $browse->vendor($term);
    break;
    
  case 'platform':
    $browse = new browse;
    $array = $browse->platform($term);
    break;
    
  case 'lib':
    $browse = new browse;
    $array = $browse->lib($term);
    break;
    
  case 'call_num':
    $browse = new browse;
    $array = $browse->call_num($term);
    break;
    
  case 'fund':
    $browse = new browse;
    $array = $browse->fund($term);
    break;
    
  case 'title':
    $search = new search($term);
    $array = $search->title();
    break;
    
  case 'isbn':
    $search = new search($term);
    $array = $search->isbn();
    break;
  
  default:
    error::trigger('An error in downloading has occurred. Please go back and try again.');
    break;
}

$csv = new csv;
$csv->download($array);

?>
