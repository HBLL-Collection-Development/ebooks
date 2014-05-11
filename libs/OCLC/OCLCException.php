<?php
/**
  * Exception class for OCLC APIs
  *
  * @author Jared Howland <oclc@jaredhowland.com>
  * @version 2014-05-10
  * @since 2014-05-10
  *
  */

namespace OCLC;

class OCLCException extends \Exception {
  public function __construct($message, $code = 0, \Exception $previous = null) {
    $message = "<pre>\nOCLCException: " . $message . "\n\n" . parent::getFile() . ' on line ' . parent::getLine() . "\n\n" . parent::getTraceAsString() . "\n</pre>";
    parent::__construct($message, $code, $previous);
  }
}
?>
