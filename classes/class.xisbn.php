<?php
/**
  * Class to find all related ISBNs using OCLC’s XISBN service
  *
  * http://www.oclc.org/developer/services/xisbn
  * http://www.oclc.org/developer/documentation/xisbn/using-api
  *
  * http://xisbn.worldcat.org/webservices/xid/isbn/9783527283132?method=getMetadata&fl=*&format=json&ai={config::WORLDCAT_ID}
  * http://xisbn.worldcat.org/webservices/xid/isbn/9783527283132?method=getEditions&format=php&ai={config::WORLDCAT_ID}
  *
  * @author Jared Howland <book.usage@jaredhowland.com>
  * @version 2013-06-09
  * @since 2013-05-30
  *
  */

class xisbn {
  /**
   * Returns array of related ISBNs
   *
   * @access public
   * @param string ISBN
   * @return array Array of related ISBNs
   */
  public function get_isbns($isbn) {
    if(config::WORLDCAT_ID) {
      $file = 'http://xisbn.worldcat.org/webservices/xid/isbn/' . $isbn . '?method=getEditions&format=txt&ai=' . config::WORLDCAT_ID;
      return file($file);
    } else {
      return $isbn;
    }
  }
  
}
?>
