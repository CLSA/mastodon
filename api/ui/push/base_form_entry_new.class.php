<?php
/**
 * base_form_entry_new.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\push;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * Base class for all form_entry new operations
 */
abstract class base_form_entry_new extends \cenozo\ui\push\base_new
{
  /**
   * Constructor.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param string $form_type The type of form being adjudicated.
   * @param array $args Push arguments
   * @access public
   */
  public function __construct( $form_type, $args )
  {
    parent::__construct( $form_type.'_form_entry', $args );
    $this->form_type = $form_type;

    // we can't use a transaction, otherwise the semaphore in the finish() method won't work right
    lib::create( 'business\session' )->set_use_transaction( false );

    // get the form and form_entry (dynamic) names
    $form_name = $this->form_type.'_form';
    $form_id_name = $form_name.'_id';
    $form_entry_list_class_name = lib::get_class_name( 'database\\'.$form_name );
    $form_class_name = lib::get_class_name( 'database\\'.$form_name );
    $form_entry_count_method_name = sprintf( 'get_%s_count', $this->get_subject() );

    $db_user = lib::create( 'business\session' )->get_user();

    // we need to use a semaphore to avoid race conditions
    $semaphore = sem_get( getmyinode() );
    if( !sem_acquire( $semaphore ) )
    {
      log::err( sprintf( 'Unable to aquire semaphore for user "%s"', $db_user()->name ) );
      throw lib::create( 'exception\notice',
        'The server is busy, please wait a few seconds then click the refresh button.',
        __METHOD__ );
    }

    // This new operation is different from others.  Instead of providing an ID the system must
    // instead search for one, reporting a notice if none are available
    $found = false;
    if( is_null( $this->form_mod ) ) $this->form_mod = lib::create( 'database\modifier' );
    $this->form_mod->where( 'invalid', '=', false );
    $this->form_mod->where( 'complete', '=', false );
    $this->form_mod->order( 'id' );
    foreach( $form_class_name::select( $this->form_mod ) as $db_form )
    {
      // find a form which has less than 2 entries
      $form_entry_mod = lib::create( 'database\modifier' );
      $form_entry_mod->where( 'user_id', '=', $db_user->id );
      if( 0 == $db_form->$form_entry_count_method_name( $form_entry_mod ) &&
          2 > $db_form->$form_entry_count_method_name() )
      {
        $this->arguments['columns'][$form_id_name] = $db_form->id;
        $this->arguments['columns']['user_id'] = $db_user->id;
        $found = true;
        break;
      }
    }

    // throw a notice if no form was found
    if( !$found ) throw lib::create( 'exception\notice',
      sprintf( 'There are currently no %ss available for processing.',
               str_replace( '_', ' ', $this->get_subject() ) ),
      __METHOD__ );

    // release the semaphore
    if( !sem_release( $semaphore ) )
      log::err( sprintf( 'Unable to release semaphore for user %s', $db_user->name ) );
  }

  /**
   * The type of form (ie: consent, contact, proxy)
   * @var string $form_type
   * @access private
   */
  private $form_type;

  /**
   * The modifier used when selecting a new form.
   * @var database\modifier $form_mod
   * @access protected
   */
  private $form_mod;
}
?>
