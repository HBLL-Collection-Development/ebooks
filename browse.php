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

$browse = new browse;

if($vendor_id) {
  $results = $browse->vendor($vendor_id);
  $heading = template::get_vendor($vendor_id);
} else {
  $results = $browse->platform($platform_id);
  $heading = template::get_platform($platform_id);
}

$html = array('title' => 'Browse Usage', 'heading' => $heading, 'html' => $results);

template::display('results.tmpl', $html);
?>
