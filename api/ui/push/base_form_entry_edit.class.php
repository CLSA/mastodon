<?php
/**
 * base_form_entry_edit.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\ui
 * @filesource
 */

namespace mastodon\ui\push;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * Base class for all form entry edit operations.
 *
 * @package mastodon\ui
 */
abstract class base_form_entry_edit extends \cenozo\ui\push\base_edit
{
  /**
   * Constructor.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param string $form_type The type of form being listed.
   * @param array $args Push arguments
   * @access public
   */
  public function __construct( $form_type, $args )
  {
    parent::__construct( $form_type.'_form_entry', $args );
    $this->form_type = $form_type;
  }

  /**
   * Override the parent in case the entry is being un-deferred
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access public
   */
  public function finish()
  {
    $columns = $this->get_argument( 'columns' );

    // if we are changing the defer boolean to false then validate and submit the entry
    if( array_key_exists( 'deferred', $columns ) && !$columns['deferred'] )
    {
      $validate_class_name = sprintf( 'ui\pull\%s_form_entry_validate', $this->form_type );
      $get_form_method_name = sprintf( 'get_%s_form', $this->form_type );

      $op_validate = lib::create( $validate_class_name, array( 'id' => $this->get_record()->id ) );
      $errors = $op_validate->finish();
      if( 0 < count( $errors ) )
        throw lib::create( 'exception\runtime',
          sprintf( 'Tried to submit %s form entry that has %d errors.',
                   $this->form_type,
                   count( $errors ) ),
          __METHOD__ );

      parent::finish();
      $this->get_record()->$get_form_method_name()->import( $this->get_record() );
    }
    else parent::finish();
  }

  /**
   * The type of form (ie: consent, contact, proxy)
   * @var string $form_type
   * @access private
   */
  private $form_type;
}
?>
