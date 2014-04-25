<?php
/**
  * Class to browse by platform or vendor
  *
  * @author Jared Howland <book.usage@jaredhowland.com>
  * @version 2013-07-09
  * @since 2013-05-15
  *
  */

class browse {
  private $term;
  private $page;
  private $rpp;

  /**
   * Constructor; Sets variables for use later (search term, page number to display, results per page)
   *
   * @access public
   * @param string Search terms to search for
   * @param int Page number to display
   * @param int Results per page
   * @return null
   */
  public function __construct($term, $page = 1, $rpp = config::RESULTS_PER_PAGE) {
    $this->term = $term;
    $this->page = $page;
    $this->rpp  = $rpp;
  }

  /**
    * Performs the browse by vendor
    *
    * @access public
    * @param string Sort type
    * @return array Usage array for specified vendor
    *
    */
  public function vendor($sort = 'title') {
    return $this->get_vendor_usage($sort);
  }

  /**
    * Performs the browse by platform
    *
    * @access public
    * @param string Sort type
    * @return array Usage array for specified platform
    *
    */
  public function platform($sort = 'title') {
    return $this->get_platform_usage($sort);
  }
  
  /**
    * Performs the browse by subject librarian
    *
    * @access public
    * @param string Sort type
    * @return array Usage array for books under fund codes assigned to specified librarian
    *
    */
  public function lib($sort = 'title') {
    return $this->get_librarian_usage($sort);
  }
  
  /**
    * Performs the browse by fund code
    *
    * @access public
    * @param string Sort type
    * @return array Usage array for books under specified fund code
    *
    */
  public function fund($sort = 'title') {
    return $this->get_fund_usage($sort);
  }
  
  /**
    * Performs the browse by call number
    *
    * @access public
    * @param string Sort type
    * @return array Usage array for books in specified call number range
    *
    */
  public function call_num($sort = 'title') {
    return $this->get_call_num_usage($sort);
  }
  
