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

$search    = new search($query);
$platforms = $search->format_platforms();
$vendors   = $search->format_vendors();
$libs      = $search->format_libs();
$funds     = $search->format_funds();
$call_nums = $search->format_call_nums();

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

$html = array('title' => 'Home', 'html' => $html);

template::display('generic.tmpl', $html);
?>
