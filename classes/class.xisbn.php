<?php
/**
  * Class to find all related ISBNs using OCLC’s XISBN service
  * http://www.oclc.org/developer/documentation/xisbn/using-api
  * http://xisbn.worldcat.org/webservices/xid/isbn/9783527283132?method=getMetadata&fl=*&format=json&ai=jared_howland
  * http://xisbn.worldcat.org/webservices/xid/isbn/9783527283132?method=getEditions&format=php&ai=jared_howland
  * http://www.oclc.org/developer/services/xisbn
  *
  * @author Jared Howland <book.usage@jaredhowland.com>
  * @version 2013-05-30
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
    $file = 'http://xisbn.worldcat.org/webservices/xid/isbn/' . $isbn . '?method=getEditions&format=txt&ai=jared_howland';
    return file($file);
  }
  
}
?>
