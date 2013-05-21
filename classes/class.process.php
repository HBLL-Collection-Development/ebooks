<?php
/**
  * Process and parse the temp_counter tables into individual tables
  *
  * @author Jared Howland <book.usage@jaredhowland.com>
  * @version 2013-05-14
  * @since 2013-05-04
  *
  */
  
class process {
  // Book table methods
  // Return int
  protected function update_books() {
    $book_id = $this->find_book_id();
    if(is_null($book_id)) {
      $book_id = $this->create_book();
    }
    return $book_id;
  }
  
  // Return int or NULL
  protected function find_book_id() {
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
  protected function search_by_standard_number() {
    // Define variables
    $doi  = $this->result['doi'];
    $isbn = $this->result['isbn'];
    $issn = $this->result['issn'];
    // Normalize NULL data to search database
    if(is_null($doi))  { $doi  = 'N/A'; }
    if(is_null($isbn)) { $isbn = 'N/A'; }
    if(is_null($issn)) { $issn = 'N/A'; }
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
  protected function search_by_oclc_number() {
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
  protected function search_by_title() {
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
  protected function create_book() {
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
  protected function update_vendors() {
    $vendor_id = $this->search_by_vendor();
    if(is_null($vendor_id)) {
      $vendor_id = $this->create_vendor();
    }
    return $vendor_id;
  }
  
  // Return int or NULL
  protected function search_by_vendor() {
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
  protected function create_vendor() {
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
  
  protected function update_books_vendors($book_id, $vendor_id) {
    $book_vendor_id = $this->search_by_book_vendor_id($book_id, $vendor_id);
    if(is_null($book_vendor_id)) {
      $book_vendor_id = $this->create_book_vendor($book_id, $vendor_id);
    }
    return $book_vendor_id;
  }
  
  protected function search_by_book_vendor_id($book_id, $vendor_id) {
    // Connect to database
    $database = new db;
    $db    = $database->connect();
    $sql   = 'SELECT id, vendor_id, book_id FROM books_vendors WHERE vendor_id = :vendor_id AND book_id = :book_id LIMIT 1';
    $query = $db->prepare($sql);
    $query->bindParam(':vendor_id', $vendor_id);
    $query->bindParam(':book_id', $book_id);
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
  
  protected function create_book_vendor($book_id, $vendor_id) {
    // Connect to database
    $database = new db;
    $db    = $database->connect();
    $sql   = 'INSERT INTO books_vendors (book_id, vendor_id) VALUES (:book_id, :vendor_id)';
    $query = $db->prepare($sql);
    $query->bindParam(':book_id', $book_id);
    $query->bindParam(':vendor_id', $vendor_id);
    $query->execute();
    $book_vendor_id = $db->lastInsertId();
    $db = NULL;
    return $book_vendor_id;
  }
  
  // Platform table methods
  protected function update_platforms($vendor_id) {
    $platform_id = $this->search_by_platform($vendor_id);
    if(is_null($platform_id)) {
      $platform_id = $this->create_platform($vendor_id);
    }
    return $platform_id;
  }
  
  // Return int or NULL
  protected function search_by_platform($vendor_id) {
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
  protected function create_platform($vendor_id) {
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
  
  protected function update_books_platforms($book_id, $platform_id) {
    $book_platform_id = $this->search_by_book_platform_id($book_id, $platform_id);
    if(is_null($book_platform_id)) {
      $book_platform_id = $this->create_book_platform($book_id, $platform_id);
    }
    return $book_platform_id;
  }
  
  protected function search_by_book_platform_id($book_id, $platform_id) {
    // Connect to database
    $database = new db;
    $db    = $database->connect();
    $sql   = 'SELECT id, platform_id, book_id FROM books_platforms WHERE platform_id = :platform_id AND book_id = :book_id LIMIT 1';
    $query = $db->prepare($sql);
    $query->bindParam(':platform_id', $platform_id);
    $query->bindParam(':book_id', $book_id);
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
  
  protected function create_book_platform($book_id, $platform_id) {
    // Connect to database
    $database = new db;
    $db    = $database->connect();
    $sql   = 'INSERT INTO books_platforms (book_id, platform_id) VALUES (:book_id, :platform_id)';
    $query = $db->prepare($sql);
    $query->bindParam(':book_id', $book_id);
    $query->bindParam(':platform_id', $platform_id);
    $query->execute();
    $book_vendor_id = $db->lastInsertId();
    $db = NULL;
    return $book_platform_id;
  }
  
}

?>
