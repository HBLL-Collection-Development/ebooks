<?php
/**
  * Class to search for a book
  *
  * @author Jared Howland <book.usage@jaredhowland.com>
  * @version 2013-07-09
  * @since 2013-05-29
  *
  */

class search {
  private $term;
  private $page;
  private $rpp;

  /**
   * Constructor; Sets variables for use later (search term, page number to display, results per page)
   *
   * @access public
   * @param string Search terms to search for
   * @return null
   */
  public function __construct($term, $page = 1, $rpp = config::RESULTS_PER_PAGE) {
    $this->term = $term;
    $this->page = $page;
    $this->rpp  = $rpp;
  }

  /**
    * Search by title
    *
    * @access public
    * @param NULL
    * @return array Formatted usage data for results
    *
    */
  public function title($sort = 'title') {
    $order_by = $this->get_order_by($sort);
    // Connect to database
    $database = new db;
    $db       = $database->connect();
    $term     = $db->quote($this->term);
    $sql      = "SELECT b.id, b.title, b.author, b.publisher, b.isbn, b.call_num, CAST(GROUP_CONCAT(DISTINCT o.platforms ORDER BY o.platforms SEPARATOR '|') AS CHAR CHARSET UTF8) AS platforms, (SELECT SUM(cbr2.counter_br2) FROM current_br2 cbr2 WHERE cbr2.book_id = b.id) AS current_br2, (SELECT SUM(pbr2.counter_br2) FROM previous_br2 pbr2 WHERE pbr2.book_id = b.id) AS previous_br2, (SELECT SUM(cbr1.counter_br1) FROM current_br1 cbr1 WHERE cbr1.book_id = b.id) AS current_br1, (SELECT SUM(pbr1.counter_br1) FROM previous_br1 pbr1 WHERE pbr1.book_id = b.id) AS previous_br1 FROM books AS b LEFT JOIN overlap o ON b.id = o.book_id WHERE id IN (SELECT id FROM books_search WHERE MATCH (title) AGAINST (" . $term . " IN BOOLEAN MODE) ORDER BY title) GROUP BY b.id ORDER BY " . $order_by;
    $query    = $db->prepare($sql);
    $query->execute();
    $results  = $query->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC);
    $db       = NULL;
    return $this->format_usage($results);
  }

  /**
    * Search by ISBN
    *
    * @access public
    * @param NULL
    * @return array Formatted usage data for results
    *
    */
  public function isbn($sort = 'title') {
    $order_by = $this->get_order_by($sort);
    $isbn     = $this->validate_standard_number($this->term);
    $in       = $this->get_related($isbn);
    if($in == 'invalidId') {
      $in = '0000000000';
    }
    // Connect to database
    $database = new db;
    $db       = $database->connect();
    $sql      = "SELECT b.id, b.title, b.author, b.publisher, b.isbn, b.call_num, CAST(GROUP_CONCAT(DISTINCT o.platforms ORDER BY o.platforms SEPARATOR '|') AS CHAR CHARSET UTF8) AS platforms, (SELECT SUM(cbr2.counter_br2) FROM current_br2 cbr2 WHERE cbr2.book_id = b.id) AS current_br2, (SELECT SUM(pbr2.counter_br2) FROM previous_br2 pbr2 WHERE pbr2.book_id = b.id) AS previous_br2, (SELECT SUM(cbr1.counter_br1) FROM current_br1 cbr1 WHERE cbr1.book_id = b.id) AS current_br1, (SELECT SUM(pbr1.counter_br1) FROM previous_br1 pbr1 WHERE pbr1.book_id = b.id) AS previous_br1 FROM books AS b LEFT JOIN overlap o ON b.id = o.book_id WHERE b.isbn IN (" . $in . ") GROUP BY b.id ORDER BY " . $order_by;
    $query    = $db->prepare($sql);
    $query->execute();
    $results  = $query->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC);
    $db       = NULL;
    return $this->format_usage($results);
  }

  /**
    * Translate URL sort request into correct field to search in MySQL
    *
    * @access private
    * @param string Requested field to sort by
    * @return string Correct field to sort by
    *
    */
  private function get_order_by($sort) {
    switch ($sort) {
      case 'title':
        return "IF(b.title = '' OR b.title IS NULL, 1, 0), b.title";
        break;
      case 'author':
        // Sort NULL authors to the bottom
        return "IF(b.author = '' OR b.author IS NULL, 1, 0), b.author";
        break;
      case 'callnum':
        // Sort NULL call numbers to the bottom
        return "IF(b.call_num = '' OR b.call_num IS NULL, 1, 0), b.call_num";
        break;
      case 'currentbr1':
        return 'current_br1 DESC';
        break;
      case 'currentbr2':
        return 'current_br2 DESC';
        break;
      case 'previousbr1':
        return 'previous_br1 DESC';
        break;
      case 'previousbr2':
        return 'previous_br2 DESC';
        break;
      default:
        return "IF(b.title = '' OR b.title IS NULL, 1, 0), b.title";
        break;
    }
  }

  /**
    * Get all related ISBNs from OCLC’s XISBN service
    *
    * @access private
    * @param string ISBN
    * @return array All ISBNs returned by XISBN service
    *
    */
  private function get_related($isbn) {
    return $this->format_xisbn($isbn);
  }

  /**
    * Format XISBN search results into an array
    *
    * @access private
    * @param string ISBN
    * @return array ALL ISBNs returned by XISBN service
    *
    */
  private function format_xisbn($isbn) {
    $xisbn = new xisbn;
    $related_isbns = $xisbn->get_isbns($isbn);
    return implode(',',$related_isbns);
  }

  /**
    * Format usage data array for consumption in Twig template
    *
    * @access private
    * @param array Usage data
    * @return array Formatted usage data
    *
    */
  private function format_usage($usage) {
    foreach($usage as $key => $result) {
      // Reset variables
      $title         = NULL;
      $author        = NULL;
      $publisher     = NULL;
      $isbn          = NULL;
      $call_num      = NULL;
      $platforms     = NULL;
      $platform_list = NULL;
      $current_br1   = NULL;
      $previous_br1  = NULL;
      $current_br2   = NULL;
      $previous_br2  = NULL;
      $book_id       = $key;
      $title         = $result[0]['title'];
      $author        = $result[0]['author'];
      $publisher     = $result[0]['publisher'];
      $isbn          = $result[0]['isbn'];
      $call_num      = $result[0]['call_num'];
      $platforms     = explode('|', $result[0]['platforms']);
      foreach($platforms as $platform) {
        $platform_list .= '<li>' . $platform . '</li>';
      }
      $current_br1   = $result[0]['current_br1'];
      $previous_br1  = $result[0]['previous_br1'];
      $current_br2   = $result[0]['current_br2'];
      $previous_br2  = $result[0]['previous_br2'];
      if(is_null($current_br1) AND is_null($current_br2) AND is_null($previous_br1) AND is_null($previous_br2)) {
      // Do not add to $usages array if there is no usage in the past 2 years
      } else {
      $usages[] = array('book_id' => $book_id, 'title' => $title, 'author' => $author, 'publisher' => $publisher, 'isbn' => $isbn, 'call_num' => $call_num, 'platforms' => $platform_list, 'latest_br1' => $current_br1, 'previous_br1' => $previous_br1, 'latest_br2' => $current_br2, 'previous_br2' => $previous_br2);
      }
    }
    $num_results  = count($usages);
    if($num_results > 0) {
      // If results per page is greater than zero, calculate number of total pages
      if($this->rpp > 0) {
        $pages      = ceil($num_results/$this->rpp);
      // Otherwise, just put it all on one page
      } else {
        $pages      = 1;
      }
      if($this->page > $pages || $this->page < 1) { $this->page = 1; }
      $start_from   = ($this->page - 1) * $this->rpp;
      $start_result = $start_from + 1;
      $end_result   = ($start_result + $this->rpp) - 1;
      if($end_result > $num_results) { $end_result = $num_results; }
      // If $rpp is zero, we assume we want everything on one page so do not slice up array for pagination
      if($this->rpp != 0) {
        $usages     = array_slice($usages, $start_from, $this->rpp);
      }
      return array('current_year' => config::$current_year, 'previous_year' => config::$previous_year, 'search_term' => htmlspecialchars($this->term), 'num_results' => $num_results, 'pages' => $pages, 'page' => $this->page, 'rpp' => $this->rpp, 'start_result' => $start_result, 'end_result' => $end_result, 'results' => $usages);
    } else {
      return array('search_term' => htmlspecialchars($this->term), 'num_results' => $num_results);
    }
  }

  /**
    * Validate ISBN-10s, ISBN-13s, and ISSNs
    *
    * @access protected
    * @param string Standard number (ISBN-10s, ISBN-13s, or ISSNs)
    * @return mixed ISBN-10, ISBN-13, or ISSN if valid; NULL otherwise
    *
    */
  protected function validate_standard_number($standard_number) {
    // Clean up number to make sure it is formatted correctly
    $standard_number = $this->strip_non_numeric($standard_number);
    // Determine whether it is an ISSN, ISBN-10, or ISBN-13
    // Validate using appropriate method
    if(strlen($standard_number) == 8) {
      $is_valid = $this->is_issn_valid($standard_number);
    }elseif(strlen($standard_number) == 10 || strlen($standard_number) == 13) {
      $is_valid = $this->is_isbn_valid($standard_number);
    } else {
      $is_valid = FALSE;
    }
    // Return cleaned string if valid
    if($is_valid) {
      return $standard_number;
    // Return null otherwise
    } else {
      return NULL;
    }
  }

  /**
   * Remove all characters except numbers and checksums (some of which are X)
   * Converts lowercase 'x' to uppercase 'X'
   *
   * @param string $string
   * @return string Cleaned string
   */
  protected function strip_non_numeric($string) {
    return preg_replace('{[^0-9X]}', '', strtoupper($string));
  }

  /**
    * Validates ISSNs
    *
    * @access protected
    * @param string ISSN
    * @return mixed ISSN if valid; NULL otherwise
    *
    */
  protected function is_issn_valid($issn) {
    $length = strlen($issn);
    // Get checksum
    $checksum = ($issn[($length - 1)] === 'X') ? 10 : intval($issn[($length - 1)]);
    // Calculate checksum
    if($length === 8) {
      $sum = NULL;
      for($i = 1; $i < $length; $i++) {
        $sum+= (8 - ($i - 1)) * $issn[($i - 1)];
      }
      $sum = 11 - $sum % 11;
      return $sum === $checksum;
    }
    return FALSE;
  }

  /**
    * Validates ISBN-10s and ISBN-13s
    *
    * @access protected
    * @param string ISBN
    * @return mixed ISBN if valid; NULL otherwise
    *
    */
  protected function is_isbn_valid($isbn) {
    if (!is_string($isbn) && !is_int($isbn)) {
      return false;
    }
    $isbn = (string) $isbn;
    // ISBN-10
    if(strlen($isbn) == 10) {
      $a = 0;
      for($i = 0; $i < 10; $i++){
        if($isbn[$i] == "X"){
          $a += 10*intval(10-$i);
        } else {//running the loop
        $a += intval($isbn[$i]) * intval(10-$i);
        }
      }
      return ($a % 11 == 0);
    // ISBN-13
    } elseif(strlen($isbn) == 13) {
      $check = 0;
      for($i = 0; $i < 13; $i+=2) $check += substr($isbn, $i, 1);
      for($i = 1; $i < 12; $i+=2) $check += 3 * substr($isbn, $i, 1);
      return $check % 10 == 0;
    } else {
      return FALSE;
    }
  }

  /**
    * List of all platforms in database
    *
    * @access public
    * @param NULL
    * @return array List of all platforms in database
    *
    */
  public function get_platforms($platform_id = null) {
    // Connect to database
    $database = new db;
    $db    = $database->connect();
    $sql   = 'SELECT platforms.id AS platform_id, vendors.vendor AS vendor, platforms.platform AS platform FROM platforms, vendors WHERE platforms.vendor_id = vendors.id ORDER BY platform ASC';
    $query = $db->prepare($sql);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_ASSOC);
    $db = NULL;
    return array('current_platform_id' => $platform_id, 'platforms' => $results);
  }

  /**
    * List of all subject librarians in database
    *
    * @access public
    * @param NULL
    * @return array List of all subject librarians in database
    *
    */
  public function get_libs($lib_id = null) {
    // Connect to database
    $database = new db;
    $db    = $database->connect();
    // Only show librarians that actually have books associated with them
    $sql   = 'SELECT DISTINCT fl.lib_id, l.first_name, l.last_name FROM funds_libs fl, libs l WHERE fl.lib_id = l.id AND fl.fund_id IN (SELECT DISTINCT fund_id FROM books WHERE fund_id IS NOT NULL AND fund_id != 0) ORDER BY l.last_name';
    $query = $db->prepare($sql);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_ASSOC);
    $db = NULL;
    return array('current_lib_id' => $lib_id, 'libs' => $results);
  }

  /**
    * List of fund codes
    *
    * @access public
    * @param NULL
    * @return array List of fund codes
    *
    */
  public function get_funds($fund_id = null) {
    // Connect to database
    $database = new db;
    $db    = $database->connect();
    // Only show fund codes that actually include books
    $sql   = 'SELECT id AS fund_id, fund_code, fund_name FROM funds WHERE id IN (SELECT DISTINCT fund_id FROM books WHERE fund_id IS NOT NULL AND fund_id != 0) ORDER BY fund_code ASC';
    $query = $db->prepare($sql);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_ASSOC);
    $db = NULL;
    return array('current_fund_id' => $fund_id, 'funds' => $results);
  }

  /**
    * List of call number ranges
    *
    * @access public
    * @param NULL
    * @return array List of call number ranges
    *
    */
  function get_call_nums($call_num_id = null) {
    // Connect to database
    $database = new db;
    $db    = $database->connect();
    // Only show call number ranges that actually include books
    $sql   = 'SELECT id AS call_num_id, start_range AS call_num_start, end_range AS call_num_end, subject FROM call_nums WHERE fund_id IN (SELECT DISTINCT fund_id FROM books WHERE fund_id IS NOT NULL AND fund_id != 0) ORDER BY start_range ASC';
    $query = $db->prepare($sql);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_ASSOC);
    $db = NULL;
    return array('current_call_num_id' => $call_num_id, 'call_nums' => $results);
  }
}
?>
