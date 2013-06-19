<?php
/**
  * Class to search for a book
  *
  * @author Jared Howland <book.usage@jaredhowland.com>
  * @version 2013-05-29
  * @since 2013-05-29
  *
  */

class search {
  private $term;
  
  /**
   * Constructor; Sets $this->term variable for term to search for in database
   *
   * @access public
   * @param string Search terms to search for
   * @return null
   */
  public function __construct($term) {
    $this->term = $term;
  }
  
  /**
    * Search by title
    *
    * @access public
    * @param NULL
    * @return array Formatted usage data for results
    *
    */
  public function title() {
    // Connect to database
    $database = new db;
    $db       = $database->connect();
    $term     = $db->quote($this->term);
    $sql      = "SELECT b.id, b.title, b.author, b.publisher, b.isbn, b.call_num, CAST(GROUP_CONCAT(DISTINCT o.platforms ORDER BY o.platforms SEPARATOR '|') AS CHAR CHARSET UTF8) AS platforms, (SELECT SUM(cbr2.counter_br2) FROM current_br2 cbr2 WHERE cbr2.book_id = b.id) AS current_br2, (SELECT SUM(pbr2.counter_br2) FROM previous_br2 pbr2 WHERE pbr2.book_id = b.id) AS previous_br2, (SELECT SUM(cbr1.counter_br1) FROM current_br1 cbr1 WHERE cbr1.book_id = b.id) AS current_br1, (SELECT SUM(pbr1.counter_br1) FROM previous_br1 pbr1 WHERE pbr1.book_id = b.id) AS previous_br1 FROM books AS b LEFT JOIN overlap o ON b.id = o.book_id WHERE id IN (SELECT id FROM books_search WHERE MATCH (title) AGAINST (" . $term . " IN BOOLEAN MODE) ORDER BY title) GROUP BY b.id ORDER BY b.title";
    $query    = $db->prepare($sql);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC);
    $db = NULL;
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
  public function isbn() {
    $isbn          = $this->validate_standard_number($this->term);
    $in            = $this->get_related($isbn);
    if($in == 'invalidId') {
      $in = '0000000000';
    }
    // Connect to database
    $database = new db;
    $db       = $database->connect();
    $sql      = "SELECT b.id, b.title, b.author, b.publisher, b.isbn, b.call_num, CAST(GROUP_CONCAT(DISTINCT o.platforms ORDER BY o.platforms SEPARATOR '|') AS CHAR CHARSET UTF8) AS platforms, (SELECT SUM(cbr2.counter_br2) FROM current_br2 cbr2 WHERE cbr2.book_id = b.id) AS current_br2, (SELECT SUM(pbr2.counter_br2) FROM previous_br2 pbr2 WHERE pbr2.book_id = b.id) AS previous_br2, (SELECT SUM(cbr1.counter_br1) FROM current_br1 cbr1 WHERE cbr1.book_id = b.id) AS current_br1, (SELECT SUM(pbr1.counter_br1) FROM previous_br1 pbr1 WHERE pbr1.book_id = b.id) AS previous_br1 FROM books AS b LEFT JOIN overlap o ON b.id = o.book_id WHERE b.isbn IN (" . $in . ") GROUP BY b.id ORDER BY b.title";
    $query    = $db->prepare($sql);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC);
    $db = NULL;
    return $this->format_usage($results);
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
      $previous_br1 = $result[0]['previous_br1'];
      $current_br2   = $result[0]['current_br2'];
      $previous_br2 = $result[0]['previous_br2'];
      if(is_null($current_br1) AND is_null($current_br2) AND is_null($previous_br1) AND is_null($previous_br2)) {
        // Do not add to $usages array if there is no usage in the past 2 years
      } else {
      $usages[] = array('book_id' => $book_id, 'title' => $title, 'author' => $author, 'publisher' => $publisher, 'isbn' => $isbn, 'call_num' => $call_num, 'platforms' => $platform_list, 'latest_br1' => $current_br1, 'previous_br1' => $previous_br1, 'latest_br2' => $current_br2, 'previous_br2' => $previous_br2);
      }
    }
    return array('current_year' => config::$current_year, 'previous_year' => config::$previous_year, 'search_term' => htmlspecialchars($this->term), 'results' => $usages);
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
    * List of all vendors in database
    *
    * @access public
    * @param NULL
    * @return array List of all vendors in database
    *
    */
  public function get_vendors() {
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
    * List of all platforms in database
    *
    * @access public
    * @param NULL
    * @return array List of all platforms in database
    *
    */
  public function get_platforms() {
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
    * List of all subject librarians in database
    *
    * @access public
    * @param NULL
    * @return array List of all subject librarians in database
    *
    */
  public function get_libs() {
    // Connect to database
    $database = new db;
    $db    = $database->connect();
    // Only show librarians that actually have books associated with them
    $sql   = 'SELECT DISTINCT fl.lib_id, l.first_name, l.last_name FROM funds_libs fl, libs l WHERE fl.lib_id = l.id AND fl.fund_id IN (SELECT DISTINCT fund_id FROM books WHERE fund_id IS NOT NULL AND fund_id != 0) ORDER BY l.last_name';
    $query = $db->prepare($sql);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_ASSOC);
    $db = NULL;
    return $results;
  }
  
  /**
    * List of fund codes
    *
    * @access public
    * @param NULL
    * @return array List of fund codes
    *
    */
  public function get_funds() {
    // Connect to database
    $database = new db;
    $db    = $database->connect();
    // Only show fund codes that actually include books
    $sql   = 'SELECT id AS fund_id, fund_code, fund_name FROM funds WHERE id IN (SELECT DISTINCT fund_id FROM books WHERE fund_id IS NOT NULL AND fund_id != 0) ORDER BY fund_code ASC';
    $query = $db->prepare($sql);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_ASSOC);
    $db = NULL;
    return $results;
  }

  /**
    * List of call number ranges
    *
    * @access public
    * @param NULL
    * @return array List of call number ranges
    *
    */
  function get_call_nums() {
    // Connect to database
    $database = new db;
    $db    = $database->connect();
    // Only show call number ranges that actually include books
    $sql   = 'SELECT id AS call_num_id, start_range AS call_num_start, end_range AS call_num_end FROM call_nums WHERE fund_id IN (SELECT DISTINCT fund_id FROM books WHERE fund_id IS NOT NULL AND fund_id != 0) ORDER BY start_range ASC';
    $query = $db->prepare($sql);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_ASSOC);
    $db = NULL;
    return $results;
  }

  /**
    * Format vendors array for use in HTML form in Twig template
    *
    * @access public
    * @param NULL
    * @return string HTML for form in Twig template
    *
    */
  public function format_vendors($active_vendor_id = NULL) {
    $html = NULL;
    foreach($this->get_vendors() as $vendor) {
      $vendor_id = $vendor['id'];
      $vendor    = $vendor['vendor'];
      if($active_vendor_id == $vendor_id) {
        $html .= '<option value="' . $vendor_id . '" selected="selected">' . $vendor . '</option>';
      } else {
        $html .= '<option value="' . $vendor_id . '">' . $vendor . '</option>';
      }
    }
    return $html;
  }

  /**
    * Format platforms for use in HTML form in Twig template
    *
    * @access public
    * @param NULL
    * @return string HTML for form in Twig template
    *
    */
  public function format_platforms($active_platform_id = NULL) {
    $html = NULL;
    foreach($this->get_platforms() as $platform) {
      $platform_id = $platform['platform_id'];
      $vendor      = $platform['vendor'];
      $platform    = $platform['platform'];
      if($active_platform_id == $platform_id) {
        $html .= '<option value="' . $platform_id . '" selected="selected">' . $platform . ' (' . $vendor . ')</option>';
      } else {
        $html .= '<option value="' . $platform_id . '">' . $platform . ' (' . $vendor . ')</option>';
      }
    }
    return $html;
  }
  
  /**
    * Format subject librarians for HTML form
    *
    * @access public
    * @param NULL
    * @return string HTML for drop-down form of all subject librarians
    *
    */
  public function format_libs($active_lib_id = NULL) {
    $html = NULL;
    foreach($this->get_libs() as $lib) {
      $lib_id     = $lib['lib_id'];
      $first_name = $lib['first_name'];
      $last_name  = $lib['last_name'];
      if($active_lib_id == $lib_id) {
        $html .= '<option value="' . $lib_id . '" selected="selected">' . $first_name . ' ' . $last_name . '</option>';
      } else {
        $html .= '<option value="' . $lib_id . '">' . $first_name . ' ' . $last_name . '</option>';
      }
    }
    return $html;
  }

  /**
    * Format fund names for HTML form
    *
    * @access public
    * @param NULL
    * @return string HTML for drop-down form of all fund codes
    *
    */
  public function format_funds($active_fund_id = NULL) {
    $html = NULL;
    foreach($this->get_funds() as $fund) {
      $fund_id   = $fund['fund_id'];
      $fund_code = $fund['fund_code'];
      $fund_name = $fund['fund_name'];
      if($active_fund_id == $fund_id) {
        $html .= '<option value="' . $fund_id . '" selected="selected">' . $fund_code . ' (' . $fund_name . ')</option>';
      } else {
        $html .= '<option value="' . $fund_id . '">' . $fund_code . ' (' . $fund_name . ')</option>';
      }
    }
    return $html;
  }

  /**
    * Format call number ranges for HTML form
    *
    * @access public
    * @param NULL
    * @return string HTML for drop-down form of all fund codes
    *
    */
  public function format_call_nums($active_call_num_id = NULL) {
    $html = NULL;
    foreach($this->get_call_nums() as $call_num) {
      $call_num_id    = $call_num['call_num_id'];
      $call_num_start = $call_num['call_num_start'];
      $call_num_end   = $call_num['call_num_end'];
      if($call_num_start === $call_num_end) {
        $call_number = $call_num_start;
      } else {
        $call_number = $call_num_start . '–' . $call_num_end;
      }
      if($active_call_num_id == $call_num_id) {
        $html .= '<option value="' . $call_num_id . '" selected="selected">' . $call_number . '</option>';
      } else {
        $html .= '<option value="' . $call_num_id . '">' . $call_number . '</option>';
      }
    }
    return $html;
  }
}
?>
