<?php
/**
  * Class to search OCLC Classify Web Service
  * http://classify.oclc.org/classify2/api_docs/classify.html
  *
  * @author Jared Howland <book.usage@jaredhowland.com>
  * @version 2013-05-09
  * @since 2013-05-01
  *
  */

class ingest_counter_rpt2 {

  /**
   * Passes the content into the specified Twig template
   *
   * @access public
   * @param string Search type to perform; Valid values: standard_number, title; Default: standard_number
   * @param string|array String or array of search term(s)
   * @return array Array of title, author, isbn, call_num for all search terms
   */
  public function ingest($file) {
    if($this->valid_file($file)) {
      // Open and process CSV file
      $file = fopen($file['uploadedfile']['tmp_name'], 'r');
      while (($line = fgetcsv($file)) !== FALSE) {
        //$line is an array of the CSV elements
        $lines[] = $line;
      }
      fclose($file);
      // Place CSV data into temp_counter_br2 table
      foreach($lines as $line) {
        // If this is COUNTER BR2 R4, define variables as follows:
        if(count($line) == 10) {
          $id                     = NULL;
          $title                  = $this->clean_nulls($line[0]);
          $publisher              = $this->clean_nulls($line[1]);
          $platform               = $this->clean_platform($line[2]);
          $doi                    = $this->clean_nulls($line[3]);
          $proprietary_identifier = $this->clean_nulls($line[4]);
          $isbn                   = $this->validate_standard_number($line[5]);
          $issn                   = $this->validate_standard_number($line[6]);
          $counter_br2          = $this->clean_nulls($line[7]);
          $year                   = $this->clean_nulls($line[8]);
          $vendor                 = $this->clean_nulls($line[9]);
        // If this is COUNTER BR2 R1, define variables as follows:
        } elseif(count($line == 8)) {
          $id                     = NULL;
          $title                  = $this->clean_nulls($line[0]);
          $publisher              = $this->clean_nulls($line[1]);
          $platform               = $this->clean_platform($line[2]);
          $doi                    = NULL;
          $proprietary_identifier = NULL;
          $isbn                   = $this->validate_standard_number($line[3]);
          $issn                   = $this->validate_standard_number($line[4]);
          $counter_br2          = $this->clean_nulls($line[5]);
          $year                   = $this->clean_nulls($line[6]);
          $vendor                 = $this->clean_nulls($line[7]);
        }
        // Connect to database
        $database = new db;
        $db       = $database->connect();
        $sql      = 'INSERT INTO temp_counter_br2 (id, title, publisher, platform, doi, proprietary_identifier, isbn, issn, counter_br2, year, vendor) VALUES (:id, :title, :publisher, :platform, :doi, :proprietary_identifier, :isbn, :issn, :counter_br2, :year, :vendor)';
        $query    = $db->prepare($sql);
        $query->bindParam(':id', $id);
        $query->bindParam(':title', $title);
        $query->bindParam(':publisher', $publisher);
        $query->bindParam(':platform', $platform);
        $query->bindParam(':doi', $doi);
        $query->bindParam(':proprietary_identifier', $proprietary_identifier);
        $query->bindParam(':isbn', $isbn);
        $query->bindParam(':issn', $issn);
        $query->bindParam(':counter_br2', $counter_br2);
        $query->bindParam(':year', $year);
        $query->bindParam(':vendor', $vendor);
        $result = $query->execute();
        $db = NULL;
      }
    }
  }
  
  private function clean_nulls($string) {
    if(strtoupper($string) == 'NULL' || strtoupper($string) == 'N/A') {
      return NULL;
    } else {
      return $string;
    }
  }
  
  private function clean_platform($platform) {
    switch ($platform) {
      case 'EBRARY':
        return 'ebrary';
        break;
      case 'http://proquest.safaribooksonline.com/?uicode=ualc':
        return 'Safari Tech Books Online';
        break;
      case 'EBL- Ebook Library':
        return 'Ebook Library';
        break;
      default:
        return $this->clean_nulls($platform);
        break;
    }
  }
  
  private function valid_file($file) {
    if ( $file['uploadedfile']['type'] != "text/csv" ) {
      $error = new error;
      $error->trigger('Only plain text .CSV files may be uploaded');
      return FALSE;
    } else {
      return TRUE;
    }
  }
  
  private function validate_standard_number($standard_number) {
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
  private function strip_non_numeric($string) {
    return preg_replace('{[^0-9X]}', '', strtoupper($string));
  }
  
  private function is_issn_valid($issn) {
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
  
  private function is_isbn_valid($isbn) {
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