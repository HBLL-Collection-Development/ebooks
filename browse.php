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
$results = $browse->vendor($vendor_id);
// echo '<pre>';print_r($results);echo '</pre>';die();
$html = array('title' => 'Home', 'html' => $results);

template::display('results.tmpl', $html);
?>
