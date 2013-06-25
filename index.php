<?php
/**
  * Displays search screen for book usage database
  * TODO: Allow dynamic sorting of columns
  *
  * @author Jared Howland <book.usage@jaredhowland.com>
  * @version 2013-05-15
  * @since 2013-04-23
  *
  */
require_once 'config.php';

$search    = new search(NULL);
$platforms = $search->format_platforms($platform_id);
$vendors   = $search->format_vendors($vendor_id);
$libs      = $search->format_libs($lib_id);
$funds     = $search->format_funds($fund_id);
$call_nums = $search->format_call_nums($call_num_id);

$dropdown_fix = template::get_dropdown_fix($vendor_id, $platform_id, $lib_id, $fund_id, $call_num_id);

$html = array('title' => 'Home', 'dropdown_fix' => $dropdown_fix, 'platforms' => $platforms, 'vendors' => $vendors, 'libs' => $libs, 'funds' => $funds, 'call_nums' => $call_nums, 'html' => $html);

template::display('home.tmpl', $html);
?>
