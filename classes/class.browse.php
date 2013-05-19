<?php
/**
  * Class to search OCLC Classify Web Service
  * http://classify.oclc.org/classify2/api_docs/classify.html
  *
  * @author Jared Howland <book.usage@jaredhowland.com>
  * @version 2013-05-15
  * @since 2013-05-15
  *
  */

class browse {
  private $current_year;
  private $previous_year;
  
  /**
   * Passes the content into the specified Twig template
   *
   * @access public
   * @param string Search type to perform; Valid values: standard_number, title; Default: standard_number
   * @param string|array String or array of search term(s)
   * @return array Array of title, author, isbn, call_num for all search terms
   */
  public function vendor($vendor_id) {
    $results = $this->get_vendor_usage($vendor_id);
    foreach($results as $key => $result) {
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
    return array('current_year' => $this->current_year, 'previous_year' => $this->previous_year, 'results' => $usages);
  }

  public function platform($platform_id) {
    $results = $this->get_platform_usage($platform_id);
    foreach($results as $key => $result) {
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
    return array('current_year' => $this->current_year, 'previous_year' => $this->previous_year, 'results' => $usages);
  }
  
  private function get_vendor_usage($vendor_id) {
    $current_year  = $this->get_current_year();
    $previous_year = $this->get_previous_year();
    // Connect to database
    $database = new db;
    $db       = $database->connect();
    // Query the vendors_browse view table
    $sql      = 'SELECT book_id, title, author, publisher, isbn, call_num, vendor_id, SUM(counter_usage) AS total_usage, usage_year, usage_type FROM vendors_browse WHERE vendor_id = :vendor_id AND usage_year BETWEEN :previous_year AND :current_year GROUP BY book_id, usage_type, usage_year ORDER BY title';
    $query    = $db->prepare($sql);
    $query->bindParam(':vendor_id', $vendor_id);
    $query->bindParam(':current_year', $current_year);
    $query->bindParam(':previous_year', $previous_year);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC);
    $db = NULL;
    return $results;
  }
  
  private function get_platform_usage($platform_id) {
    $current_year  = $this->get_current_year();
    $previous_year = $this->get_previous_year();
    // Connect to database
    $database = new db;
    $db       = $database->connect();
    // Query the vendors_browse view table
    $sql      = 'SELECT book_id, title, author, publisher, isbn, call_num, vendor_id, SUM(counter_usage) AS total_usage, usage_year, usage_type FROM platforms_browse WHERE platform_id = :platform_id AND usage_year BETWEEN :previous_year AND :current_year GROUP BY book_id, usage_type, usage_year ORDER BY title';
    $query    = $db->prepare($sql);
    $query->bindParam(':platform_id', $platform_id);
    $query->bindParam(':current_year', $current_year);
    $query->bindParam(':previous_year', $previous_year);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC);
    $db = NULL;
    return $results;
  }
  
  private function get_current_year() {
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
    return $this->current_year;
  }
  
  private function get_previous_year() {
    $this->previous_year = $this->current_year - 1;
    return $this->previous_year;
  }
  
}
?>
