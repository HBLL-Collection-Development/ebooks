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

$browse    = new browse;
$search    = new search(NULL);
$platforms = $search->format_platforms();
$vendors   = $search->format_vendors();

if($vendor_id) {
  $results = $browse->vendor($vendor_id);
  $heading = template::get_vendor($vendor_id);
  $term    = $vendor_id;
  $type    = 'vendor';
} else {
  $results = $browse->platform($platform_id);
  $heading = template::get_platform($platform_id);
  $term    = $platform_id;
  $type    = 'platform';
}

$html = array('title' => 'Browse Usage', 'heading' => $heading, 'platforms' => $platforms, 'vendors' => $vendors, 'term' => $term, 'type' => $type, 'html' => $results);

template::display('results.tmpl', $html);
?>
