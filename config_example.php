<?php
/**
  * Configuration class.
  *
  * @author Jared Howland <book.usage@jaredhowland.com>
  * @version 2013-06-08
  * @since 2013-04-30
  *
  */
class config {
  // Database settings
  const DB_HOST          = 'localhost';
  const DB_PORT          = '3306';
  const DB_NAME          = 'book_usage';
  const DB_USERNAME      = 'USERNAME';
  const DB_PASSWORD      = 'PASSWORD';
  // App settings
  const TIME_ZONE        = 'America/Denver'; // Needed for date calculations in PHP
  const DEVELOPMENT      = TRUE; // Changes the app behavior (error reporting, template caching, which database to use, etc.); FALSE if in production.
  const URL              = 'http://localhost/books'; // Base URL of your application
  const PROCESS_LIMIT    = 50; // Number of rows to process at one time when parsing from temp table to permanent tables
  const WORLDCAT_ID      = FALSE; // WorldCat Affiliate ID; FALSE if you do not have one
  const TEMPLATE_DIR     = 'templates/default';

  /****************************************************************************/
  /*                       DO NOT EDIT BELOW THIS LINE                        */
  /****************************************************************************/

  public static $current_year;
  public static $previous_year;

  public function __construct() {
    $this->set_current_year();
    $this->set_previous_year();
  }

  /**
    * Determines type of error reporting
    * Based on state of DEVELOPMENT constant
    *
    * @param null
    * @return string Type of error reporting
    */
  public static function set_error_reporting() {
    if(self::DEVELOPMENT) {
      ini_set('error_reporting', E_ALL^E_NOTICE);
      ini_set('display_errors', 1);
    } else {
      error_reporting(0);
    }
  }
  
  /**
    * Finds to newest usage year from the database and sets $current_year
    *
    * @access private
    * @param NULL
    * @return NULL
    *
    */
  private function set_current_year() {
    // Connect to database
    $database = new db;
    $db       = $database->connect();
    $sql      = 'SELECT MAX(usage_year) AS current_year FROM counter_usage';
    $query    = $db->prepare($sql);
    $query->bindParam(':vendor_id', $vendor_id);
    $query->execute();
    $current_year = $query->fetchAll(PDO::FETCH_ASSOC);
    $db = NULL;
    self::$current_year = $current_year[0]['current_year'];
  }
  
  /**
    * Calculates the previous year of usage data available in the database
    *
    * @access private
    * @param NULL
    * @return NULL
    *
    */
  private function set_previous_year() {
    self::$previous_year = self::$current_year - 1;
  }
  
} // End class


/****************************************/
/* Miscellaneous configuration settings */
/****************************************/

// Autoload classes
// Must be in the 'classes' directory and prefixed with 'class.'
function __autoload($class) {
  // require_once(__DIR__ . '/classes/class.' . $class . '.php'); // Will work when migrate to PHP > 5.3
  require_once(dirname(__FILE__) . '/classes/class.' . $class . '.php');
}

// Set default time zone
date_default_timezone_set(config::TIME_ZONE);

// Set larger memory size
ini_set('memory_limit','512M');

// Set error reporting
config::set_error_reporting();

// Various PHP settings
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
mb_http_input('UTF-8');
mb_language('uni');
mb_regex_encoding('UTF-8');

// Instantiate config class
$config = new config;

?>
