<?php
/**
  * Class to ingest Project COUNTER Book Reports 1 & 2 files
  *
  * @author Jared Howland <book.usage@jaredhowland.com>
  * @version 2013-06-08
  * @since 2013-05-01
  *
  */

class ingest {
  /**
   * Parses and ingests plain text CSV files
   *
   * @access public
   * @param array $file
   * @return NULL
   */
  public function __construct($file) {
    if($this->valid_file($file)) {
      // Open and process CSV file
      $file = fopen($file['uploadedfile']['tmp_name'], 'r');
      while (($line = fgetcsv($file)) !== FALSE) {
        // $line is an array of the CSV elements
        $lines[] = $line;
      }
      fclose($file);
      return $lines;
    }
  }
  
  /**
    * Cleans NULLs before inserting into database
    *
    * @access protected
    * @param string String to clean
    * @return NULL|string NULL if no value, NULL value, or N/A; trimmed string otherwise
    *
    */
  protected function clean_nulls($string) {
    $string = trim($string);
    if(strtoupper($string) == 'NULL' || strtoupper($string) == 'N/A' || $string == '') {
      return NULL;
    } else {
      return $string;
    }
  }
  
  /**
    * Converts weird names for platforms into a consistent name for know inconsistencies
    *
    * @access protected
    * @param string Platform name
    * @return string Cleaned platform name
    *
    */
  protected function clean_platform($platform) {
    $platform = trim($platform);
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
      case 'MetaPress':
        return 'SpringerLink';
        break;
      case 'SPL':
        return 'SpringerLink';
        break;
      case 'GOLD':
        return 'Gale Virtual Reference Library';
        break;
      default:
        return $this->clean_nulls($platform);
        break;
    }
  }
  
  /**
    * Checks to see if it is a plain text CSV file that was uploaded
    *
    * @access protected
    * @param array Uploaded file
    * @return bool TRUE if valid; FALSE otherwise
    *
    */
  protected function valid_file($file) {
    if ( $file['uploadedfile']['type'] != "text/csv" ) {
      error::trigger('Only plain text .CSV files may be uploaded');
      return FALSE;
    } else {
      return TRUE;
    }
  }
  
  /**
    * Validates ISBN-10s, ISBN-13s, and ISSNs
    *
    * @access protected
    * @param string ISBN-10, ISBN-13, or ISSN
    * @return mixed Returns cleaned standard number if valid; bool FALSE otherwise
    *
    */
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
   * @return string Cleaned $string
   */
  protected function strip_non_numeric($string) {
    return preg_replace('{[^0-9X]}', '', strtoupper($string));
  }
  
  /**
    * Validates ISSNs
    *
    * @access protected
    * @param string ISSN
    * @return mixed ISSN string if valid; bool FALSE otherwise
    *
    */
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
  
  /**
    * Validates ISBN-10s and ISBN-13s
    *
    * @access protected
    * @param string ISBN
    * @return mixed ISBN string if valid; bool FALSE otherwise
    *
    */
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
  
}
?>
