<?php
/**
 * consent_form_entry_new.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\ui
 * @filesource
 */

namespace mastodon\ui\push;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * push: consent_form_entry new
 *
 * Create a new consent_form_entry.
 * @package mastodon\ui
 */
class consent_form_entry_new extends \cenozo\ui\push\base_new
{
  /**
   * Constructor.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param array $args Push arguments
   * @access public
   */
  public function __construct( $args )
  {
    parent::__construct( 'consent_form_entry', $args );

    // we can't use a transaction, otherwise the semaphore in the finish() method won't work right
    lib::create( 'business\session' )->set_use_transaction( false );

    $consent_form_class_name = lib::get_class_name( 'database\consent_form' );
    $db_user = lib::create( 'business\session' )->get_user();

    // we need to use a semaphore to avoid race conditions
    $semaphore = sem_get( getmyinode() );
    if( !sem_acquire( $semaphore ) )
    {
      log::err( sprintf(
        'Unable to aquire semaphore for user id %d',
        $session->get_user()->id ) );
      throw lib::create( 'exception\notice',
        'The server is busy, please wait a few seconds then click the refresh button.',
        __METHOD__ );
    }

    // This new operation is different from others.  Instead of providing an ID the system must
    // instead search for one, reporting a notice if none are available
    $found = false;
    $consent_form_mod = lib::create( 'database\modifier' );
    $consent_form_mod->where( 'invalid', '!=', true );
    $consent_form_mod->where( 'consent_id', '=', NULL );
    $consent_form_mod->order( 'id' );
    foreach( $consent_form_class_name::select( $consent_form_mod ) as $db_consent_form )
    {
      // find a form which has less than 2 entries
      $consent_form_entry_mod = lib::create( 'database\modifier' );
      $consent_form_entry_mod->where( 'user_id', '=', $db_user->id );
      if( 0 == $db_consent_form->get_consent_form_entry_count( $consent_form_entry_mod ) &&
          2 > $db_consent_form->get_consent_form_entry_count() )
      {
        $this->arguments['columns']['consent_form_id'] = $db_consent_form->id;
        $this->arguments['columns']['user_id'] = $db_user->id;
        $found = true;
        break;
      }
    }

    // throw a notice if no form was found
    if( !$found ) throw lib::create( 'exception\notice',
      'There are currently no consent forms available for processing.',
      __METHOD__ );

    // release the semaphore
    if( !sem_release( $semaphore ) )
    {
      log::err( sprintf(
        'Unable to release semaphore for user id %d',
        $session->get_user()->id ) );
    }
  }
}
?>
