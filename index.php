<?php
/**
  * Displays search screen for book usage database
  * TODO: Allow dynamic sorting of columns
  *
  * @author Jared Howland <book.usage@jaredhowland.com>
  * @version 2013-05-15
  * @since 2013-04-23
  *
  */
require_once 'config.php';

$vendors   = format_vendors();
$platforms = format_platforms();
$libs      = format_libs();
$funds     = format_funds();
$call_nums = format_call_nums();

$html = <<<HTML

<div class="page">
  <h1>eBook Usage Database</h1>
  <div class="span-24">
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
        <h2>Platform Browse</h2>
        <form action="browse.php" method="get" accept-charset="utf-8">
          <select name="platform" id="platform">
            $platforms
          </select>
          <input class="button small" type="submit" name="submit" value="Browse" />
        </form>
      </section>
    </div>
    <!-- <div class="span-7 prepend-1">
          <section>
            <h2>Vendor Browse</h2>
            <form action="browse.php" method="get" accept-charset="utf-8">
              <select name="vendor" id="vendor">
                $vendors
              </select>
              <input class="button small" type="submit" name="submit" value="Browse" />
            </form>
          </section>
        </div> -->
    
    <div class="span-7 prepend-1">
      <section>
        <h2>Fund Code Browse*</h2>
        <form action="browse.php" method="get" accept-charset="utf-8">
          <select name="fund" id="fund">
            $funds
          </select>
          <input class="button small" type="submit" name="submit" value="Browse" />
        </form>
      </section>
    </div>
  </div>
  
  <div class="span-24">
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
        <h2>Subject Librarian Browse*</h2>
        <form action="browse.php" method="get" accept-charset="utf-8">
          <select name="lib" id="lib">
            $libs
          </select>
          <input class="button small" type="submit" name="submit" value="Browse" />
        </form>
      </section>
    </div>

    <div class="span-7 prepend-1">
      <section>
        <h2>Call Number Browse*</h2>
        <form action="browse.php" method="get" accept-charset="utf-8">
          <select name="call_num" id="call_num">
            $call_nums
          </select>
          <input class="button small" type="submit" name="submit" value="Browse" />
        </form>
      </section>
    </div>
  </div>
</div>
HTML;

/**
  * List of vendors in database
  *
  * @param NULL
  * @return array List of vendors in database
  *
  */
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

/**
  * List of platforms
  *
  * @param NULL
  * @return array List of platforms in database
  *
  */
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

/**
  * List of subject librarians
  *
  * @param NULL
  * @return array List of subject librarians
  *
  */
function get_libs() {
  // Connect to database
  $database = new db;
  $db    = $database->connect();
  $sql   = 'SELECT id AS lib_id, first_name, last_name FROM libs ORDER BY last_name ASC';
  $query = $db->prepare($sql);
  $query->execute();
  $results = $query->fetchAll(PDO::FETCH_ASSOC);
  $db = NULL;
  return $results;
}

/**
  * List of fund codes
  *
  * @param NULL
  * @return array List of fund codes
  *
  */
function get_funds() {
  // Connect to database
  $database = new db;
  $db    = $database->connect();
  $sql   = 'SELECT id AS fund_id, fund_code, fund_name FROM funds ORDER BY fund_code ASC';
  $query = $db->prepare($sql);
  $query->execute();
  $results = $query->fetchAll(PDO::FETCH_ASSOC);
  $db = NULL;
  return $results;
}

/**
  * List of call number ranges
  *
  * @param NULL
  * @return array List of call number ranges
  *
  */
function get_call_nums() {
  // Connect to database
  $database = new db;
  $db    = $database->connect();
  $sql   = 'SELECT id AS call_num_id, start_range AS call_num_start, end_range AS call_num_end FROM call_nums ORDER BY start_range ASC';
  $query = $db->prepare($sql);
  $query->execute();
  $results = $query->fetchAll(PDO::FETCH_ASSOC);
  $db = NULL;
  return $results;
}

/**
  * Format vendors for HTML form
  *
  * @param NULL
  * @return string HTML for drop-down form of all vendors
  *
  */
function format_vendors() {
  $html = NULL;
  foreach(get_vendors() as $vendor) {
    $vendor_id = $vendor['id'];
    $vendor    = $vendor['vendor'];
    $html     .= '<option value="' . $vendor_id . '">' . $vendor . '</option>';
  }
  return $html;
}

/**
  * Format platforms for HTML form
  *
  * @param NULL
  * @return string HTML for drop-down form of all platforms
  *
  */
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

/**
  * Format subject librarians for HTML form
  *
  * @param NULL
  * @return string HTML for drop-down form of all subject librarians
  *
  */
function format_libs() {
  $html = NULL;
  foreach(get_libs() as $lib) {
    $lib_id     = $lib['lib_id'];
    $first_name = $lib['first_name'];
    $last_name  = $lib['last_name'];
    $html       .= '<option value="' . $lib_id . '">' . $first_name . ' ' . $last_name . '</option>';
  }
  return $html;
}

/**
  * Format fund names for HTML form
  *
  * @param NULL
  * @return string HTML for drop-down form of all fund codes
  *
  */
function format_funds() {
  $html = NULL;
  foreach(get_funds() as $fund) {
    $fund_id   = $fund['fund_id'];
    $fund_code = $fund['fund_code'];
    $fund_name = $fund['fund_name'];
    $html       .= '<option value="' . $fund_id . '">' . $fund_code . ' (' . $fund_name . ')</option>';
  }
  return $html;
}

/**
  * Format call number ranges for HTML form
  *
  * @param NULL
  * @return string HTML for drop-down form of all fund codes
  *
  */
function format_call_nums() {
  $html = NULL;
  foreach(get_call_nums() as $call_num) {
    $call_num_id    = $call_num['call_num_id'];
    $call_num_start = $call_num['call_num_start'];
    $call_num_end   = $call_num['call_num_end'];
    if($call_num_start === $call_num_end) {
      $call_number = $call_num_start;
    } else {
      $call_number = $call_num_start . '–' . $call_num_end;
    }
    $html       .= '<option value="' . $call_num_id . '">' . $call_number . '</option>';
  }
  return $html;
}


$html = array('title' => 'Home', 'html' => $html);

template::display('generic.tmpl', $html);
?>