  /**
    * Formats the usage from $this->get_vendor_usage() or $this->get_platform_usage
    *
    * @access private
    * @param array Usage data from database query
    * @param int page Page of results to display
    * @param int rpp Results to show per page
    * @return array Formatted array for input into Twig template
    *
    */
  private function format_usage($usage, $page, $results_per_page) {
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
      if($results_per_page > 0) {
        $pages      = ceil($num_results/$results_per_page);
      // Otherwise, just put it all on one page
      } else {
        $pages      = 1;
      }
      if($page > $pages || $page < 1) { $page = 1; }
      $start_from   = ($page - 1) * $results_per_page;
      $start_result = $start_from + 1;
      $end_result   = ($start_result + $results_per_page) - 1;
      if($end_result > $num_results) { $end_result = $num_results; }
      // If $results_per_page is zero, we assume we want everything on one page so do not slice up array for pagination
      if($results_per_page != 0) {
        $usages     = array_slice($usages, $start_from, $results_per_page);
      }
      return array('current_year' => config::$current_year, 'previous_year' => config::$previous_year, 'search_term' => htmlspecialchars($this->term), 'num_results' => $num_results, 'pages' => $pages, 'page' => $page, 'rpp' => $results_per_page, 'start_result' => $start_result, 'end_result' => $end_result, 'results' => $usages);
    } else {
      return array('search_term' => htmlspecialchars($this->term), 'num_results' => $num_results);
    }
  }
  
  /**
    * Retrieves vendor usage from the database for the previous 2 years
    *
    * @access private
    * @param string Value to sort by
    * @return array Usage data formatted by $this->format_usage()
    *
    */
  private function get_vendor_usage($sort) {
    $order_by = $this->get_order_by($sort);
    // Connect to database
    $database = new db;
    $db       = $database->connect();
    $sql      = "SELECT bv.book_id, b.title, b.author, b.publisher, b.isbn, b.call_num, CAST(GROUP_CONCAT(DISTINCT o.platforms ORDER BY o.platforms SEPARATOR '|') AS CHAR CHARSET UTF8) AS platforms, (SELECT SUM(cbr2.counter_br2) FROM current_br2 cbr2 WHERE cbr2.book_id = b.id) AS current_br2, (SELECT SUM(pbr2.counter_br2) FROM previous_br2 pbr2 WHERE pbr2.book_id = b.id) AS previous_br2, (SELECT SUM(cbr1.counter_br1) FROM current_br1 cbr1 WHERE cbr1.book_id = b.id) AS current_br1, (SELECT SUM(pbr1.counter_br1) FROM previous_br1 pbr1 WHERE pbr1.book_id = b.id) AS previous_br1 FROM books_vendors bv LEFT JOIN books b ON bv.book_id = b.id LEFT JOIN overlap o ON bv.book_id = o.book_id WHERE bv.vendor_id = :vendor_id GROUP BY bv.book_id ORDER BY " . $order_by;
    $query = $db->prepare($sql);
    $query->bindParam(':vendor_id', $this->term);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC);
    $db = NULL;
    return $this->format_usage($results, $this->page, $this->rpp);
  }
  
  /**
    * Retrieves platform usage from the database for the previous 2 years
    *
    * @access private
    * @param string Value to sort by
    * @return array Usage data formatted by $this->format_usage()
    *
    */
  private function get_platform_usage($sort) {
    $order_by = $this->get_order_by($sort);
    // Connect to database
    $database = new db;
    $db       = $database->connect();
    $sql      = "SELECT bp.book_id, b.title, b.author, b.publisher, b.isbn, b.call_num, CAST(GROUP_CONCAT(DISTINCT o.platforms ORDER BY o.platforms SEPARATOR '|') AS CHAR CHARSET UTF8) AS platforms, (SELECT SUM(cbr2.counter_br2) FROM current_br2 cbr2 WHERE cbr2.book_id = b.id) AS current_br2, (SELECT SUM(pbr2.counter_br2) FROM previous_br2 pbr2 WHERE pbr2.book_id = b.id) AS previous_br2, (SELECT SUM(cbr1.counter_br1) FROM current_br1 cbr1 WHERE cbr1.book_id = b.id) AS current_br1, (SELECT SUM(pbr1.counter_br1) FROM previous_br1 pbr1 WHERE pbr1.book_id = b.id) AS previous_br1 FROM books_platforms bp LEFT JOIN books b ON bp.book_id = b.id LEFT JOIN overlap o ON bp.book_id = o.book_id WHERE bp.platform_id = :platform_id GROUP BY bp.book_id ORDER BY " . $order_by;
    $query = $db->prepare($sql);
    $query->bindParam(':platform_id', $this->term);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC);
    $db = NULL;
    return $this->format_usage($results, $this->page, $this->rpp);
  }
  
  /**
    * Retrieves usage for books assigned to fund codes of specified librarian for the previous 2 years
    *
    * @access private
    * @param string Value to sort by
    * @return array Usage data formatted by $this->format_usage()
    *
    */
  private function get_librarian_usage($sort) {
    $order_by = $this->get_order_by($sort);
    $lib_id = $this->term;
    $in = $this->get_fund_ids_by_librarian($lib_id);
    // Connect to database
    $database = new db;
    $db       = $database->connect();
    $sql      = "SELECT b.id, b.title, b.author, b.publisher, b.isbn, b.call_num, CAST(GROUP_CONCAT(DISTINCT o.platforms ORDER BY o.platforms SEPARATOR '|') AS CHAR CHARSET UTF8) AS platforms, (SELECT SUM(cbr2.counter_br2) FROM current_br2 cbr2 WHERE cbr2.book_id = b.id) AS current_br2, (SELECT SUM(pbr2.counter_br2) FROM previous_br2 pbr2 WHERE pbr2.book_id = b.id) AS previous_br2, (SELECT SUM(cbr1.counter_br1) FROM current_br1 cbr1 WHERE cbr1.book_id = b.id) AS current_br1, (SELECT SUM(pbr1.counter_br1) FROM previous_br1 pbr1 WHERE pbr1.book_id = b.id) AS previous_br1 FROM books b LEFT JOIN overlap o ON b.id = o.book_id WHERE b.fund_id IN (" . $in . ") GROUP BY b.id ORDER BY " . $order_by;
    $query = $db->prepare($sql);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC);
    $db = NULL;
    return $this->format_usage($results, $this->page, $this->rpp);
  }
  
  /**
    * Retrieves usage for books assigned to fund code
    *
    * @access private
    * @param string Value to sort by
    * @return array Usage data formatted by $this->format_usage()
    *
    */
  private function get_fund_usage($sort) {
    $order_by = $this->get_order_by($sort);
    // Connect to database
    $database = new db;
    $db       = $database->connect();
    $sql      = "SELECT b.id, b.title, b.author, b.publisher, b.isbn, b.call_num, CAST(GROUP_CONCAT(DISTINCT o.platforms ORDER BY o.platforms SEPARATOR '|') AS CHAR CHARSET UTF8) AS platforms, (SELECT SUM(cbr2.counter_br2) FROM current_br2 cbr2 WHERE cbr2.book_id = b.id) AS current_br2, (SELECT SUM(pbr2.counter_br2) FROM previous_br2 pbr2 WHERE pbr2.book_id = b.id) AS previous_br2, (SELECT SUM(cbr1.counter_br1) FROM current_br1 cbr1 WHERE cbr1.book_id = b.id) AS current_br1, (SELECT SUM(pbr1.counter_br1) FROM previous_br1 pbr1 WHERE pbr1.book_id = b.id) AS previous_br1 FROM books b LEFT JOIN overlap o ON b.id = o.book_id WHERE b.fund_id = :fund_id GROUP BY b.id ORDER BY " . $order_by;
    $query = $db->prepare($sql);
    $query->bindParam(':fund_id', $this->term);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC);
    $db = NULL;
    return $this->format_usage($results, $this->page, $this->rpp);
  }
  
  /**
    * Retrieves usage for books in specified call number range
    *
    * @access private
    * @param string Value to sort by
    * @return array Usage data formatted by $this->format_usage()
    *
    */
  private function get_call_num_usage($sort) {
    $order_by       = $this->get_order_by($sort);
    $fund_id        = $this->get_fund_ids_by_call_num($this->term);
    $call_num_range = $this->get_call_num_range($this->term);
    $start_range    = $call_num_range['start_range'];
    $end_range      = $call_num_range['end_range'];
    $adjust_start_range = $this->normalize_call_num($start_range);
    if(is_null($adjust_start_range['class_number'])) {
      $start_range = $start_range . '1';
    }
    $end_range   = $call_num_range['end_range'];
    $adjust_end_range = $this->normalize_call_num($end_range);
    if(is_null($adjust_end_range['class_number'])) {
      $end_range = $end_range . '9999.9999';
    } else if($adjust_end_range['decimal_number'] == '            ') {
      $end_range = $end_range . '.9999';
    }
    // Connect to database
    $database = new db;
    $db       = $database->connect();
    $sql      = "SELECT b.id, b.title, b.author, b.publisher, b.isbn, b.call_num, CAST(GROUP_CONCAT(DISTINCT o.platforms ORDER BY o.platforms SEPARATOR '|') AS CHAR CHARSET UTF8) AS platforms, (SELECT SUM(cbr2.counter_br2) FROM current_br2 cbr2 WHERE cbr2.book_id = b.id) AS current_br2, (SELECT SUM(pbr2.counter_br2) FROM previous_br2 pbr2 WHERE pbr2.book_id = b.id) AS previous_br2, (SELECT SUM(cbr1.counter_br1) FROM current_br1 cbr1 WHERE cbr1.book_id = b.id) AS current_br1, (SELECT SUM(pbr1.counter_br1) FROM previous_br1 pbr1 WHERE pbr1.book_id = b.id) AS previous_br1 FROM books b LEFT JOIN overlap o ON b.id = o.book_id WHERE b.fund_id = :fund_id GROUP BY b.id ORDER BY " . $order_by;
    $query = $db->prepare($sql);
    $query->bindParam(':fund_id', $fund_id);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC);
    $db = NULL;
    foreach($results as $key => $result) {
      $call_num    = $result[0]['call_num'];
      $array       = array($start_range, $end_range, $call_num);
      $sort        = new sort_lc($array);
      $sorted      = $sort->call_nums();
      // If $call_num falls between the start and end range then return the fund_id
      if($call_num !== $sorted[1]) {
        unset($results[$key]);
      }
    }
    return $this->format_usage($results, $this->page, $this->rpp);
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
    * Gets various pieces of a call number for parsing and sorting later
    *
    * @access private
    * @param string Call number
    * @return array Array of call number parts
    *
    */
  private function normalize_call_num($call_num) {
    //Convert all alpha to uppercase
    $lc_call_no = strtoupper($call_num);

    // define special trimmings that indicate integer
    $integer_markers = array("C.","BD.","DISC","DISK","NO.","PT.","V.","VOL.");
    foreach ($integer_markers as $mark) {
      $mark = str_replace(".", "\.", $mark);
      $lc_call_no = preg_replace("/$mark(\d+)/","$mark$1;",$lc_call_no);
    } // end foreach int marker

    // Remove any inital white space
    $lc_call_no = preg_replace ("/\s*/","",$lc_call_no);

    if (preg_match("/^([A-Z]{1,3})\s*(\d+)\s*\.*(\d*)\s*\.*\s*([A-Z]*)(\d*)\s*([A-Z]*)(\d*)\s*(.*)$/",$lc_call_no,$m)) {
      $initial_letters = $m[1];
      $class_number    = $m[2];
      $decimal_number  = $m[3];
      $cutter_1_letter = $m[4];
      $cutter_1_number = $m[5];
      $cutter_2_letter = $m[6];
      $cutter_2_number = $m[7];
      $the_trimmings   = $m[8];
    } //end if call number match

    if ($class_number) {
      $class_number = sprintf("%5s", $class_number);
    }
    $decimal_number = sprintf("%-12s", $decimal_number);
    return array('initial_letters' => $initial_letters, 'class_number' => $class_number, 'decimal_number' => $decimal_number);
  }
  
  /**
    * Get fund code ids assigned to specified librarian
    *
    * @access private
    * @param int lib_id
    * @return string Comma delimited list of all fund code ids associated with specified librarian to be used in MySQL query
    *
    */
  private function get_fund_ids_by_librarian($lib_id) {
    // Connect to database
    $database = new db;
    $db       = $database->connect();
    $sql      = 'SELECT fund_id FROM funds_libs WHERE lib_id = :lib_id';
    $query = $db->prepare($sql);
    $query->bindParam(':lib_id', $lib_id);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_ASSOC);
    $db = NULL;
    $fund_ids = NULL;
    foreach($results as $result) {
      $fund_ids[] = $result['fund_id'];
    }
    return implode(',', $fund_ids);
  }
  
  /**
    * List of fund code id associated with a specified call number
    *
    * @access private
    * @param int Call number id
    * @return int Fund code id
    *
    */
  private function get_fund_ids_by_call_num($call_num_id) {
    // Connect to database
    $database = new db;
    $db       = $database->connect();
    $sql      = 'SELECT fund_id FROM call_nums WHERE id = :call_num_id LIMIT 1';
    $query = $db->prepare($sql);
    $query->bindParam(':call_num_id', $call_num_id);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_ASSOC);
    $db = NULL;
    return $results[0]['fund_id'];
  }
  
  /**
    * Call number range for a specified call number id
    *
    * @access private
    * @param int Call number id
    * @return array Start and end range for call number id
    *
    */
  private function get_call_num_range($call_num_id) {
    // Connect to database
    $database = new db;
    $db       = $database->connect();
    $sql      = 'SELECT start_range, end_range FROM call_nums WHERE id = :call_num_id LIMIT 1';
    $query = $db->prepare($sql);
    $query->bindParam(':call_num_id', $call_num_id);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_ASSOC);
    $db = NULL;
    return array('start_range' => $results[0]['start_range'], 'end_range' => $results[0]['end_range']);
  }
}
?>
