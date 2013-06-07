<?php
/**
  * Class to search OCLC Classify Web Service
  * http://classify.oclc.org/classify2/api_docs/classify.html
  *
  * @author Jared Howland <book.usage@jaredhowland.com>
  * @version 2013-05-29
  * @since 2013-05-15
  *
  */

class browse {
  /**
   * Passes the content into the specified Twig template
   *
   * @access public
   * @param string Search type to perform; Valid values: standard_number, title; Default: standard_number
   * @param string|array String or array of search term(s)
   * @return array Array of title, author, isbn, call_num for all search terms
   */
  public function vendor($vendor_id) {
    return $this->get_vendor_usage($vendor_id);
  }

  public function platform($platform_id) {
    return $this->get_platform_usage($platform_id);
  }
  
  private function format_usage($usage) {
    foreach($usage as $key => $result) {
      // Reset variables
      $title         = NULL;
      $author        = NULL;
      $publisher     = NULL;
      $isbn          = NULL;
      $call_num      = NULL;
      $platform_list = NULL;
      $usage_year    = NULL;
      $usage_type    = NULL;
      $total_usage   = NULL;
      $latest_br1    = NULL;
      $previous_br1  = NULL;
      $latest_br2    = NULL;
      $previous_br2  = NULL;
      // Define variables
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
      foreach($result as $usage) {
        $usage_year  = $usage['usage_year'];
        $usage_type  = $usage['usage_type'];
        $total_usage = $usage['total_usage'];
        // Current usage
        if($usage_year == config::$current_year) {
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
      $usages[] = array('book_id' => $book_id, 'title' => $title, 'author' => $author, 'publisher' => $publisher, 'isbn' => $isbn, 'call_num' => $call_num, 'platforms' => $platform_list, 'latest_br1' => $latest_br1, 'previous_br1' => $previous_br1, 'latest_br2' => $latest_br2, 'previous_br2' => $previous_br2);
    }
    return array('current_year' => config::$current_year, 'previous_year' => config::$previous_year, 'results' => $usages);
  }
  
  private function get_vendor_usage($vendor_id) {
    $current_year  = config::$current_year;
    $previous_year = config::$previous_year;
    // Connect to database
    $database = new db;
    $db       = $database->connect();
    $sql      = "SELECT bv.book_id, b.title, b.author, b.publisher, b.isbn, b.call_num, CAST(GROUP_CONCAT(DISTINCT o.platforms ORDER BY o.platforms SEPARATOR '|') AS CHAR CHARSET UTF8) AS platforms, SUM(cbr1.counter_br1) AS current_br1, SUM(pbr1.counter_br1) AS previous_br1, SUM(cbr2.counter_br2) AS current_br2, SUM(pbr2.counter_br2) AS previous_br2 FROM books_vendors bv LEFT JOIN current_br1 cbr1 ON bv.book_id = cbr1.book_id LEFT JOIN previous_br1 pbr1 ON bv.book_id = pbr1.book_id LEFT JOIN current_br2 cbr2 ON bv.book_id = cbr2.book_id LEFT JOIN previous_br2 pbr2 ON bv.book_id = pbr2.book_id LEFT JOIN books b ON bv.book_id = b.id LEFT JOIN overlap o ON bv.book_id = o.book_id WHERE bv.vendor_id = :vendor_id GROUP BY bv.book_id";
    $query    = $db->prepare($sql);
    $query->bindParam(':vendor_id', $vendor_id);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC);
    $db = NULL;
    return $this->format_usage($results);
  }
  
  private function get_platform_usage($platform_id) {
    $current_year  = config::$current_year;
    $previous_year = config::$previous_year;
    // Connect to database
    $database = new db;
    $db       = $database->connect();
    // Query the platforms_browse view table
    $sql      = "SELECT bp.book_id, b.title, b.author, b.publisher, b.isbn, b.call_num, CAST(GROUP_CONCAT(DISTINCT o.platforms ORDER BY o.platforms SEPARATOR '|') AS CHAR CHARSET UTF8) AS platforms, SUM(cbr1.counter_br1) AS current_br1, SUM(pbr1.counter_br1) AS previous_br1, SUM(cbr2.counter_br2) AS current_br2, SUM(pbr2.counter_br2) AS previous_br2 FROM books_platforms bp LEFT JOIN current_br1 cbr1 ON bp.book_id = cbr1.book_id LEFT JOIN previous_br1 pbr1 ON bp.book_id = pbr1.book_id LEFT JOIN current_br2 cbr2 ON bp.book_id = cbr2.book_id LEFT JOIN previous_br2 pbr2 ON bp.book_id = pbr2.book_id LEFT JOIN books b ON bp.book_id = b.id LEFT JOIN overlap o ON bp.book_id = o.book_id WHERE bp.platform_id = :platform_id GROUP BY bp.book_id";
    $query    = $db->prepare($sql);
    $query->bindParam(':platform_id', $platform_id);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC);
    $db = NULL;
    return $this->format_usage($results);
  }
}
?>
