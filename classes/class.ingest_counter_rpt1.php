<?php
/**
  * Class to ingest Project COUNTER Book Report 1 files
  *
  * @author Jared Howland <book.usage@jaredhowland.com>
  * @version 2013-05-14
  * @since 2013-05-01
  *
  */

class ingest_counter_rpt1 extends ingest {

  /**
   * Parses and ingests plain text CSV files
   *
   * @access public
   * @param array $file
   * @return NULL
   */
  public function __construct($file) {
    $lines = parent::__construct($file);
    // Place CSV data into temp_counter_br1 table
    foreach($lines as $line) {
      // If this is COUNTER BR1 R4, define variables as follows:
      if(count($line) == 10) {
        $id                     = NULL;
        $title                  = $this->clean_nulls($line[0]);
        $publisher              = $this->clean_nulls($line[1]);
        $platform               = $this->clean_platform($line[2]);
        $doi                    = $this->clean_nulls($line[3]);
        $proprietary_identifier = $this->clean_nulls($line[4]);
        $isbn                   = $this->validate_standard_number($line[5]);
        $issn                   = $this->validate_standard_number($line[6]);
        $counter_br1            = $this->clean_nulls($line[7]);
        $usage_year             = $this->clean_nulls($line[8]);
        $vendor                 = $this->clean_nulls($line[9]);
      // If this is COUNTER BR1 R1, define variables as follows:
      } elseif(count($line == 8)) {
        $id                     = NULL;
        $title                  = $this->clean_nulls($line[0]);
        $publisher              = $this->clean_nulls($line[1]);
        $platform               = $this->clean_platform($line[2]);
        $doi                    = NULL; // Is not included in COUNTER BR1 R1
        $proprietary_identifier = NULL; // Is not included in COUNTER BR1 R1
        $isbn                   = $this->validate_standard_number($line[3]);
        $issn                   = $this->validate_standard_number($line[4]);
        $counter_br1            = $this->clean_nulls($line[5]);
        $usage_year             = $this->clean_nulls($line[6]);
        $vendor                 = $this->clean_nulls($line[7]);
      }
      // Connect to database
      $database = new db;
      $db       = $database->connect();
      $sql      = 'INSERT INTO temp_counter_br1 (id, title, publisher, platform, doi, proprietary_identifier, isbn, issn, counter_br1, usage_year, vendor) VALUES (:id, :title, :publisher, :platform, :doi, :proprietary_identifier, :isbn, :issn, :counter_br1, :usage_year, :vendor)';
      $query    = $db->prepare($sql);
      $query->bindParam(':id', $id);
      $query->bindParam(':title', $title);
      $query->bindParam(':publisher', $publisher);
      $query->bindParam(':platform', $platform);
      $query->bindParam(':doi', $doi);
      $query->bindParam(':proprietary_identifier', $proprietary_identifier);
      $query->bindParam(':isbn', $isbn);
      $query->bindParam(':issn', $issn);
      $query->bindParam(':counter_br1', $counter_br1);
      $query->bindParam(':usage_year', $usage_year);
      $query->bindParam(':vendor', $vendor);
      $result = $query->execute();
      $db = NULL;
    }
  }
  
}
?>