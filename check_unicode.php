<?php
/**
  * Shows titles from database with non-ASCII characters that may be mangled UTF8
  * Fix incorrect titles in the database (no GUI for this yet)
  * If all are correct, run the following SQL on the book_usage database:
  * UPDATE `books` SET `valid_utf8` = 'Y'
  * TODO: This should probably be cleaned up and a GUI created for correcting incorrectly encoded titles
  *
  * @author Jared Howland <book.usage@jaredhowland.com>
  * @version 2014-05-07
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
  $html = '<table><tr><th>book_id</th><th>Original Encoding</th><th>ISO-8859-1 to UTF-8</th><th>ISO-8859-15 to UTF-8</th></tr>';
  foreach(get_titles() as $title) {
    $id    = $title['id'];
    $title = $title['title'];
    $latin1 = mb_convert_encoding($title, 'ISO-8859-1', 'UTF-8');
    $latin9 = mb_convert_encoding($title, 'ISO-8859-15', 'UTF-8');
    $html   .= '<tr><td>' . $id . '</td><td><a href="#" class="edit" id="orig_' . $id . '">' . $title . '</a></td><td><a href="#" class="edit" id="latin1_' . $id . '">' . $latin1 . '</a></td><td><a href="#" class="edit" id="latin9_' . $id . '">' . $latin9 . '</a></td></tr>';
  }
  $html .= '</table>';
  return $html;
}

$html = array('title' => 'Unicode', 'html' => $html);

template::display('generic.tmpl', $html);
?>
