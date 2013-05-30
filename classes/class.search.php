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
  private $current_year;
  private $previous_year;
  private $term;
  
  /**
   * Constructor
   *
   * @access public
   * @param int book_id
   * @return null
   */
  public function __construct($term) {
    $this->set_current_year();
    $this->set_previous_year();
    $this->term = $term;
  }
  
  public function title() {
    $previous_year = $this->get_previous_year();
    $current_year  = $this->get_current_year();
    // Connect to database
    $database = new db;
    $db       = $database->connect();
    $term     = $db->quote($this->term);
    $sql      = "SELECT b.id, b.title, b.author, b.publisher, b.isbn, b.call_num, SUM(cu.counter_usage) AS total_usage, cu.usage_type, cu.usage_year FROM books AS b INNER JOIN counter_usage AS cu ON b.id = cu.book_id WHERE id IN (SELECT id FROM books_search WHERE MATCH(title) AGAINST($term IN BOOLEAN MODE) ORDER BY title) AND cu.usage_year BETWEEN :previous_year AND :current_year GROUP BY b.id, cu.usage_type, cu.usage_year ORDER BY b.title";
    $query    = $db->prepare($sql);
    $query->bindParam(':previous_year', $previous_year);
    $query->bindParam(':current_year', $current_year);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC);
    $db = NULL;
    return $this->format_usage($results);
  }

  public function isbn() {
    $isbn          = $this->validate_standard_number($this->term);
    $in            = $this->get_related($isbn);
    if($in == 'invalidId') {
      $in = '0000000000';
    }
    $previous_year = $this->get_previous_year();
    $current_year  = $this->get_current_year();
    // Connect to database
    $database = new db;
    $db       = $database->connect();
    $sql      = 'SELECT b.id, b.title, b.author, b.publisher, b.isbn, b.call_num, SUM(cu.counter_usage) AS total_usage, cu.usage_type, cu.usage_year FROM books AS b INNER JOIN counter_usage AS cu ON b.id = cu.book_id WHERE b.isbn IN (' . $in . ') AND cu.usage_year BETWEEN :previous_year AND :current_year GROUP BY b.id, cu.usage_type, cu.usage_year ORDER BY b.title';
    $query    = $db->prepare($sql);
    $query->bindParam(':previous_year', $previous_year);
    $query->bindParam(':current_year', $current_year);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC);
    $db = NULL;
    return $this->format_usage($results);
  }
  
  private function get_related($isbn) {
    return $this->format_xisbn($isbn);
  }
  
  private function format_xisbn($isbn) {
    $xisbn = new xisbn;
    $related_isbns = $xisbn->get_isbns($isbn);
    return implode(',',$related_isbns);
  }
  
  private function format_usage($usage) {
    foreach($usage as $key => $result) {
      // Reset variables
      $title        = NULL;
      $author       = NULL;
      $publisher    = NULL;
      $isbn         = NULL;
      $call_num     = NULL;
      $usage_year   = NULL;
      $usage_type   = NULL;
      $total_usage  = NULL;
      $latest_br1   = NULL;
      $previous_br1 = NULL;
      $latest_br2   = NULL;
      $previous_br2 = NULL;
      $book_id   = $key;
      $title     = $result[0]['title'];
      $author    = $result[0]['author'];
      $publisher = $result[0]['publisher'];
      $isbn      = $result[0]['isbn'];
      $call_num  = $result[0]['call_num'];
      foreach($result as $usage) {
        $usage_year  = $usage['usage_year'];
        $usage_type  = $usage['usage_type'];
        $total_usage = $usage['total_usage'];
        // Current usage
        if($usage_year == $this->current_year) {
          if($usage_type == 'br1') {
            $latest_br1 = $total_usage;
          } else {
            $latest_br2 = $total_usage;
          }
        // Previous usage
        } else {
          if($usage_type == 'br1') {
            $previous_br1 = $total_usage;
          } else {
            $previous_br2 = $total_usage;
          }
        }
      }
      $usages[] = array('book_id' => $book_id, 'title' => $title, 'author' => $author, 'publisher' => $publisher, 'isbn' => $isbn, 'call_num' => $call_num, 'latest_br1' => $latest_br1, 'previous_br1' => $previous_br1, 'latest_br2' => $latest_br2, 'previous_br2' => $previous_br2);
    }
    return array('current_year' => $this->current_year, 'previous_year' => $this->previous_year, 'search_term' => htmlspecialchars($this->term), 'results' => $usages);
  }
  
  private function set_current_year() {
    // Connect to database
    $database = new db;
    $db       = $database->connect();
    $sql      = 'SELECT MAX(usage_year) AS current_year FROM counter_usage';
    $query    = $db->prepare($sql);
    $query->bindParam(':vendor_id', $vendor_id);
    $query->execute();
    $current_year = $query->fetchAll(PDO::FETCH_ASSOC);
    $db = NULL;
    $this->current_year = $current_year[0]['current_year'];
  }
  
  private function get_current_year() {
    return $this->current_year;
  }
  
  private function set_previous_year() {
    $this->previous_year = $this->current_year - 1;
  }
  
  private function get_previous_year() {
    return $this->previous_year;
  }
  
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
   * @return string
   */
  protected function strip_non_numeric($string) {
    return preg_replace('{[^0-9X]}', '', strtoupper($string));
  }
  
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
  
  protected function is_isbn_valid($isbn) {
    if (!is_string($isbn) && !is_int($isbn)) {
      return false;
    }
    $isbn = (string) $isbn;
    // ISBN-10
    if(strlen($isbn) == 10) {
      // Sum
      $sum    = 0;
      for ($i = 0; $i < 9; $i++) {
        $sum += (10 - $i) * $isbn;
      }
      // Checksum
      $checksum = 11 - ($sum % 11);
      if ($checksum == 11) {
        $checksum = '0';
      } elseif ($checksum == 10) {
        $checksum = 'X';
      }
    // ISBN-13
    } elseif(strlen($isbn) == 13) {
      // Sum
      $sum    = 0;
      for ($i = 0; $i < 12; $i++) {
        if ($i % 2 == 0) {
          $sum += $isbn;
        } else {
          $sum += 3 * $isbn;
        }
      }
      // Checksum
      $checksum = 10 - ($sum % 10);
      if ($checksum == 10) {
        $checksum = '0';
      }
    // Invalid ISBN
    } else {
      return FALSE;
    }
    // Validate
    if (substr($isbn, -1) != $checksum) {
      return FALSE;
    }
    return TRUE;
  }
  
}
?>
