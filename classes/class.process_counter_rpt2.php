<?php
/**
  * Process and parse the temp_counter_br2 table into individual tables
  * http://stackoverflow.com/questions/2708237/php-mysql-transactions-examples
  *
  * @author Jared Howland <book.usage@jaredhowland.com>
  * @version 2013-06-04
  * @since 2013-05-14
  *
  */
  
class process_counter_rpt2 extends process {
  protected $result;
  
  /**
    * Constructor; Wraps everything in a transaction so that rows are not deleted until they are properly parsed into correct tables
    *
    * @access public
    * @param NULL
    * @return NULL
    *
    */
  public function __construct() {
    // Connect to database
    $database = new db;
    $db       = $database->connect();
    $db->beginTransaction();
    try {
      // Limit to config::PROCESS_LIMIT so that system is not overwhelmed
      $sql      = 'SELECT id AS temp_id, title, publisher, platform, doi, proprietary_identifier, isbn, issn, counter_br2, usage_year, vendor FROM temp_counter_br2 LIMIT ' . config::PROCESS_LIMIT . ' FOR UPDATE';
      $query    = $db->prepare($sql);
      $query->execute();
      $results = $query->fetchAll(PDO::FETCH_ASSOC);
      foreach($results as $result) {
        $this->result       = $result;
        $book_id            = $this->update_books();
        $vendor_id          = $this->update_vendors();
        $books_vendors_id   = $this->update_books_vendors($book_id, $vendor_id);
        $platform_id        = $this->update_platforms($vendor_id);
        $books_platforms_id = $this->update_books_platforms($book_id, $platform_id);
        $counter_br2_id     = $this->update_counter_br2($book_id, $vendor_id, $platform_id);
        if($book_id && $vendor_id && $platform_id && $counter_br2_id) {
          $this->clean_temp_counter_br2($db);
        } else {
          $error = 'There was a mistake in processing the loaded data. Contact the administrator to determine the exact problem.<br/>book_id: ' . $book_id . '<br/>vendor_id: ' . $vendor_id . '<br/>platform_id: ' . $platform_id . '<br/>counter_br2_id: ' . $counter_br2_id;
          throw new Exception($error);
        }
      }
    $db->commit();
    $db = NULL;
    } catch (Exception $e) {
      echo 'Caught exception: ',  $e->getMessage(), "\n";
      $db->rollback();
      $db = NULL;
      exit;
    }
  }
  
