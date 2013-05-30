<?php
/**
  * Displays search results
  *
  * @author Jared Howland <book.usage@jaredhowland.com>
  * @version 2013-05-29
  * @since 2013-05-29
  *
  */
require_once 'config.php';

$query = $_GET['q'];
$type  = $_GET['type'];

$search = new search($query);

switch ($type) {
  case 'title':
    $results = $search->title();
    break;
  
  case 'isbn':
    $results = $search->isbn();
    break;
  
  default:
    $results = $search->title();
    $type    = 'title';
    break;
}

$html = array('title' => 'Search Usage', 'heading' => $heading, 'type' => $type, 'html' => $results);

template::display('search.tmpl', $html);
?>
