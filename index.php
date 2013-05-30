<?php
/**
  * Displays search screen for book usage database
  *
  * @author Jared Howland <book.usage@jaredhowland.com>
  * @version 2013-05-15
  * @since 2013-04-23
  *
  */
require_once 'config.php';

// TODO: Provide list of what platforms have usage and for what years

// TODO: Allow export to CSV for custom sorting

$vendors   = format_vendors();
$platforms = format_platforms();

$html = <<<HTML

<div class="page">
  <h1>eBook Usage Database</h1>
  <div class="span-16">
    <div class="span-7">
      <section>
        <form action="search.php" method="get" accept-charset="utf-8" class="search_form linear">
          <h2>Title Search</h2>
          <div class="search_wrapper">
            <input type="text" name="q" value="" id="title" placeholder="Title"/>
            <input type="hidden" name="type" value="title" id="type_title">
            <div class="search_btn">
              <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="18" height="20">
                <g transform="rotate(-45 10 10)">
                  <circle cx="8" cy="8" r="4.5" stroke-width="2" stroke="#fff" fill="none"></circle>
                  <line x1="8" y1="14" x2="8" y2="18" stroke="#fff" stroke-width="2" stroke-linecap="round"></line>
                </g>
              </svg>
            </div>
          </div>
          <input type="submit" class="button small" value="Search" />
        </form>
      </section>
    </div>

    <div class="span-7 prepend-1">
      <section>
        <h2>Vendor Browse</h2>
        <form action="browse.php" method="get" accept-charset="utf-8">
          <select name="vendor" id="vendor">
            $vendors
          </select>
          <input class="button small" type="submit" name="submit" value="Browse" />
        </form>
      </section>
    </div>
  </div>
  
  <div class="span-16">
    <div class="span-7">
      <section>
        <form action="search.php" method="get" accept-charset="utf-8" class="search_form linear">
          <h2>ISBN Search</h2>
          <div class="search_wrapper">
            <input type="text" name="q" value="" id="isbn" placeholder="ISBN (with or without dashes)"/>
            <input type="hidden" name="type" value="isbn" id="type_isbn">
            <div class="search_btn">
              <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="18" height="20">
                <g transform="rotate(-45 10 10)">
                  <circle cx="8" cy="8" r="4.5" stroke-width="2" stroke="#fff" fill="none"></circle>
                  <line x1="8" y1="14" x2="8" y2="18" stroke="#fff" stroke-width="2" stroke-linecap="round"></line>
                </g>
              </svg>
            </div>
          </div>
          <input type="submit" class="button small" value="Search" />
        </form>
      </section>
    </div>
    
    <div class="span-7 prepend-1">
      <section>
        <h2>Platform Browse</h2>
        <form action="browse.php" method="get" accept-charset="utf-8">
          <select name="platform" id="platform">
            $platforms
          </select>
          <input class="button small" type="submit" name="submit" value="Browse" />
        </form>
      </section>
    </div>
  </div>
</div>
HTML;

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

function get_platforms() {
  // Connect to database
  $database = new db;
  $db    = $database->connect();
  $sql   = 'SELECT platforms.id AS platform_id, vendors.vendor AS vendor, platforms.platform AS platform FROM platforms, vendors WHERE platforms.vendor_id = vendors.id ORDER BY platform ASC';
  $query = $db->prepare($sql);
  $query->execute();
  $results = $query->fetchAll(PDO::FETCH_ASSOC);
  $db = NULL;
  return $results;
}

function format_vendors() {
  $html = NULL;
  foreach(get_vendors() as $vendor) {
    $vendor_id = $vendor['id'];
    $vendor    = $vendor['vendor'];
    $html     .= '<option value="' . $vendor_id . '">' . $vendor . '</option>';
  }
  return $html;
}

function format_platforms() {
  $html = NULL;
  foreach(get_platforms() as $platform) {
    $platform_id = $platform['platform_id'];
    $vendor      = $platform['vendor'];
    $platform    = $platform['platform'];
    $html       .= '<option value="' . $platform_id . '">' . $platform . ' (' . $vendor . ')</option>';
  }
  return $html;
}

$html = array('title' => 'Home', 'html' => $html);

template::display('generic.tmpl', $html);
?>