  /**
    * Get data from the temp_counter_br2 table
    * Limit to number of records specified in config
    *
    * @access private
    * @param NULL
    * @return array Results of database query
    *
    */
  private function get_data(){
    // Connect to database
    $database = new db;
    $db       = $database->connect();
    // Limited to config::PROCESS_LIMIT so that system is not overwhelmed
    $sql      = 'SELECT id AS temp_id, title, publisher, platform, doi, proprietary_identifier, isbn, issn, counter_br2, usage_year, vendor FROM temp_counter_br2 LIMIT ' . config::PROCESS_LIMIT;
    $query    = $db->prepare($sql);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_ASSOC);
    $db = NULL;
    return $results;
  }
  
  /**
    * Enhance the data if it is not already in the database using OCLC’s Classify service
    * Places the enhanced data into the $this->result variable for future use
    *
    * @access protected
    * @param NULL
    * @return NULL
    *
    */
  protected function enhance_data() {
    $temp_id                = $this->result['temp_id'];
    $title                  = $this->result['title'];
    $publisher              = $this->result['publisher'];
    $platform               = $this->result['platform'];
    $doi                    = $this->result['doi'];
    $proprietary_identifier = $this->result['proprietary_identifier'];
    $isbn                   = $this->result['isbn'];
    $issn                   = $this->result['issn'];
    $counter_br2            = $this->result['counter_br2'];
    $usage_year             = $this->result['usage_year'];
    $vendor                 = $this->result['vendor'];
    // Enhance using OCLC Classify service
    if(!is_null($isbn)) {
      $classify           = new classify;
      $oclc               = $classify->search('standard_number', $isbn);

      $oclc_title         = $oclc[0]['title'];
      $oclc_author        = $oclc[0]['author'];
      $oclc_num           = $oclc[0]['oclc'];
      $oclc_call_num      = $oclc[0]['call_num'];
      $oclc_response_code = $oclc[0]['response_code'];
    } else {
      $oclc_title         = NULL;
      $oclc_author        = NULL;
      $oclc_num           = NULL;
      $oclc_call_num      = NULL;
      $oclc_response_code = NULL;
    }
    $classify = NULL;
    // Find fund_id if there is a call number for the book
    if(!is_null($oclc_call_num)) {
      $fund_id = $this->get_fund_id($oclc_call_num);
    } else {
      $fund_id = NULL;
    }
    $this->result = array('temp_id' => $temp_id, 'title' => $title, 'publisher' => $publisher, 'platform' => $platform, 'doi' => $doi, 'proprietary_identifier' => $proprietary_identifier, 'isbn' => $isbn, 'issn' => $issn, 'counter_br2' => $counter_br2, 'usage_year' => $usage_year, 'vendor' => $vendor, 'oclc_title' => $oclc_title, 'oclc_author' => $oclc_author, 'oclc_num' => $oclc_num, 'oclc_call_num' => $oclc_call_num, 'oclc_response_code' => $oclc_response_code, 'fund_id' => $fund_id);
    return NULL;
  }

  /**
    * Update the `counter_br2` table
    *
    * @access private
    * @param int book_id
    * @param int vendor_id
    * @param int platform_id
    * @return int Newly created or updated counter_br2_id
    *
    */
  private function update_counter_br2($book_id, $vendor_id, $platform_id) {
    $counter_br2_id = $this->search_by_counter_br2($book_id, $vendor_id, $platform_id);
    if(is_null($counter_br2_id)) {
      $counter_br2_id = $this->create_counter_br2($book_id, $vendor_id, $platform_id);
    } else {
      // If this usage already exists, then this is an updated usage report
      // Overwrite the old usage number with this new number
      $counter_br2_id = $this->overwrite_counter_br2($counter_br2_id);
    }
    return $counter_br2_id;
  }

  /**
    * Search to find if usage data has already been loaded for this platform and year
    *
    * @access private
    * @param int book_id
    * @param int vendor_id
    * @param int platform_id
    * @return mixed counter_br2_id if exists; NULL otherwise
    *
    */
  private function search_by_counter_br2($book_id, $vendor_id, $platform_id) {
    // Define variables
    $usage_year = $this->result['usage_year'];
    // Connect to database
    $database = new db;
    $db    = $database->connect();
    $sql   = 'SELECT id, book_id, vendor_id, platform_id, usage_year FROM counter_br2 WHERE book_id = :book_id AND vendor_id = :vendor_id AND platform_id = :platform_id AND usage_year = :usage_year LIMIT 1';
    $query = $db->prepare($sql);
    $query->bindParam(':book_id', $book_id);
    $query->bindParam(':vendor_id', $vendor_id);
    $query->bindParam(':platform_id', $platform_id);
    $query->bindParam(':usage_year', $usage_year);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_ASSOC);
    $db = NULL;
    // Return id if it exists in the database
    if(count($results) > 0) {
      return $results[0]['id'];
    } else {
      return NULL;
    }
  }
  
  /**
    * Create usage entry if it does not already exist
    *
    * @access private
    * @param int book_id
    * @param int vendor_id
    * @param int platform_id
    * @return int counter_br2_id
    *
    */
  private function create_counter_br2($book_id, $vendor_id, $platform_id) {
    // Define variables
    $counter_br2 = $this->result['counter_br2'];
    $usage_year  = $this->result['usage_year'];
    // Connect to database
    $database = new db;
    $db    = $database->connect();
    $sql   = 'INSERT INTO counter_br2 (book_id, vendor_id, platform_id, usage_year, counter_br2) VALUES (:book_id, :vendor_id, :platform_id, :usage_year, :counter_br2)';
    $query = $db->prepare($sql);
    $query->bindParam(':book_id', $book_id);
    $query->bindParam(':vendor_id', $vendor_id);
    $query->bindParam(':platform_id', $platform_id);
    $query->bindParam(':usage_year', $usage_year);
    $query->bindParam(':counter_br2', $counter_br2);
    $query->execute();
    $counter_br2_id = $db->lastInsertId();
    $db = NULL;
    return $counter_br2_id;
  }
  
  /**
    * If usage data has already been loaded for this platform and year, overwrite it with this new, presumably, updated data
    *
    * @access private
    * @param int counter_br2_id
    * @return int counter_br2_id
    *
    */
  private function overwrite_counter_br2($counter_br2_id) {
    // Define variables
    $counter_br2 = $this->result['counter_br2'];
    // Connect to database
    $database = new db;
    $db    = $database->connect();
    $sql   = 'UPDATE counter_br2 SET counter_br2 = :counter_br2 WHERE id = :counter_br2_id';
    $query = $db->prepare($sql);
    $query->bindParam(':counter_br2', $counter_br2);
    $query->bindParam(':counter_br2_id', $counter_br2_id);
    $query->execute();
    $db = NULL;
    return $counter_br2_id;
  }
  
  /**
    * Delete row from temp table once all data has properly parsed and been placed in individual tables
    *
    * @access private
    * @param object Database connection details
    * @return NULL
    *
    */
  private function clean_temp_counter_br2($db) {
    $id    = $this->result['temp_id'];
    $sql   = 'DELETE FROM temp_counter_br2 WHERE id = :id';
    $query = $db->prepare($sql);
    $query->bindParam(':id', $id);
    $query->execute();
  }
  
}

?>
