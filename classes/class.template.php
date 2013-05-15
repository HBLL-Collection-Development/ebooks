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
    echo self::templatize($template_name, $content);
  }

  private static function templatize($template_name, $content) {
    require_once './libs/Twig/Autoloader.php';
    // Inject format into content array
    $format = array('format' => $format);
    $content = array_merge($format, $content);
    // Call template
    Twig_Autoloader::register();
    $loader = new Twig_Loader_Filesystem('./templates');
    // Do not cache templates if in development
    if(config::DEVELOPMENT) {
      $twig = new Twig_Environment($loader);
    // Cache templates in production
    } else {
      $twig = new Twig_Environment($loader, array('cache' => './tmp/cache'));
    }
    // Needed for pluralization of variables
    $twig->addExtension(new Twig_Extensions_Extension_I18n());
    $template = $twig->loadTemplate($template_name);
    return $template->render($content);
  }
}
?>
