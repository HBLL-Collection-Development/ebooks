<?php
/**
  * Class to generate results for templates from $_GET array
  *
  * @author Jared Howland <book.usage@jaredhowland.com>
  * @version 2014-05-09
  * @since 2013-07-11
  *
  */

class results {

  /**
   * Constructor; Sets $this->term variable for term to search for in database
   *
   * @access public
   * @param string Search terms to search for
   * @return null
   */
  public function get($variables) {
    // Browse variables
    $vendor_id        = $variables['vendor'];
    $platform_id      = $variables['platform'];
    $lib_id           = $variables['lib'];
    $fund_id          = $variables['fund'];
    $call_num_id      = $variables['call_num'];
    // Search variables
    $title            = $variables['title'];
    $isbn             = $variables['isbn'];
    // Results metadata (pagination and sort data)
    $page             = $variables['page'];
    $results_per_page = $variables['rpp'];
    $sort             = $variables['sort'];
    // Set default values if NULL or invalid value
    // Default results per page from config file
    if(is_null($results_per_page) || $results_per_page < 1) { $results_per_page = config::RESULTS_PER_PAGE; }
    // Default to first page
    if(is_null($page) || $page < 1) { $page = 1; }
    // Default to title sort
    if(is_null($sort) || ($sort != 'title' && $sort != 'author' && $sort != 'callnum' && $sort != 'currentbr1' && $sort != 'currentbr2' && $sort != 'previousbr1' && $sort != 'previousbr2')) { $sort = 'title'; }

    // Determine search or browse type requested and get content from database
    if($title) {
      $search  = new search($title, $page, $results_per_page);
      $results = $search->title($sort);
      $heading = $results['search_term'];
      $term    = $title;
      $type    = 'title';
      $title   = 'Search Title';
    } else if($isbn) {
      $search  = new search($isbn, $page, $results_per_page);
      $results = $search->isbn($sort);
      $heading = $results['search_term'];
      $term    = $isbn;
      $type    = 'isbn';
      $title   = 'Search ISBN';
    } else if($platform_id) {
      $browse  = new browse($platform_id, $page, $results_per_page);
      $search    = new search(NULL);
      $results = $browse->platform($sort);
      $heading = template::get_platform($platform_id);
      $term    = $platform_id;
      $type    = 'platform';
      $title   = 'Browse Platform';
    } else if($lib_id) {
      $browse  = new browse($lib_id, $page, $results_per_page);
      $search    = new search(NULL);
      $results = $browse->lib($sort);
      $heading = template::get_lib($lib_id);
      $term    = $lib_id;
      $type    = 'lib';
      $title   = 'Browse Librarian';
    } else if($fund_id) {
      $browse  = new browse($fund_id, $page, $results_per_page);
      $search    = new search(NULL);
      $results = $browse->fund($sort);
      $heading = template::get_fund($fund_id);
      $term    = $fund_id;
      $type    = 'fund';
      $title   = 'Browse Fund Code';
    } else if($call_num_id) {
      $browse  = new browse($call_num_id, $page, $results_per_page);
      $search    = new search(NULL);
      $results = $browse->call_num($sort);
      $heading = template::get_call_num($call_num_id);
      $term    = $call_num_id;
      $type    = 'call_num';
      $title   = 'Browse Call Number';
    // Default is title search
    } else {
      $search  = new search($title, $page, $results_per_page);
      $results = $search->title($sort);
      $heading = $results['search_term'];
      $term    = $query;
      $type    = 'title';
      $title   = 'Search';
    }

    // Create drop-down forms for browse options
    $platforms = $search->get_platforms($platform_id);
    $libs      = $search->get_libs($lib_id);
    $funds     = $search->get_funds($fund_id);
    $call_nums = $search->get_call_nums($call_num_id);

    return array('title' => $title, 'heading' => $heading, 'type' => $type, 'term' => $term, 'sort' => $sort, 'platforms' => $platforms, 'libs' => $libs, 'funds' => $funds, 'call_nums' => $call_nums, 'html' => $results);

  }

}
?>
