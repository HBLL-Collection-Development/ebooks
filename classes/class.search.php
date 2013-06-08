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
   * Constructor
   *
   * @access public
   * @param int book_id
   * @return null
   */
  public function __construct($term) {
    $this->term = $term;
  }
  
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
      $title         = NULL;
      $author        = NULL;
      $publisher     = NULL;
      $isbn          = NULL;
      $call_num      = NULL;
      $platforms     = NULL;
      $platform_list = NULL;
      $current_br1    = NULL;
      $previous_br1  = NULL;
      $current_br2    = NULL;
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

  public function format_vendors() {
    $html = NULL;
    foreach($this->get_vendors() as $vendor) {
      $vendor_id = $vendor['id'];
      $vendor    = $vendor['vendor'];
      $html     .= '<option value="' . $vendor_id . '">' . $vendor . '</option>';
    }
    return $html;
  }

  public function format_platforms() {
    $html = NULL;
    foreach($this->get_platforms() as $platform) {
      $platform_id = $platform['platform_id'];
      $vendor      = $platform['vendor'];
      $platform    = $platform['platform'];
      $html       .= '<option value="' . $platform_id . '">' . $platform . ' (' . $vendor . ')</option>';
    }
    return $html;
  }
  
}
?>
