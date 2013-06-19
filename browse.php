<?php
/**
  * Displays browse results
  *
  * @author Jared Howland <book.usage@jaredhowland.com>
  * @version 2013-05-15
  * @since 2013-05-15
  *
  */
require_once 'config.php';

$vendor_id   = $_GET['vendor'];
$platform_id = $_GET['platform'];
$lib_id      = $_GET['lib'];
$fund_id     = $_GET['fund'];
$call_num_id = $_GET['call_num'];

$browse    = new browse;
$search    = new search(NULL);
$platforms = $search->format_platforms($platform_id);
$vendors   = $search->format_vendors($vendor_id);
$libs      = $search->format_libs($lib_id);
$funds     = $search->format_funds($fund_id);
$call_nums = $search->format_call_nums($call_num_id);

if($vendor_id) {
  $results = $browse->vendor($vendor_id);
  $heading = template::get_vendor($vendor_id);
  $term    = $vendor_id;
  $type    = 'vendor';
} else if($platform_id) {
  $results = $browse->platform($platform_id);
  $heading = template::get_platform($platform_id);
  $term    = $platform_id;
  $type    = 'platform';
} else if($lib_id) {
  $results = $browse->lib($lib_id);
  $heading = template::get_lib($lib_id);
  $term    = $lib_id;
  $type    = 'lib';
} else if($fund_id) {
  $results = $browse->fund($fund_id);
  $heading = template::get_fund($fund_id);
  $term    = $fund_id;
  $type    = 'fund';
} else if($call_num_id) {
  $results = $browse->call_num($call_num_id);
  $heading = template::get_call_num($call_num_id);
  $term    = $call_num_id;
  $type    = 'call_num';
} else {
  error::trigger('Only valid browse types are "vendor", "platform", and "lib".');
}

$html = array('title' => 'Browse Usage', 'heading' => $heading, 'platforms' => $platforms, 'vendors' => $vendors, 'libs' => $libs, 'funds' => $funds, 'call_nums' => $call_nums, 'term' => $term, 'type' => $type, 'html' => $results);

template::display('results.tmpl', $html);
?>
