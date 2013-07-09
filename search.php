<?php
/**
  * Displays search results
  *
  * @author Jared Howland <book.usage@jaredhowland.com>
  * @version 2013-05-30
  * @since 2013-05-29
  *
  */
require_once 'config.php';

$title            = $_GET['title'];
$isbn             = $_GET['isbn'];
$page             = $_GET['page'];
if(is_null($page)) { $page = 1; }
$results_per_page = $_GET['rpp'];
if(is_null($results_per_page)) { $results_per_page = config::RESULTS_PER_PAGE; }

if($title) {
  $search    = new search($title, $page, $results_per_page);
  $results = $search->title();
  $term    = $title;
  $type    = 'title';
} else if ($isbn) {
  $search    = new search($isbn, $page, $results_per_page);
  $results = $search->isbn();
  $term    = $isbn;
  $type    = 'isbn';
} else {
  $search    = new search($title, $page, $results_per_page);
  $results = $search->title();
  $term    = $query;
  $type    = 'title';
}

$platforms = $search->format_platforms();
$vendors   = $search->format_vendors();
$libs      = $search->format_libs();
$funds     = $search->format_funds();
$call_nums = $search->format_call_nums();

$dropdown_fix = template::get_dropdown_fix($vendor_id, $platform_id, $lib_id, $fund_id, $call_num_id);

$heading = $results['search_term'];

$html = array('title' => 'Search Usage', 'heading' => $heading, 'dropdown_fix' => $dropdown_fix, 'type' => $type, 'term' => $term, 'platforms' => $platforms, 'vendors' => $vendors, 'libs' => $libs, 'funds' => $funds, 'call_nums' => $call_nums, 'html' => $results);

template::display('results.tmpl', $html);
?>
