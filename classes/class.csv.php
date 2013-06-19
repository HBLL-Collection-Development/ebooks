<?php
/**
  * Class to export search/browse results to a CSV file
  * http://stackoverflow.com/questions/4249432/export-to-csv-via-php
  *
  * @author Jared Howland <book.usage@jaredhowland.com>
  * @version 2013-05-30
  * @since 2013-05-30
  *
  */

class csv {
  /**
    * Download results as a CSV file
    *
    * @access public
    * @param array Array of results to download
    * @return mixed CSV file of results
    *
    */
  public function download($array, $heading) {
    $array = $this->format_array($array['results']);
    $this->download_send_headers($heading . '_' . date("Y-m-d") . ".csv");
    return $this->array2csv($array);
  }
  
  /**
    * Convert array to CSV file format
    *
    * @access private
    * @param array Array of results to convert to CSV format
    * @return mixed CSV file of results or NULL if invalid array is passed
    *
    */
  private function array2csv(array $array) {
    if(count($array) == 0) {
      return NULL;
    }
    $df = fopen("php://output", 'w');
    fputcsv($df, array_keys(reset($array)));
    foreach ($array as $row){
      fputcsv($df, $row);
    }
    fclose($df);
    return ob_get_clean();
  }
  
  /**
    * Send headers to browser to force download of CSV file
    *
    * @access private
    * @param string Name of file to download
    * @return NULL
    *
    */
  private function download_send_headers($filename) {
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-stream");
    header("Content-Type: application/download");
    header("Content-Disposition: attachment;filename={$filename}");
    header("Content-Transfer-Encoding: binary");
  }
  
  /**
    * Parse all fields in results to individual fields to be placed in CSV
    *
    * @access private
    * @param array Results to download
    * @return array Formatted array of results
    *
    */
  private function format_array($array) {
    foreach($array as $book) {
      $title        = $this->clean($book['title']);
      $author       = $this->clean($book['author']);
      $publisher    = $this->clean($book['publisher']);
      $isbn         = $this->clean_isbn($book['isbn']);
      $call_num     = $this->clean($book['call_num']);
      $platforms    = $this->clean_platforms($book['platforms']);
      $latest_br1   = $this->clean($book['latest_br1']);
      $previous_br1 = $this->clean($book['previous_br1']);
      $latest_br2   = $this->clean($book['latest_br2']);
      $previous_br2 = $this->clean($book['previous_br2']);
      $books[]      = array('Title' => $title, 'Author' => $author, 'Publisher' => $publisher, 'ISBN' => $isbn, 'Call Number' => $call_num, 'Platform(s)' => $platforms, config::$current_year . ' Title-level Usage' => $latest_br1, config::$previous_year . ' Title-level Usage' => $previous_br1, config::$current_year . ' Chapter-level Usage' => $latest_br2, config::$previous_year . ' Chapter-level Usage' => $previous_br2);
    }
    return $books;
  }
  
  /**
    * Cleans the data (trims whitespace and replaces NULL values with '--')
    *
    * @access private
    * @param string String to clean
    * @return string String to be placed in CSV field
    *
    */
  private function clean($string) {
    if(is_null($string)) {
      return '--';
    } else {
      return trim($string);
    }
  }
  
  /**
    * Clean ISBNs
    *
    * @access private
    * @param string ISBN to clean
    * @return string Cleaned ISBN placed in quotes so Excel does not convert to scientific notation
    *
    */
  private function clean_isbn($isbn) {
    $isbn = $this->clean($isbn);
    return '="' . $isbn . '"';
  }
  
  /**
    * Cleans the platforms for better CSV consumption
    *
    * @access private
    * @param string List of platforms
    * @return All platforms pipe delimited
    *
    */
  private function clean_platforms($platforms) {
    $platforms = $this->clean($platforms);
    $platforms = str_replace('</li><li>', '|', $platforms);
    $platforms = str_replace('<li>', '', $platforms);
    return str_replace('</li>', '', $platforms);
  }
}

?>
