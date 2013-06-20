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

$query = $_GET['q'];
$type  = $_GET['type'];

$search    = new search($query);
$platforms = $search->format_platforms();
$vendors   = $search->format_vendors();
$libs      = $search->format_libs();
$funds     = $search->format_funds();
$call_nums = $search->format_call_nums();

switch ($type) {
  case 'title':
    $results = $search->title();
    $term    = $query;
    $type    = 'title';
    break;
  
  case 'isbn':
    $results = $search->isbn();
    $term    = $query;
    $type    = 'isbn';
    break;
  
  default:
    $results = $search->title();
    $term    = $query;
    $type    = 'title';
    break;
}

$dropdown_fix = NULL;
if(!$vendor_id) { $dropdown_fix .= "\$('#vendor').prop('selectedIndex', -1);"; }
if(!$platform_id) { $dropdown_fix .= "\$('#platform').prop('selectedIndex', -1);"; }
if(!$lib_id) { $dropdown_fix .= "\$('#lib').prop('selectedIndex', -1);"; }
if(!$fund_id) { $dropdown_fix .= "\$('#fund').prop('selectedIndex', -1);"; }
if(!$call_num_id) { $dropdown_fix .= "\$('#call_num').prop('selectedIndex', -1);"; }

$html = array('title' => 'Search Usage', 'heading' => $heading, 'dropdown_fix' => $dropdown_fix, 'type' => $type, 'term' => $term, 'platforms' => $platforms, 'vendors' => $vendors, 'libs' => $libs, 'funds' => $funds, 'call_nums' => $call_nums, 'html' => $results);

template::display('search.tmpl', $html);
?>
