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
  private $format;

  public function __construct($format = null) {
    $this->set_format($format);
  }

  public function set_format($format) {
    switch ($format) {
      case 'xml':
      case 'json':
      case 'php_object':
      case 'php_array':
        $this->format = $format;
        break;
      case null:
        $this->format = 'php_array';
        break;
      default:
        throw new OCLCException("Invalid format.\n\nValid formats include `xml`, `json`, `php_object`, and `php_array`.");
    }
  }

  public function stdnbr($stdnbr, $options = null) {
    return $this->get_stdnbr($stdnbr, $options);
  }

  // Alias of stdnbr()
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
    return $this->get_multi($array, $options);
  }

  private function get_stdnbr($stdnbr, $options) {
    return $this->get_array('stdnbr', $stdnbr, $options);
  }

  private function get_oclc($oclc, $options) {
    return $this->get_array('oclc', $oclc, $options);
  }

  private function get_isbn($isbn, $options) {
    return $this->get_array('isbn', $isbn, $options);
  }

  private function get_upc($upc, $options) {
    return $this->get_array('upc', $upc, $options);
  }

  private function get_ident($ident, $options) {
    return $this->get_array('ident', $ident, $options);
  }

  private function get_heading($heading, $options) {
    return $this->get_array('heading', $heading, $options);
  }

  private function get_swid($swid, $options) {
    return $this->get_array('swid', $swid, $options);
  }

  private function get_author($author, $options) {
    return $this->get_array('author', $author, $options);
  }

  private function get_title($title, $options) {
    return $this->get_array('title', $title, $options);
  }

  // TODO: Finish this function to allow multiple field searches in one query
  private function get_multi($search, $options) {
    if(is_array($search)) {
      return $this->get_array('multi', $search, $options);
    } else {
      throw new OCLCException("If you want to search multiple fields at once, the search terms must be placed in an array.\n\nValid search fields include `stdnbr` (or `standard_number`), `oclc`, `isbn`, `issn`, `upc`, `ident`, `heading`, `swid`, `author`, `title`.");
    }
  }

  // Quick and dirty XML to PHP array function
  // Possible better solution for future: http://www.lalit.org/lab/convert-xml-to-array-in-php-xml2array/
  private function get_array($type, $search, $options) {
    if($type == 'multi') {
      $search = http_build_query($this->validate_search($search));
    } else {
      if(is_array($search) && count($search) == 1) {
        $key   = key($search);
        $value = reset($search);
        if($key == $type) { // Forgive using an array for a search if it matches the called method
          $search = $type . '=' . $value;
        } else {
          throw new OCLCException('Only `multi` searches should be an array. Please try again using a string.');
        }
      } elseif (!is_array($search)) {
        $search = $type . '=' . $search;
      } else {
        throw new OCLCException('Only `multi` searches should be an array. Please try again using a string.');
      }
    }
    $url = self::BASE_URL . $search . $this->set_options($options);
    switch ($this->format) {
      case 'xml':
        return file_get_contents($url);
        break;
      case 'json':
        return json_encode(simplexml_load_file(urlencode($url)));
        break;
      case 'php_object':
        return simplexml_load_file(urlencode($url));
        break;
      case 'php_array':
        return json_decode(json_encode(simplexml_load_file(urlencode($url))), true);
        break;
      default:
        return json_decode(json_encode(simplexml_load_file(urlencode($url))), true);
        break;
    }
  }

  private function set_options($options = null) {
    if(is_null($options)) {
      return $options;
    } elseif(is_array($options)) {
      return '&' . http_build_query($this->validate_options($options));
    } else {
      throw new OCLCException("Classify options must be passed as an array.\n\nValid options are `summary` (bool), `maxRecs` (int), `orderBy` (string), and `startRec` (int).");
    }
  }

  private function validate_options($options) {
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
          throw new OCLCException("Invalid option parameter.\n\nValid parameters are `summary` (bool), `maxRecs` (int), `orderBy` (string), and `startRec` (int).");
      }
    }
    return $options_array;
  }

  private function validate_search($search) {
    $search_array = null;
    foreach($search as $key => $value) {
      switch ($key) {
        case 'stdnbr':
        case 'standard_number':
          $search_array['stdnbr'] = $value;
          break;
        case 'oclc':
          $search_array['oclc'] = $value;
          break;
        case 'isbn':
          $search_array['isbn'] = $value;
          break;
        case 'issn':
          $search_array['issn'] = $value;
          break;
        case 'upc':
          $search_array['upc'] = $value;
          break;
        case 'ident':
          $search_array['ident'] = $value;
          break;
        case 'heading':
          $search_array['heading'] = $value;
          break;
        case 'swid':
          $search_array['swid'] = $value;
          break;
        case 'author':
          $search_array['author'] = $value;
          break;
        case 'title':
          $search_array['title'] = $value;
          break;
        default:
          throw new OCLCException("Invalid search attempted.\n\nValid search fields include `stdnbr` (or `standard_number`), `oclc`, `isbn`, `issn`, `upc`, `ident`, `heading`, `swid`, `author`, `title`.");
      }
    }
    return $search_array;
  }

}
?>
