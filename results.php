<?php
/**
  * Displays search results
  *
  * @author Jared Howland <book.usage@jaredhowland.com>
  * @version 2013-07-11
  * @since 2013-07-11
  *
  */
require_once 'config.php';

$results = new results;
$html = $results->get($_GET);
template::display('results.tmpl', $html);

?>
