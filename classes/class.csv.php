<?php
/**
  * Class to search OCLC Classify Web Service
  * http://classify.oclc.org/classify2/api_docs/classify.html
  * http://stackoverflow.com/questions/4249432/export-to-csv-via-php
  *
  * @author Jared Howland <book.usage@jaredhowland.com>
  * @version 2013-05-30
  * @since 2013-05-30
  *
  */

class csv {
  private $current_year;
  private $previous_year;

  public function download($array) {
    $this->current_year  = config::$current_year;
    $this->previous_year = config::$previous_year;
    $array = $this->format_array($array['results']);
    $this->download_send_headers("data_export_" . date("Y-m-d") . ".csv");
    return $this->array2csv($array);
  }
  
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

      $books[]      = array('Title' => $title, 'Author' => $author, 'Publisher' => $publisher, 'ISBN' => $isbn, 'Call Number' => $call_num, 'Platform(s)' => $platforms, $this->current_year . ' Title-level Usage' => $latest_br1, $this->previous_year . ' Title-level Usage' => $previous_br1, $this->current_year . ' Chapter-level Usage' => $latest_br2, $this->previous_year . ' Chapter-level Usage' => $previous_br2);
    }
    return $books;
  }
  
  private function clean($string) {
    if(is_null($string)) {
      return '--';
    } else {
      return trim($string);
    }
  }
  
  private function clean_isbn($isbn) {
    $isbn = $this->clean($isbn);
    return '="' . $isbn . '"';
  }
  
  private function clean_platforms($platforms) {
    $platforms = $this->clean($platforms);
    $platforms = str_replace('</li><li>', '|', $platforms);
    $platforms = str_replace('<li>', '', $platforms);
    return str_replace('</li>', '', $platforms);
  }

}

?>
