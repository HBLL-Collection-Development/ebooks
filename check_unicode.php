<?php
/**
  * Displays search screen for book usage database
  *
  * @author Jared Howland <book.usage@jaredhowland.com>
  * @version 2013-05-20
  * @since 2013-04-23
  *
  */
require_once 'config.php';

$html = format_titles();

function get_titles() {
  // Connect to database
  $database = new db;
  $db    = $database->connect();
  $sql   = 'SELECT id, title FROM unicode';
  $query = $db->prepare($sql);
  $query->execute();
  $results = $query->fetchAll(PDO::FETCH_ASSOC);
  $db = NULL;
  return $results;
}

function format_titles() {
  $html = NULL;
  foreach(get_titles() as $title) {
    $id    = $title['id'];
    $title = $title['title'];
    // $title = mysql_real_escape_string(mb_convert_encoding($title, 'ISO-8859-1', 'UTF-8'));
    // $title = mysql_real_escape_string(mb_convert_encoding($title, 'ISO-8859-15', 'UTF-8'));
    // Remove escape from single quotes before using SQL
    // $title = str_replace('\\', '', $title);
    // $title   = 'UPDATE books SET title="' . $title . '" WHERE id=' . $id . ';';
    $html   .= '<p>' . $title . '</p>';
  }
  // $html .= mb_convert_encoding('InglÃ©s-EspaÃ±ol', 'ISO-8859-15', 'UTF-8');
  return $html;
}

$html = array('title' => 'Unicode', 'html' => $html);

template::display('generic.tmpl', $html);
?>
