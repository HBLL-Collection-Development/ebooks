<?php
/**
  * Process and parse the temp_counter_br1 table into individual tables
  *
  * @author Jared Howland <book.usage@jaredhowland.com>
  * @version 2013-05-29
  * @since 2013-05-14
  *
  */
  
class process_counter_rpt1 extends process {
  protected $result;
  
  public function __construct() {
    $results = $this->get_data();
    foreach($results as $result) {
      $this->result     = $result;
      $book_id          = $this->update_books();
      $vendor_id        = $this->update_vendors();
      $books_vendors_id   = $this->update_books_vendors($book_id, $vendor_id);
      $platform_id      = $this->update_platforms($vendor_id);
      $books_platforms_id = $this->update_books_platforms($book_id, $platform_id);
      $counter_br1_id   = $this->update_counter_br1($book_id, $vendor_id, $platform_id);
      if($book_id && $vendor_id && $platform_id && $counter_br1_id) {
        $this->clean_temp_counter_br1();
      }
    }
  }
  
  private function get_data(){
    // Connect to database
    $database = new db;
    $db       = $database->connect();
    // Limited to config::PROCESS_LIMIT so that system is not overwhelmed
    $sql      = 'SELECT id AS temp_id, title, publisher, platform, doi, proprietary_identifier, isbn, issn, counter_br1, usage_year, vendor FROM temp_counter_br1 LIMIT ' . config::PROCESS_LIMIT;
    $query    = $db->prepare($sql);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_ASSOC);
    $db = NULL;
    return $results;
  }
  
  protected function enhance_data() {
    $temp_id                = $this->result['temp_id'];
    $title                  = $this->result['title'];
    $publisher              = $this->result['publisher'];
    $platform               = $this->result['platform'];
    $doi                    = $this->result['doi'];
    $proprietary_identifier = $this->result['proprietary_identifier'];
    $isbn                   = $this->result['isbn'];
    $issn                   = $this->result['issn'];
    $counter_br1            = $this->result['counter_br1'];
    $usage_year             = $this->result['usage_year'];
    $vendor                 = $this->result['vendor'];
    if(!is_null($isbn)) {
      $classify = new classify;
      $oclc     = $classify->search('standard_number', $isbn);
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
    $this->result = array('temp_id' => $temp_id, 'title' => $title, 'publisher' => $publisher, 'platform' => $platform, 'doi' => $doi, 'proprietary_identifier' => $proprietary_identifier, 'isbn' => $isbn, 'issn' => $issn, 'counter_br1' => $counter_br1, 'usage_year' => $usage_year, 'vendor' => $vendor, 'oclc_title' => $oclc_title, 'oclc_author' => $oclc_author, 'oclc_num' => $oclc_num, 'oclc_call_num' => $oclc_call_num, 'oclc_response_code' => $oclc_response_code);
    return NULL;
  }

  // Counter usage table methods
  private function update_counter_br1($book_id, $vendor_id, $platform_id) {
    $counter_br1_id = $this->search_by_counter_br1($book_id, $vendor_id, $platform_id);
    if(is_null($counter_br1_id)) {
      $counter_br1_id = $this->create_counter_br1($book_id, $vendor_id, $platform_id);
    } else {
      // If this usage already exists, then this is an updated usage report
      // Overwrite the old usage number with this new number
      $counter_br1_id = $this->overwrite_counter_br1($counter_br1_id);
    }
    return $counter_br1_id;
  }

  // Return int or NULL
  private function search_by_counter_br1($book_id, $vendor_id, $platform_id) {
    // Define variables
    $usage_year = $this->result['usage_year'];
    // Connect to database
    $database = new db;
    $db    = $database->connect();
    $sql   = 'SELECT id, book_id, vendor_id, platform_id, usage_year FROM counter_br1 WHERE book_id = :book_id AND vendor_id = :vendor_id AND platform_id = :platform_id AND usage_year = :usage_year LIMIT 1';
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
  
  // Return int
  private function create_counter_br1($book_id, $vendor_id, $platform_id) {
    // Define variables
    $counter_br1 = $this->result['counter_br1'];
    $usage_year  = $this->result['usage_year'];
    // Connect to database
    $database = new db;
    $db    = $database->connect();
    $sql   = 'INSERT INTO counter_br1 (book_id, vendor_id, platform_id, usage_year, counter_br1) VALUES (:book_id, :vendor_id, :platform_id, :usage_year, :counter_br1)';
    $query = $db->prepare($sql);
    $query->bindParam(':book_id', $book_id);
    $query->bindParam(':vendor_id', $vendor_id);
    $query->bindParam(':platform_id', $platform_id);
    $query->bindParam(':usage_year', $usage_year);
    $query->bindParam(':counter_br1', $counter_br1);
    $query->execute();
    $counter_br1_id = $db->lastInsertId();
    $db = NULL;
    return $counter_br1_id;
  }
  
  private function overwrite_counter_br1($counter_br1_id) {
    // Define variables
    $counter_br1 = $this->result['counter_br1'];
    // Connect to database
    $database = new db;
    $db    = $database->connect();
    $sql   = 'UPDATE counter_br1 SET counter_br1 = :counter_br1 WHERE id = :counter_br1_id';
    $query = $db->prepare($sql);
    $query->bindParam(':counter_br1', $counter_br1);
    $query->bindParam(':counter_br1_id', $counter_br1_id);
    $query->execute();
    $db = NULL;
    return $counter_br1_id;
  }
  
  // temp_counter_br1 table cleanup
  private function clean_temp_counter_br1() {
    $id = $this->result['temp_id'];
    // Connect to database
    $database = new db;
    $db    = $database->connect();
    $sql   = 'DELETE FROM temp_counter_br1 WHERE id = :id';
    $query = $db->prepare($sql);
    $query->bindParam(':id', $id);
    $query->execute();
    $db = NULL;
    return NULL;
  }
  
}

?>
