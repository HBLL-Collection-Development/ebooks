<?php
/**
  * Class to search OCLC Classify Web Service
  * http://classify.oclc.org/classify2/api_docs/classify.html
  *
  * @author Jared Howland <book.usage@jaredhowland.com>
  * @version 2013-05-29
  * @since 2013-05-29
  *
  */

class details {
  private $book_id;
  
  /**
   * Passes the content into the specified Twig template
   *
   * @access public
   * @param string Search type to perform; Valid values: standard_number, title; Default: standard_number
   * @param string|array String or array of search term(s)
   * @return array Array of title, author, isbn, call_num for all search terms
   */
  public function __construct($book_id) {
    $this->book_id = $book_id;
  }
  
  public function get_details() {
    $usage     = $this->get_usage();
    $book_data = $this->get_book_data();
    return array('book_data' => $book_data, 'usage' => $usage);
  }
  
  private function get_usage() {
    // Connect to database
    $database = new db;
    $db       = $database->connect();
    // Query the counter_usage view table
    $sql      = 'SELECT vendor_id, platform_id, usage_year, counter_usage, usage_type FROM counter_usage WHERE book_id = :book_id ORDER BY usage_year DESC';
    $query    = $db->prepare($sql);
    $query->bindParam(':book_id', $this->book_id);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC);
    $db = NULL;
    return $this->sort_usage($results);
  }
  
  private function sort_usage($usage) {
    foreach($usage as $platform_id => $platform) {
      $br1_years = array();
      $br2_years = array();
      $br1_usage = array();
      $br2_usage = array();
      foreach($platform as $use) {
        $usage_type = $use['usage_type'];
        $usage_year = $use['usage_year'];
        $counter_usage = $use['counter_usage'];
        if($usage_type == 'br1') {
          $br1_years[] = $usage_year;
          $br1_usage[] = $counter_usage;
        } else {
          $br2_years[] = $usage_year;
          $br2_usage[] = $counter_usage;
        }
      }
      $br1 = array('years' => $br1_years, 'usage' => $br1_usage);
      $br2 = array('years' => $br2_years, 'usage' => $br2_usage);
      if(count($br1['years']) < 1 ) { $br1 = NULL; }
      if(count($br2['years']) < 1 ) { $br2 = NULL; }
      $platform = template::get_platform($platform_id);
      $all_usage[] = array('platform' => $platform, 'br1' => $br1, 'br2' => $br2);
    }
    return $all_usage;
  }
  
  private function get_book_data() {
    // Connect to database
    $database = new db;
    $db       = $database->connect();
    // Query the counter_usage view table
    $sql      = 'SELECT title, author, publisher, doi, oclc, isbn, issn, call_num FROM books WHERE id = :book_id LIMIT 1';
    $query    = $db->prepare($sql);
    $query->bindParam(':book_id', $this->book_id);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_ASSOC);
    $db = NULL;
    return $results[0];
  }
  
}
?>
