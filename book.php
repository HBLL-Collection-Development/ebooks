<?php
/**
  * Displays browse results
  *
  * @author Jared Howland <book.usage@jaredhowland.com>
  * @version 2013-05-29
  * @since 2013-05-29
  *
  */
require_once 'config.php';

$book_id = $_GET['book_id'];

$details = new details($book_id);

$usage = $details->get_details();

$html = array('title' => 'Detailed Usage', 'html' => $usage);

template::display('details.tmpl', $html);
?>
