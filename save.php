<?php
/**
  * Saves title unicode status from check_unicode.php
  *
  * @author Jared Howland <book.usage@jaredhowland.com>
  * @version 2014-05-07
  * @since 2014-05-07
  *
  */
require_once 'config.php';

$id    = $_REQUEST['id'];
$value = $_REQUEST['value']; // Y, N, Unknown

$type = explode('_', $id);
$id   = (int) $type[1];
$type = $type[0]; // orig, latin1, latin9

if($type == 'orig' && $value == 'Y') {
  set_unicode('Y', $id);
  echo 'Fixed';
} elseif($type == 'latin1' && $value == 'Y') {
  $title = get_title($id);
  $latin1 = mb_convert_encoding($title, 'ISO-8859-1', 'UTF-8');
  if(update_title($latin1, $id)) {
    set_unicode('Y', $id);
    echo 'Fixed';
  } else {
    echo 'Error';
  }
} elseif($type == 'latin9' && $value == 'Y') {
  $title = get_title($id);
  $latin9 = mb_convert_encoding($title, 'ISO-8859-15', 'UTF-8');
  if(update_title($latin9, $id)) {
    set_unicode('Y', $id);
    echo 'Fixed';
  } else {
    echo 'Error';
  }
} else {
  return get_title($id);
}

function get_title($id) {
  // Connect to database
  $database = new db;
  $db    = $database->connect();
  $sql   = 'SELECT id, title FROM books WHERE id = :id';
  $query = $db->prepare($sql);
  $query->bindParam(':id', $id);
  $query->execute();
  $title = $query->fetch(PDO::FETCH_ASSOC);
  $db = NULL;
  return $title['title'];
}

function set_unicode($value, $id) {
  // Connect to database
  $database = new db;
  $db    = $database->connect();
  $sql   = 'UPDATE books SET valid_utf8 = :value WHERE id = :id';
  $query = $db->prepare($sql);
  $query->bindParam(':id', $id);
  $query->bindParam(':value', $value);
  $query->execute();
  $db = NULL;
  return $query;
}

function update_title($title, $id) {
  // Connect to database
  $database = new db;
  $db    = $database->connect();
  $sql   = 'UPDATE books SET title = :title WHERE id = :id';
  $query = $db->prepare($sql);
  $query->bindParam(':id', $id);
  $query->bindParam(':title', $title);
  $query->execute();
  $db = NULL;
  return $query;
}

?>
