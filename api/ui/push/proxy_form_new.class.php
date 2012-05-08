<?php
/**
 * proxy_form_new.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\ui
 * @filesource
 */

namespace mastodon\ui\push;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * push: proxy_form new
 *
 * This is a special operation that creates a proxy form including a entry and immediately
 * attempts to import the data.  This is used by Beartooth in order to validate and import
 * proxy forms from Onyx.
 * @package mastodon\ui
 */
class proxy_form_new extends \cenozo\ui\push\base_new
{
  /**
   * Constructor.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param array $args Push arguments
   * @access public
   */
  public function __construct( $args )
  {
    parent::__construct( 'proxy_form', $args );
  }

  /**
   * Executes the push.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access public
   */
  public function finish()
  {
    parent::finish();

    // if a form variable was included try to decode it and store it as a proxy form
    $form = $this->get_argument( 'form', NULL );
    if( !is_null( $form ) ) 
    {
      $form_decoded = base64_decode( chunk_split( $form ) );
      if( false == $form_decoded )
        throw lib::create( 'exception\runtime', 'Unable to decode form argument.', __METHOD__ );

      // create a new proxy form
      $this->get_record()->scan = $form_decoded;
      $this->get_record()->save();
    }

    // if an entry was included add it and try importing the form immediately
    $entry = $this->get_argument( 'entry', NULL );    
    if( !is_null( $entry ) && is_array( $entry ) )
    {
      $db_proxy_form_entry = lib::create( 'database\proxy_form_entry' );
      $db_proxy_form_entry->proxy_form_id = $this->get_record()->id;
      $db_proxy_form_entry->deferred = false;
      $db_proxy_form_entry->signed = !is_null( $this->get_record()->scan );
      foreach( $entry as $column => $value ) $db_proxy_form_entry->$column = $value;

      // the user is in a noid argument
      $noid = $this->get_argument( 'noid' );
      $user_class_name = util::get_class_name( 'database\user' );
      $db_user = $user_class_name::get_unique_record( 'name', $noid['user.name'] );
      $db_proxy_form_entry->user_id = $db_user->id;

      $db_proxy_form_entry->save();

      // validate the entry
      $op_validate = lib::create( 'ui\pull\proxy_form_entry_validate',
                                  array( 'id' => $db_proxy_form_entry->id ) );
      $errors = $op_validate->finish();

      // no errors, so import the entry
      if( 0 == count( $errors ) ) $this->get_record()->import( $db_proxy_form_entry );
    }
  }
}
?>
