<?php
/**
  * Class to easily access Twig templating engine
  *
  * @author Jared Howland <book.usage@jaredhowland.com>
  * @version 2013-05-15
  * @since 2012-12-06
  *
  */

class template {
  /**
   * Passes the content into the specified Twig template
   *
   * @access public
   * @param string Name of template to use
   * @param array Array of content to place in template
   * @return Displays content in the requested template
   */
  public static function display($template_name, $content) {
    // Allow generic HTML to be placed into templates by converting strings to arrays
    if(!is_array($content)) {
      $content = array('html' => $content);
    }
    echo template::templatize($template_name, $content);
  }

  private static function templatize($template_name, $content) {
    require_once './libs/Twig/Autoloader.php';
    // Inject format into content array
    $format = array('format' => $format);
    $content = array_merge($format, $content);
    // Call template
    Twig_Autoloader::register();
    $loader = new Twig_Loader_Filesystem('./' . config::TEMPLATE_DIR);
    // Do not cache templates if in development
    if(config::DEVELOPMENT) {
      $twig = new Twig_Environment($loader, array('debug' => TRUE));
    // Cache templates in production
    } else {
      $twig = new Twig_Environment($loader, array('cache' => './tmp/cache', 'auto_reload' => true));
    }
    // Needed for pluralization of variables
    $twig->addExtension(new Twig_Extensions_Extension_I18n());
    $twig->addGlobal('percent_browsable', self::get_percent_browsable());
    $twig->addGlobal('percent_not_browsable', self::get_percent_not_browsable());
    $template = $twig->loadTemplate($template_name);
    return $template->render($content);
  }
  
  /**
    * Gets specific vendor name
    *
    * @access public
    * @param int vendor_id
    * @return string Vendor name
    *
    */
  public static function get_vendor($vendor_id) {
    // Connect to database
    $database = new db;
    $db       = $database->connect();
    // Query the vendors table
    $sql      = 'SELECT vendor FROM vendors WHERE id = :vendor_id LIMIT 1';
    $query    = $db->prepare($sql);
    $query->bindParam(':vendor_id', $vendor_id);
    $query->execute();
    $vendor = $query->fetchAll(PDO::FETCH_ASSOC);
    $db = NULL;
    return $vendor[0]['vendor'];
  }
  
  /**
    * Gets specific platform name
    *
    * @access public
    * @param int platform_id
    * @return string Platform name
    *
    */
  public static function get_platform($platform_id) {
    // Connect to database
    $database = new db;
    $db    = $database->connect();
    $sql   = 'SELECT platforms.id AS platform_id, vendors.vendor AS vendor, platforms.platform AS platform FROM platforms INNER JOIN vendors ON platforms.vendor_id = vendors.id WHERE platforms.id = :platform_id LIMIT 1';
    $query = $db->prepare($sql);
    $query->bindParam(':platform_id', $platform_id);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_ASSOC);
    $db = NULL;
    return $results[0]['platform'] . ' (' . $results[0]['vendor'] . ')';
  }
  
  /**
    * Gets specific subject librarian name
    *
    * @access public
    * @param int lib_id
    * @return string Librarian name
    *
    */
  public static function get_lib($lib_id) {
    // Connect to database
    $database = new db;
    $db    = $database->connect();
    $sql   = 'SELECT id, first_name, last_name FROM libs WHERE id = :lib_id LIMIT 1';
    $query = $db->prepare($sql);
    $query->bindParam(':lib_id', $lib_id);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_ASSOC);
    $db = NULL;
    return $results[0]['first_name'] . ' ' . $results[0]['last_name'];
  }
  
  /**
    * Gets specific fund code name
    *
    * @access public
    * @param int fund_id
    * @return string Fund name
    *
    */
  public static function get_fund($fund_id) {
    // Connect to database
    $database = new db;
    $db    = $database->connect();
    $sql   = 'SELECT id AS fund_id, fund_code, fund_name FROM funds WHERE id = :fund_id LIMIT 1';
    $query = $db->prepare($sql);
    $query->bindParam(':fund_id', $fund_id);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_ASSOC);
    $db = NULL;
    return $results[0]['fund_code'] . ' (' . $results[0]['fund_name'] . ')';
  }
  
  /**
    * Gets specific call number range name
    *
    * @access public
    * @param int call_num_id
    * @return string Call number range name
    *
    */
  public static function get_call_num($call_num_id) {
    // Connect to database
    $database = new db;
    $db    = $database->connect();
    $sql   = 'SELECT id AS call_num_id, start_range, end_range, subject FROM call_nums WHERE id = :call_num_id LIMIT 1';
    $query = $db->prepare($sql);
    $query->bindParam(':call_num_id', $call_num_id);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_ASSOC);
    $db = NULL;
    if($results[0]['start_range'] === $results[0]['end_range']) {
      $call_number = $results[0]['start_range'];
    } else {
      $call_number = $results[0]['start_range'] . '–' . $results[0]['end_range'];
    }
    return $call_number . ' (' . $results[0]['subject'] . ')';
  }
  
  /**
    * Creates jQuery script text to adjust search boxes for less cluttered searching
    *
    * @access public
    * @param int vendor_id
    * @param int platform_id
    * @param int lib_id
    * @param int fund_id
    * @param int call_num_id
    * @return string jQuery script text to include on each page
    *
    */
  public static function get_dropdown_fix($vendor_id, $platform_id, $lib_id, $fund_id, $call_num_id) {
    $dropdown_fix = NULL;
    if(!$vendor_id) { $dropdown_fix .= "\$('#vendor').prop('selectedIndex', -1);"; }
    if(!$platform_id) { $dropdown_fix .= "\$('#platform').prop('selectedIndex', -1);"; }
    if(!$lib_id) { $dropdown_fix .= "\$('#lib').prop('selectedIndex', -1);"; }
    if(!$fund_id) { $dropdown_fix .= "\$('#fund').prop('selectedIndex', -1);"; }
    if(!$call_num_id) { $dropdown_fix .= "\$('#call_num').prop('selectedIndex', -1);"; }
    return $dropdown_fix;
  }
  
  /**
    * Returns int representing percent of database that is browsable
    *
    * @access public
    * @param NULL
    * @return int Percent of database that is browsable (must include call number to be browsable)
    *
    */
  public static function get_percent_browsable() {
    $title_count                = self::get_title_count();
    $title_count_with_call_nums = self::get_title_count_with_call_nums();
    return self::percent($title_count_with_call_nums, $title_count);
  }
  
  /**
    * Returns int representing percent of database that is not browsable
    *
    * @access public
    * @param NULL
    * @return int Percent of database that is not browsable (must include call number to be browsable)
    *
    */
  public static function get_percent_not_browsable() {
    return 100 - self::get_percent_browsable();
  }
  
  private static function get_title_count() {
    // Connect to database
    $database = new db;
    $db       = $database->connect();
    $sql      = "SELECT COUNT(*) AS count FROM books";
    $query = $db->query($sql);
    $f = $query->fetch();
    $result = $f['count'];
    $db = NULL;
    return $result;
  }
  
  private static function get_title_count_with_call_nums() {
    // Connect to database
    $database = new db;
    $db       = $database->connect();
    $sql      = "SELECT COUNT(*) AS count FROM books WHERE call_num IS NOT NULL";
    $query = $db->query($sql);
    $f = $query->fetch();
    $result = $f['count'];
    $db = NULL;
    return $result;
  }
  
  private static function percent($num_amount, $num_total) {
    $count1 = $num_amount / $num_total;
    $count2 = $count1 * 100;
    $count  = number_format($count2, 0);
    return $count;
  }
}
?>
