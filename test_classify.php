<?php
/**
  * Displays search screen for book usage database
  *
  * @author Jared Howland <book.usage@jaredhowland.com>
  * @version 2013-05-01
  * @since 2013-04-30
  *
  */
require_once 'config.php';

$classify = new classify;
// $results = $classify->search('standard_number', array('0738433853', '9780470053041'));
// $results = $classify->search('title', 'Advanced Android Development: A Safari Guide');
$results = $classify->search('standard_number', '9780132490832');
print_r($results);
?>