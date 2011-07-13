<?php
/**
 * permission.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\exception
 * @filesource
 */

namespace mastodon\exception;
use mastodon\log, mastodon\util;
use mastodon\business as bus;

/**
 * permission: permission exceptions
 * 
 * All exceptions which are due to denied permissions, use this class to throw exceptions.
 * @package mastodon\exception
 */
class permission extends base_exception
{
  /**
   * Constructor
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param database\operation $db_operation The associated operation.
   * @param string|int $context The exceptions context, either a function name or error code.
   * @param exception $previous The previous exception used for the exception chaining.
   * @access public
   */
  public function __construct( $db_operation, $context, $previous = NULL )
  {
    $this->operation = $db_operation;
    $message = is_null( $db_operation ) ||
               !is_object( $db_operation ) ||
               !is_a( $db_operation, 'mastodon\\database\\operation' )
             ? 'operation (unknown) denied'
             : sprintf( 'operation "%s %s %s" denied.',
                        $db_operation->type,
                        $db_operation->subject,
                        $db_operation->name );
    parent::__construct( $message, $context, $previous );
  }

  /**
   * The operation which was denied
   * @var database\site
   * @access protected
   */
  protected $operation = NULL;
}
?>
