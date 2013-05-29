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
  $html = '<table><tr><th>Original Encoding</th><th>ISO-8859-1 to UTF-8</th><th>ISO-8859-15 to UTF-8</th></tr>';
  foreach(get_titles() as $title) {
    $id    = $title['id'];
    $title = $title['title'];
    $title = str_replace('\\', '', $title);
    $latin1 = mysql_real_escape_string(mb_convert_encoding($title, 'ISO-8859-1', 'UTF-8'));
    $latin9 = mysql_real_escape_string(mb_convert_encoding($title, 'ISO-8859-15', 'UTF-8'));
    // Remove escape from single quotes before using SQL
    // $title = str_replace('\\', '', $title);
    $html   .= '<tr><td>' . $title . '</td><td>' . $latin1 . '</td><td>' . $latin9 . '</td></tr>';
  }
  $html .= '</table>';
  return $html;
}

$html = array('title' => 'Unicode', 'html' => $html);

template::display('generic.tmpl', $html);
?>
