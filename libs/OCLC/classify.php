<?php
/**
  * Class to search OCLC Classify Web Service
  * http://classify.oclc.org/classify2/api_docs/classify.html
  *
  * @author Jared Howland <oclc@jaredhowland.com>
  * @version 2014-05-10
  * @since 2014-05-10
  *
  */

namespace OCLC;

class classify {
  const BASE_URL = 'http://classify.oclc.org/classify2/Classify?';

  public function stdnbr($stdnbr, $options = null) {
    return $this->get_stdnbr($stdnbr, $options);
  }

  public function standard_number($stdnbr, $options = null) {
    return $this->get_stdnbr($stdnbr, $options);
  }

  public function oclc($oclc, $options = null) {
    return $this->get_oclc($oclc, $options);
  }

  public function isbn($isbn, $options = null) {
    return $this->get_isbn($isbn, $options);
  }

  public function upc($upc, $options = null) {
    return $this->get_upc($upc, $options);
  }

  public function ident($ident, $options = null) {
    return $this->get_ident($ident, $options);
  }

  public function heading($heading, $options = null) {
    return $this->get_heading($heading, $options);
  }

  public function swid($swid, $options = null) {
    return $this->get_swid($swid, $options);
  }

  public function author($author, $options = null) {
    return $this->get_author($author, $options);
  }

  public function title($title, $options = null) {
    return $this->get_title($title, $options);
  }

  public function multi($array, $options = null) {
    return $this->get_multi($multi, $options);
  }

  private function get_stdnbr($stdnbr, $options) {
    return $this->get_array('stdnbr', $stdnbr, $options);
  }

  private function get_oclc($oclc, $options = null) {
    return $this->get_array('oclc', $oclc, $options);
  }

  private function get_isbn($isbn, $options = null) {
    return $this->get_array('isbn', $isbn, $options);
  }

  private function get_upc($upc, $options = null) {
    return $this->get_array('upc', $upc, $options);
  }

  private function get_ident($ident, $options = null) {
    return $this->get_array('ident', $ident, $options);
  }

  private function get_heading($heading, $options = null) {
    return $this->get_array('heading', $heading, $options);
  }

  private function get_swid($swid, $options = null) {
    return $this->get_array('swid', $swid, $options);
  }

  private function get_author($author, $options = null) {
    return $this->get_array('author', $author, $options);
  }

  private function get_title($title, $options = null) {
    return $this->get_array('title', $title, $options);
  }

  // TODO: Finish this function to allow multiple field searches in one query
  private function get_multi($array, $options = null) {

  }

  // Quick and dirty XML to PHP array function
  // Possible better solution for future: http://www.lalit.org/lab/convert-xml-to-array-in-php-xml2array/
  private function get_array($type, $search, $options) {
    return json_decode(json_encode(simplexml_load_file(urlencode(self::BASE_URL . $type . '=' . $search . '&' . $this->set_options($options)))), true);
  }

  private function set_options($options = null) {
    if(is_null($options)) {
      return $options;
    } elseif(is_array($options)) {
      return http_build_query($this->validate_options($options));
    } else {
      throw new OCLCException("Classify options must be passed as an array.\n\nValid parameters are `summary`, `maxRecs`, `orderBy`, and `startRec`.");
    }
  }

  private function validate_options($options = null) {
    $options_array = null;
    $orderBy       = array('mancount asc', 'mancount desc', 'hold asc', 'hold desc', 'lyr asc', 'lyr desc', 'hyr asc', 'hyr desc', 'ln asc', 'ln desc', 'sheading asc', 'sheading desc', 'works asc', 'works desc', 'type asc', 'type desc');
    foreach($options as $key => $value) {
      switch ($key) {
        case 'summary':
          if((bool) $value) {
            $options_array['summary'] = 'true';
          } else {
            $options_array['summary'] = 'false';
          }
          break;
        case 'maxRecs':
          $options_array['maxRecs'] = (int) $value;
          break;
        case 'orderBy':
          if(in_array($value, $orderBy)) {
            $options_array['orderBy'] = $value;
          } else {
            throw new OCLCException("Invalid orderBy value.\n\nValid values are `mancount asc`, `mancount desc`, `hold asc`, `hold desc`, `lyr asc`, `lyr desc`, `hyr asc`, `hyr desc`, `ln asc`, `ln desc`, `sheading asc`, `sheading desc`, `works asc`, `works desc`, `type asc`, `type desc`.");
          }
          break;
        case 'startRec':
          $options_array['startRec'] = (int) $value;
          break;
        default:
          throw new OCLCException("Invalid option parameter.\n\nValid parameters are `summary`, `maxRecs`, `orderBy`, and `startRec`.");
      }
    }
    return $options_array;
  }

}
?>
