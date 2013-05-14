<?php
/**
  * Displays search screen for book usage database
  *
  * @author Jared Howland <book.usage@jaredhowland.com>
  * @version 2013-04-23
  * @since 2013-04-23
  *
  */
require_once 'config.php';

$display = new display;
$html    = $display->html('active');
template::display('html.tmpl', $html);
?>