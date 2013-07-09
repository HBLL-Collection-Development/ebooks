<?php
/**
  * Exports data to a CSV file
  *
  * @author Jared Howland <book.usage@jaredhowland.com>
  * @version 2013-07-09
  * @since 2013-05-30
  *
  */
require_once 'config.php';

$term = $_GET['term'];
$type = $_GET['type'];
$heading = to_slug($_GET['heading']);

function to_slug($string, $space='-') {
  if (function_exists('iconv')) {
    $string = @iconv('UTF-8', 'ASCII//TRANSLIT', $string);
  }
  $string = preg_replace('/[^a-zA-Z0-9 -]/', '', $string);
  $string = strtolower($string);
  $string = str_replace(' ', $space, $string);
  return $string;
}

switch ($type) {
  case 'vendor':
    $browse = new browse;
    $array = $browse->vendor($term, NULL, 0);
    break;
    
  case 'platform':
    $browse = new browse;
    $array = $browse->platform($term, NULL, 0);
    break;
    
  case 'lib':
    $browse = new browse;
    $array = $browse->lib($term, NULL, 0);
    break;
    
  case 'call_num':
    $browse = new browse;
    $array = $browse->call_num($term, NULL, 0);
    break;
    
  case 'fund':
    $browse = new browse;
    $array = $browse->fund($term, NULL, 0);
    break;
    
  case 'title':
    $search = new search($term, NULL, 0);
    $array = $search->title();
    break;
    
  case 'isbn':
    $search = new search($term, NULL, 0);
    $array = $search->isbn();
    break;
  
  default:
    error::trigger('An error in downloading has occurred. Please go back and try again.');
    break;
}

$csv = new csv;
$csv->download($array, $heading);

?>
