<?php
/**
  * Parse the temp_counter_br2 table into individual tables
  *
  * @author Jared Howland <book.usage@jaredhowland.com>
  * @version 2013-05-08
  * @since 2013-05-04
  *
  */
  
class parse {
  private $result;
  
  public function __construct() {
    $results = $this->get_data();
    foreach($results as $result) {
      $this->result     = $result;
      $book_id          = $this->update_books();
      $vendor_id        = $this->update_vendors();
      $platform_id      = $this->update_platforms($vendor_id);
      $counter_br2_id   = $this->update_counter_br2($book_id, $vendor_id, $platform_id);
      if($book_id && $vendor_id && $platform_id && $counter_br2_id) {
        $this->clean_temp_counter_br2();
      }
    }
  }
  
  private function get_data(){
    // Connect to database
    $database = new db;
    $db       = $database->connect();
    // TODO: Remove limit after done testing
    $sql      = 'SELECT id AS temp_id, title, publisher, platform, doi, proprietary_identifier, isbn, issn, counter_br2, year, vendor FROM temp_counter_br2 LIMIT 100';
    $query    = $db->prepare($sql);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_ASSOC);
    $db = NULL;
    return $results;
  }
  
  private function enhance_data() {
    $temp_id                = $this->result['temp_id'];
    $title                  = $this->result['title'];
    $publisher              = $this->result['publisher'];
    $platform               = $this->result['platform'];
    $doi                    = $this->result['doi'];
    $proprietary_identifier = $this->result['proprietary_identifier'];
    $isbn                   = $this->result['isbn'];
    $issn                   = $this->result['issn'];
    $counter_br2            = $this->result['counter_br2'];
    $year                   = $this->result['year'];
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
    $this->result = array('temp_id' => $temp_id, 'title' => $title, 'publisher' => $publisher, 'platform' => $platform, 'doi' => $doi, 'proprietary_identifier' => $proprietary_identifier, 'isbn' => $isbn, 'issn' => $issn, 'counter_br2' => $counter_br2, 'year' => $year, 'vendor' => $vendor, 'oclc_title' => $oclc_title, 'oclc_author' => $oclc_author, 'oclc_num' => $oclc_num, 'oclc_call_num' => $oclc_call_num, 'oclc_response_code' => $oclc_response_code);
    return NULL;
  }
  
  // Book table methods
  // Return int
  private function update_books() {
    $book_id = $this->find_book_id();
    if(is_null($book_id)) {
      $book_id = $this->create_book();
    }
    return $book_id;
  }
  
  // Return int or NULL
  private function find_book_id() {
    // Search by DOI, ISBN, and ISSN
    $book_id = $this->search_by_standard_number();
    // If no match, enhance data and search by OCLC number
    if(is_null($book_id) && $this->result['isbn']) {
      $book_id = $this->search_by_oclc_number();
    }
    // If still no match, search by exact title
    elseif(is_null($book_id)) {
      $book_id = $this->search_by_title();
    }
    return $book_id;
  }
  
  // Return int or NULL
  private function search_by_standard_number() {
    // Define variables
    $doi  = $this->result['doi'];
    $isbn = $this->result['isbn'];
    $issn = $this->result['issn'];
    // Normalize NULL data to search database
    if(is_null($doi))      { $doi  = 'N/A'; }
    if(is_null($isbn))     { $isbn = 'N/A'; }
    if(is_null($issn))     { $issn = 'N/A'; }
    // Connect to database
    $database = new db;
    $db    = $database->connect();
    $sql   = 'SELECT id, doi, isbn, issn FROM books WHERE doi = :doi OR isbn = :isbn OR issn = :issn LIMIT 1';
    $query = $db->prepare($sql);
    $query->bindParam(':doi', $doi);
    $query->bindParam(':isbn', $isbn);
    $query->bindParam(':issn', $issn);
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
  
  // Return int or NULL
  private function search_by_oclc_number() {
    // Only enhance data if we cannot find the book by DOI, ISBN, or ISSN
    $this->enhance_data();
    // Define variables
    $oclc_num = $this->result['oclc_num'];
    // Normalize NULL data to search database
    if(is_null($oclc_num)) { $oclc_num = 'N/A'; }
    // Connect to database
    $database = new db;
    $db    = $database->connect();
    $sql   = 'SELECT id, oclc FROM books WHERE oclc = :oclc_num LIMIT 1';
    $query = $db->prepare($sql);
    $query->bindParam(':oclc_num', $oclc_num);
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
  
  // Return int or NULL
  private function search_by_title() {
    // Define variables
    $title      = $this->result['title'];
    $oclc_title = $this->result['oclc_title'];
    // Normalize NULL data to search database
    if(is_null($title))      { $title = 'N/A'; }
    if(is_null($oclc_title)) { $oclc_title = 'N/A'; }
    // Connect to database
    $database = new db;
    $db    = $database->connect();
    $sql   = 'SELECT id, title FROM books WHERE title = :title OR title = :oclc_title LIMIT 1';
    $query = $db->prepare($sql);
    $query->bindParam(':title', $title);
    $query->bindParam(':oclc_title', $oclc_title);
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
  private function create_book() {
    $title         = $this->result['title'];
    $author        = $this->result['oclc_author'];
    $publisher     = $this->result['publisher'];
    $doi           = $this->result['doi'];
    $oclc_num      = $this->result['oclc_num'];
    $isbn          = $this->result['isbn'];
    $issn          = $this->result['issn'];
    $call_num      = $this->result['oclc_call_num'];
    $response_code = $this->result['oclc_response_code'];
    $oclc_title    = $this->result['oclc_title'];
    if($oclc_title) { $title = $oclc_title; }
    // Connect to database
    $database = new db;
    $db    = $database->connect();
    $sql   = 'INSERT INTO books (title, author, publisher, doi, oclc, isbn, issn, call_num, response_code) VALUES (:title, :author, :publisher, :doi, :oclc, :isbn, :issn, :call_num, :response_code)';
    $query = $db->prepare($sql);
    $query->bindParam(':title', $title);
    $query->bindParam(':author', $author);
    $query->bindParam(':publisher', $publisher);
    $query->bindParam(':doi', $doi);
    $query->bindParam(':oclc', $oclc_num);
    $query->bindParam(':isbn', $isbn);
    $query->bindParam(':issn', $issn);
    $query->bindParam(':call_num', $call_num);
    $query->bindParam(':response_code', $response_code);
    $query->execute();
    $book_id = $db->lastInsertId();
    $db = NULL;
    return $book_id;
  }
  
  // Vendor table methods
  private function update_vendors() {
    $vendor_id = $this->search_by_vendor();
    if(is_null($vendor_id)) {
      $vendor_id = $this->create_vendor();
    }
    return $vendor_id;
  }
  
  // Return int or NULL
  private function search_by_vendor() {
    // Define variables
    $vendor = $this->result['vendor'];
    // Normalize NULL data to search database
    if(is_null($vendor)) { $vendor = 'N/A'; }
    // Connect to database
    $database = new db;
    $db    = $database->connect();
    $sql   = 'SELECT id, vendor FROM vendors WHERE vendor = :vendor LIMIT 1';
    $query = $db->prepare($sql);
    $query->bindParam(':vendor', $vendor);
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
  private function create_vendor() {
    $vendor = $this->result['vendor'];
    // Connect to database
    $database = new db;
    $db    = $database->connect();
    $sql   = 'INSERT INTO vendors (vendor) VALUES (:vendor)';
    $query = $db->prepare($sql);
    $query->bindParam(':vendor', $vendor);
    $query->execute();
    $vendor_id = $db->lastInsertId();
    $db = NULL;
    return $vendor_id;
  }
  
  // Platform table methods
  private function update_platforms($vendor_id) {
    $platform_id = $this->search_by_platform($vendor_id);
    if(is_null($platform_id)) {
      $platform_id = $this->create_platform($vendor_id);
    }
    return $platform_id;
  }
  
  // Return int or NULL
  private function search_by_platform($vendor_id) {
    // Define variables
    $platform = $this->result['platform'];
    // Normalize NULL data to search database
    if(is_null($platform)) { $platform = 'N/A'; }
    // Connect to database
    $database = new db;
    $db    = $database->connect();
    $sql   = 'SELECT id, vendor_id, platform FROM platforms WHERE vendor_id = :vendor_id AND platform = :platform LIMIT 1';
    $query = $db->prepare($sql);
    $query->bindParam(':vendor_id', $vendor_id);
    $query->bindParam(':platform', $platform);
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
  private function create_platform($vendor_id) {
    $platform = $this->result['platform'];
    // Connect to database
    $database = new db;
    $db    = $database->connect();
    $sql   = 'INSERT INTO platforms (vendor_id, platform) VALUES (:vendor_id, :platform)';
    $query = $db->prepare($sql);
    $query->bindParam(':vendor_id', $vendor_id);
    $query->bindParam(':platform', $platform);
    $query->execute();
    $platform_id = $db->lastInsertId();
    $db = NULL;
    return $platform_id;
  }
  
  // Counter usage table methods
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

  // Return int or NULL
  private function search_by_counter_br2($book_id, $vendor_id, $platform_id) {
    // Define variables
    $year = $this->result['year'];
    // Connect to database
    $database = new db;
    $db    = $database->connect();
    $sql   = 'SELECT id, book_id, vendor_id, platform_id, year FROM counter_br2 WHERE book_id = :book_id AND vendor_id = :vendor_id AND platform_id = :platform_id AND year = :year LIMIT 1';
    $query = $db->prepare($sql);
    $query->bindParam(':book_id', $book_id);
    $query->bindParam(':vendor_id', $vendor_id);
    $query->bindParam(':platform_id', $platform_id);
    $query->bindParam(':year', $year);
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
  private function create_counter_br2($book_id, $vendor_id, $platform_id) {
    // Define variables
    $counter_br2 = $this->result['counter_br2'];
    $year          = $this->result['year'];
    // Connect to database
    $database = new db;
    $db    = $database->connect();
    $sql   = 'INSERT INTO counter_br2 (book_id, vendor_id, platform_id, year, counter_br2) VALUES (:book_id, :vendor_id, :platform_id, :year, :counter_br2)';
    $query = $db->prepare($sql);
    $query->bindParam(':book_id', $book_id);
    $query->bindParam(':vendor_id', $vendor_id);
    $query->bindParam(':platform_id', $platform_id);
    $query->bindParam(':year', $year);
    $query->bindParam(':counter_br2', $counter_br2);
    $query->execute();
    $counter_br2_id = $db->lastInsertId();
    $db = NULL;
    return $counter_br2_id;
  }
  
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
  
  // temp_counter_br2 table cleanup
  private function clean_temp_counter_br2() {
    $id = $this->result['temp_id'];
    // Connect to database
    $database = new db;
    $db    = $database->connect();
    $sql   = 'DELETE FROM temp_counter_br2 WHERE id = :id';
    $query = $db->prepare($sql);
    $query->bindParam(':id', $id);
    $query->execute();
    $db = NULL;
    return NULL;
  }
  
}

?>
