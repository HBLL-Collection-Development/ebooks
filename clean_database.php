<?php
/**
  * Process and parse the temp_counter tables into individual tables
  *
  * @author Jared Howland <book.usage@jaredhowland.com>
  * @version 2013-05-14
  * @since 2013-05-04
  *
  */
  
require_once 'config.php';
  
class clean {

  public function database() {
    $this->drop_tables();
    // $this->add_tables();
    // $this->insert_data('documentation/example_data.sql');
    // Load exported file from phpMyAdmin
    $this->insert_data('documentation/matacq_book_usage.sql');
    // Constraints must be added after the data because of Foreign Key PDO errors that occur otherwise
    // $this->add_constraints();
  }
  
  private function drop_tables() {
    // DROP database
    $database = new db;
    $db    = $database->connect();
    $sql   = 'SET foreign_key_checks = 0;TRUNCATE TABLE `books`, `books_platforms`, `books_search`, `books_vendors`, `counter_br1`, `counter_br2`, `platforms`, `temp_counter_br1`, `temp_counter_br2`, `vendors`;SET foreign_key_checks = 1;';
    $query = $db->prepare($sql);
    $query->execute();
    $db = NULL;
  }
  
  private function add_tables() {
    $sql = <<<SQL
    -- --------------------------------------------------------

    --
    -- Table structure for table books
    --

    CREATE TABLE books (
      id int(11) NOT NULL AUTO_INCREMENT,
      title text COLLATE utf8_unicode_ci NOT NULL,
      author text COLLATE utf8_unicode_ci,
      publisher text COLLATE utf8_unicode_ci,
      doi text COLLATE utf8_unicode_ci,
      oclc text COLLATE utf8_unicode_ci,
      isbn text COLLATE utf8_unicode_ci,
      issn text COLLATE utf8_unicode_ci,
      call_num text COLLATE utf8_unicode_ci,
      response_code tinyint(3) DEFAULT NULL,
      valid_utf8 enum('Y','N','Unknown') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Unknown',
      PRIMARY KEY (id)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

    -- --------------------------------------------------------

    --
    -- Table structure for table books_platforms
    --

    CREATE TABLE books_platforms (
      id int(11) NOT NULL AUTO_INCREMENT,
      book_id int(11) NOT NULL,
      platform_id int(11) NOT NULL,
      PRIMARY KEY (id),
      KEY book_id (book_id),
      KEY platform_id (platform_id)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
    
    -- --------------------------------------------------------

    --
    -- Table structure for table `books_vendors`
    --

    CREATE TABLE `books_vendors` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `book_id` int(11) NOT NULL,
      `vendor_id` int(11) NOT NULL,
      PRIMARY KEY (`id`),
      KEY `book_id` (`book_id`,`vendor_id`),
      KEY `vendor_id` (`vendor_id`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

    -- --------------------------------------------------------

    --
    -- Table structure for table counter_br1
    --

    CREATE TABLE counter_br1 (
      id int(11) NOT NULL AUTO_INCREMENT,
      book_id int(11) NOT NULL,
      vendor_id int(11) NOT NULL,
      platform_id int(11) NOT NULL,
      usage_year int(11) NOT NULL,
      counter_br1 int(11) NOT NULL,
      PRIMARY KEY (id),
      KEY book_id (book_id),
      KEY vendor_id (vendor_id),
      KEY platform_id (platform_id),
      KEY year (usage_year)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

    -- --------------------------------------------------------

    --
    -- Table structure for table counter_br2
    --

    CREATE TABLE counter_br2 (
      id int(11) NOT NULL AUTO_INCREMENT,
      book_id int(11) NOT NULL,
      vendor_id int(11) NOT NULL,
      platform_id int(11) NOT NULL,
      usage_year int(11) NOT NULL,
      counter_br2 int(11) NOT NULL,
      PRIMARY KEY (id),
      KEY book_id (book_id),
      KEY vendor_id (vendor_id),
      KEY platform_id (platform_id),
      KEY year (usage_year)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

    -- --------------------------------------------------------

    --
    -- Table structure for table platforms
    --

    CREATE TABLE platforms (
      id int(11) NOT NULL AUTO_INCREMENT,
      vendor_id int(11) NOT NULL,
      platform text COLLATE utf8_unicode_ci NOT NULL,
      PRIMARY KEY (id),
      KEY vendor_id (vendor_id)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

    -- --------------------------------------------------------

    --
    -- Table structure for table temp_counter_br1
    --

    CREATE TABLE temp_counter_br1 (
      id int(11) NOT NULL AUTO_INCREMENT,
      title text COLLATE utf8_unicode_ci NOT NULL,
      publisher text COLLATE utf8_unicode_ci,
      platform text COLLATE utf8_unicode_ci,
      doi text COLLATE utf8_unicode_ci,
      proprietary_identifier text COLLATE utf8_unicode_ci,
      isbn text COLLATE utf8_unicode_ci,
      issn text COLLATE utf8_unicode_ci,
      counter_br1 int(11) DEFAULT NULL,
      usage_year int(4) DEFAULT NULL,
      vendor text COLLATE utf8_unicode_ci,
      PRIMARY KEY (id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

    -- --------------------------------------------------------

    --
    -- Table structure for table temp_counter_br2
    --

    CREATE TABLE temp_counter_br2 (
      id int(11) NOT NULL AUTO_INCREMENT,
      title text COLLATE utf8_unicode_ci NOT NULL,
      publisher text COLLATE utf8_unicode_ci,
      platform text COLLATE utf8_unicode_ci,
      doi text COLLATE utf8_unicode_ci,
      proprietary_identifier text COLLATE utf8_unicode_ci,
      isbn text COLLATE utf8_unicode_ci,
      issn text COLLATE utf8_unicode_ci,
      counter_br2 int(11) DEFAULT NULL,
      usage_year int(4) DEFAULT NULL,
      vendor text COLLATE utf8_unicode_ci,
      PRIMARY KEY (id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

    -- --------------------------------------------------------

    --
    -- Table structure for table vendors
    --

    CREATE TABLE vendors (
      id int(11) NOT NULL AUTO_INCREMENT,
      vendor text COLLATE utf8_unicode_ci NOT NULL,
      PRIMARY KEY (id)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

    -- --------------------------------------------------------

    --
    -- Table structure for table books_vendors
    --

    CREATE TABLE IF NOT EXISTS books_vendors (
      id int(11) NOT NULL AUTO_INCREMENT,
      book_id int(11) NOT NULL,
      vendor_id int(11) NOT NULL,
      PRIMARY KEY (id),
      KEY book_id (book_id,vendor_id),
      KEY vendor_id (vendor_id)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

    -- --------------------------------------------------------
    -- Views

    --
    -- Structure for view counter_usage
    --
    CREATE VIEW counter_usage AS (select counter_br1.book_id AS book_id,counter_br1.vendor_id AS vendor_id,counter_br1.platform_id AS platform_id,counter_br1.usage_year AS usage_year,counter_br1.counter_br1 AS counter_usage,'br1' AS usage_type from counter_br1) union all (select counter_br2.book_id AS book_id,counter_br2.vendor_id AS vendor_id,counter_br2.platform_id AS platform_id,counter_br2.usage_year AS usage_year,counter_br2.counter_br2 AS counter_usage,'br2' AS usage_type from counter_br2);

    -- --------------------------------------------------------

    --
    -- Structure for view platforms_browse
    --
    CREATE VIEW platforms_browse AS select bp.book_id AS book_id,b.title AS title,b.author AS author,b.publisher AS publisher,b.isbn AS isbn,b.call_num AS call_num,bp.platform_id AS platform_id,p.platform AS platform,p.vendor_id AS vendor_id,v.vendor AS vendor,cu.counter_usage AS counter_usage,cu.usage_year AS usage_year,cu.usage_type AS usage_type from ((((books_platforms bp left join counter_usage cu on((bp.book_id = cu.book_id))) left join books b on((bp.book_id = b.id))) left join platforms p on((bp.platform_id = p.id))) left join  vendors v on((p.vendor_id = v.id)));

    -- --------------------------------------------------------

    --
    -- Structure for view unicode
    --
    CREATE VIEW unicode AS select books.id AS id,books.title AS title,books.valid_utf8 AS valid_utf8 from books where ((length(books.title) <> char_length(books.title)) and ((books.valid_utf8 = 'N') or (books.valid_utf8 = 'Unknown')));

    -- --------------------------------------------------------

    --
    -- Structure for view vendors_browse
    --
    CREATE VIEW vendors_browse AS select bv.book_id AS book_id,b.title AS title,b.author AS author,b.publisher AS publisher,b.isbn AS isbn,b.call_num AS call_num,bv.vendor_id AS vendor_id,cu.counter_usage AS counter_usage,cu.usage_year AS usage_year,cu.usage_type AS usage_type from ((books_vendors bv left join counter_usage cu on((bv.book_id = cu.book_id))) left join books b on((bv.book_id = b.id)));
SQL;
  // Connect to database
  $database = new db;
  $db    = $database->connect();
  $query = $db->prepare($sql);
  $query->execute();
  $db = NULL;
  return NULL;
  }
  
  private function insert_data($file_with_data) {
    $sql = file_get_contents($file_with_data);
    // Connect to database
    $database = new db;
    $db    = $database->connect();
    $query = $db->prepare($sql);
    $query->execute();
    $db = NULL;
    return NULL;
  }
  
  private function add_constraints() {
    $sql = <<<SQL
    --
    -- Constraints for table books_platforms
    --
    ALTER TABLE books_platforms
      ADD CONSTRAINT books_platforms_book_id FOREIGN KEY (book_id) REFERENCES books (id) ON DELETE CASCADE ON UPDATE CASCADE,
      ADD CONSTRAINT books_platforms_platform_id FOREIGN KEY (platform_id) REFERENCES platforms (id) ON DELETE CASCADE ON UPDATE CASCADE;
    
    --
    -- Constraints for table books_vendors
    --
    ALTER TABLE books_vendors
      ADD CONSTRAINT books_vendors_book_id FOREIGN KEY (book_id) REFERENCES books (id) ON DELETE CASCADE ON UPDATE CASCADE,
      ADD CONSTRAINT books_vendors_vendor_id FOREIGN KEY (vendor_id) REFERENCES vendors (id) ON DELETE CASCADE ON UPDATE CASCADE;
    
    --
    -- Constraints for table counter_br1
    --
    ALTER TABLE counter_br1
      ADD CONSTRAINT counter_br1_book_id FOREIGN KEY (book_id) REFERENCES books (id) ON DELETE CASCADE ON UPDATE CASCADE,
      ADD CONSTRAINT counter_br1_platform_id FOREIGN KEY (platform_id) REFERENCES platforms (id) ON DELETE CASCADE ON UPDATE CASCADE,
      ADD CONSTRAINT counter_br1_vendor_id FOREIGN KEY (vendor_id) REFERENCES vendors (id) ON DELETE CASCADE ON UPDATE CASCADE;
    
    --
    -- Constraints for table counter_br2
    --
    ALTER TABLE counter_br2
      ADD CONSTRAINT counter_br2_book_id FOREIGN KEY (book_id) REFERENCES books (id) ON DELETE CASCADE ON UPDATE CASCADE,
      ADD CONSTRAINT counter_br2_platform_id FOREIGN KEY (platform_id) REFERENCES platforms (id) ON DELETE CASCADE ON UPDATE CASCADE,
      ADD CONSTRAINT counter_br2_vendor_id FOREIGN KEY (vendor_id) REFERENCES vendors (id) ON DELETE CASCADE ON UPDATE CASCADE;
    
    --
    -- Constraints for table platforms
    --
    ALTER TABLE platforms
      ADD CONSTRAINT platforms_vendor_id FOREIGN KEY (vendor_id) REFERENCES vendors (id) ON DELETE CASCADE ON UPDATE CASCADE;
SQL;
  // Connect to database
  $database = new db;
  $db    = $database->connect();
  $query = $db->prepare($sql);
  $query->execute();
  $db = NULL;
  return NULL;
  }
}

$drop = new clean;
$drop->database();

?>
