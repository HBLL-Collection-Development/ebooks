<?php
/**
  * Displays frequently asked questions
  *
  * @author Jared Howland <book.usage@jaredhowland.com>
  * @version 2013-05-30
  * @since 2013-05-30
  *
  */
require_once 'config.php';


$html = '<h2>What usage is included in this database?</h2><p>This list is updated dynamically so it should always reflect reality:' . get_included_usage() . '</p>';

$html .= '<h2>What are the advanced search options for title searches?</h2><p><table style="font-size:1.2em;"><thead style="color:#bbb;"><tr><th>Operator</th><th style="text-align:left;">Description</th></tr></thead><tbody><tr><td>+</td><td style="text-align:left;">Include, word must be present.</td></tr><tr><td>-</td><td style="text-align:left;">Exclude, word must not be present.</td></tr><tr><td>&gt;</td><td style="text-align:left;">Include, and increase ranking value.</td></tr><tr><td>&lt;</td><td style="text-align:left;">Include, and decrease ranking value.</td></tr><tr><td>()</td><td style="text-align:left;">Group words into sub expressions (allowing them to be included, excluded, ranked, and so forth as a group).</td></tr><tr><td>~</td><td style="text-align:left;">Negate a word’s ranking value.</td></tr><tr><td>*</td><td style="text-align:left;">Wildcard at end of word.</td></tr><tr><td>&#8220;&#8221;</td><td style="text-align:left;">Defines a phrase (as opposed to a list of individual words, the entire phrase is matched for inclusion or exclusion).</td></tr></tbody></table></p>';

$html .= '<h2>What should I know about ISBN searching?</h2><p>ISBNs are unique to a single edition of a book. Thus, print and electronic editions of the same book have two different ISBNs. This makes it difficult to know definitively whether or not we have an electronic copy because some vendors will list the print ISBN while others will list the electronic ISBN. This database will look up all related ISBNs for an ISBN search and look for those for you so that you do not need to perform several searches for different editions of a book. If we have an electronic edition of any ISBN, it should show up here (assuming the vendor has included the correct ISBN in their usage statistics).</p>';

$html .= '<h2>What is Project COUNTER?</h2><p>The <a href="http://www.projectcounter.org/">Project COUNTER</a> Code of Practice is a standard by which vendors collect usage statistics allowing you to make fair usage comparisons across different platforms.</p>';

$html .= '<h2>What is COUNTER Book Report #1?</h2><p>Book Report #1 is the number of successful title requests by title.</p>';

$html .= '<h2>What is COUNTER Book Report #2?</h2><p>Book Report #2 is the number of successful section requests by title.</p>';


function get_included_usage() {
  $html = NULL;
  $vendors = get_vendors();
  foreach($vendors as $vendor) {
    $vendor_id   = $vendor['id'];
    $vendor_name = $vendor['vendor'];
    $html .= '<h3>' . $vendor_name . '</h3>';
    $platforms = get_platforms($vendor_id);
    foreach($platforms as $platform) {
      $platform_name = $platform['platform'];
      $platform_id   = $platform['platform_id'];
      $years = get_years($platform_id);
      $min = $years[0]['min'];
      $max = $years[0]['max'];
      if($min === $max) {
        $range = $min;
      } else {
        $range = $min . '–' . $max;
      }
      $html .= '<p>' . $platform_name . ' (' . $range . ')</p>';
    }
  }
  return $html;
}

function get_vendors() {
  // Connect to database
  $database = new db;
  $db    = $database->connect();
  $sql   = 'SELECT id, vendor FROM vendors ORDER BY vendor ASC';
  $query = $db->prepare($sql);
  $query->execute();
  $results = $query->fetchAll(PDO::FETCH_ASSOC);
  $db = NULL;
  return $results;
}

function get_platforms($vendor_id) {
  // Connect to database
  $database = new db;
  $db    = $database->connect();
  $sql   = 'SELECT platforms.id AS platform_id, vendors.vendor AS vendor, platforms.platform AS platform FROM platforms, vendors WHERE platforms.vendor_id = vendors.id AND platforms.vendor_id = :vendor_id';
  $query = $db->prepare($sql);
  $query->bindParam(':vendor_id', $vendor_id);
  $query->execute();
  $results = $query->fetchAll(PDO::FETCH_ASSOC);
  $db = NULL;
  return $results;
}

function get_years($platform_id) {
  // Connect to database
  $database = new db;
  $db    = $database->connect();
  $sql   = 'SELECT MIN(usage_year) AS min, MAX(usage_year) AS max FROM counter_usage WHERE platform_id = :platform_id';
  $query = $db->prepare($sql);
  $query->bindParam(':platform_id', $platform_id);
  $query->execute();
  $results = $query->fetchAll(PDO::FETCH_ASSOC);
  $db = NULL;
  return $results;
}

$html = array('title' => 'FAQ', 'html' => $html);

template::display('generic.tmpl', $html);
?>
