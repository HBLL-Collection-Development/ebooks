<?php
/**
  * Database class to connect to a MySQL database
  *
  * @author Jared Howland <book.usage@jaredhowland.com>
  * @version 2013-06-08
  * @since 2012-12-05
  *
  */

class db {
  /**
   * Connects to the database
   *
   * @access public
   * @param null
   * @return object Object containing database connection information
   */
  public function connect() {
    return $this->db_connect();
  }

  private function db_connect() {
    try {
      $db = new PDO('mysql:host=' . config::DB_HOST . ';port=' . config::DB_PORT . ';dbname=' . config::DB_NAME . ';charset=utf8', config::DB_USERNAME, config::DB_PASSWORD, array(PDO::ATTR_PERSISTENT => false));
      $db->exec("SET NAMES 'utf8';");
      $db->exec('SET CHARACTER SET utf8;');
      $db->exec('SET group_concat_max_len=4294967295');
      // Enable PDO error messages if in development
      if(config::DEVELOPMENT) {
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      }
      return $db;
    // Throw an error and kill script if cannot connect
    } catch ( PDOException $e ) {
      $e->getMessage();
      die();
    }
  }

}
?>
