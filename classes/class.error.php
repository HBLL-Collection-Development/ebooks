<?php
/**
  * Class to trigger errors
  *
  * @author Jared Howland <book.usage@jaredhowland.com>
  * @version 2013-05-09
  * @since 2013-05-02
  *
  */

class error {
  /**
   * Logs error
   *
   * @access public
   * @param string $message Error message to display/log
   * @param string $level Error level to use
   * @return null
   */
  public static function trigger($message, $level = NULL) {
    set_error_handler('self::error_handler');
    if(is_null($level)) {
      $level = E_USER_ERROR;
    }
    // Get the caller of the calling function and details about it
    $callee = debug_backtrace();
    $callee = next($callee);
    // Trigger appropriate error
    $message = $message . ' in <strong>' . $callee['file'] . '</strong> on line <strong>' . $callee['line'] . '</strong>';
    template::display('generic.tmpl', $message);
    trigger_error($message, $level);
  }
  
  public static function error_handler($level, $message, $file, $line, $context) {
    // Handle user errors, warnings, and notices ourself
    if($level === E_USER_ERROR || $level === E_USER_WARNING || $level === E_USER_NOTICE) {
      echo '<strong>Error:</strong> ' . $message;
      return(true); // And prevent the PHP error handler from continuing
    }
    return(false); // Otherwise, use PHP's error handler
  }
}
?>
