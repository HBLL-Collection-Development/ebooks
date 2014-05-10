<?php
/**
  * Displays search screen for book usage database
  *
  * @author Jared Howland <book.usage@jaredhowland.com>
  * @version 2014-05-09
  * @since 2013-04-23
  *
  */
require_once 'config.php';

$search    = new search(null);
$platforms = $search->get_platforms();
$libs      = $search->get_libs();
$funds     = $search->get_funds();
$call_nums = $search->get_call_nums();

$html = array('title' => 'Home', 'platforms' => $platforms, 'libs' => $libs, 'funds' => $funds, 'call_nums' => $call_nums);

template::display('home.tmpl', $html);
?>
