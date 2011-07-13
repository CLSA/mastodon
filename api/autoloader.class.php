<?php
/**
 * autoloader.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon
 * @filesource
 */

namespace mastodon;

/**
 * Autoloader class which automatically includes project class files.
 * @package mastodon
 */
class autoloader
{
  /**
   * Registers this class with PHP as an autoloader.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @static
   * @access public
   */
  static public function register()
  {
    ini_set( 'unserialize_callback_func', 'spl_autoload_call' );
    spl_autoload_register( array( new self, 'autoload' ) );
  }

  /**
   * This method is called by PHP whenever an undefined class is used.
   * If the class is in the mastodon\ namespace it attemps to load it from the api/ directory.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @static
   * @throws exception\runtime
   * @access public
   */
  static public function autoload( $class )
  {
    // only work on classes starting with mastodon\
    if( 0 !== strpos( $class, 'mastodon\\' ) ) return;

    // build the path based on the class' name and namespace
    $file_base = API_PATH.str_replace( '\\', '/', substr( $class, strpos( $class, '\\' ) ) );
    
    // check for a class by this name
    $file = $file_base.'.class.php';
    if( file_exists( $file ) )
    {
      require $file;
      return;
    }

    // check for an interface by this name
    $file = $file_base.'.interface.php';
    if( file_exists( $file ) )
    {
      require $file;
      return;
    }
    
    // if we get here then the file is missing
    throw new exception\runtime( 'Missing class: '.$class, __METHOD__ );
  }
}
