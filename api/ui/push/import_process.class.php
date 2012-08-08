<?php
/**
 * import_process.class.php
 *
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\push;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * push: import process
 * 
 * Processes a list of pending imported participants, adding them to the system.
 */
class import_process extends \cenozo\ui\push\base_record
{
  /**
   * Constructor.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param array $args Push arguments
   * @access public
   */
  public function __construct( $args )
  {
    parent::__construct( 'import', 'process', $args );
  }

  /**
   * This method executes the operation's purpose.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access protected
   */
  protected function execute()
  {
    parent::execute();

    // import all entries with no errors, then mark the import as processed
    $import_entry_mod = lib::create( 'database\modifier' );
    $import_entry_mod->where( 'apartment_error', '=', false );
    $import_entry_mod->where( 'address_error', '=', false );
    $import_entry_mod->where( 'province_error', '=', false );
    $import_entry_mod->where( 'postcode_error', '=', false );
    $import_entry_mod->where( 'home_phone_error', '=', false );
    $import_entry_mod->where( 'mobile_phone_error', '=', false );
    $import_entry_mod->where( 'duplicate_error', '=', false );
    $import_entry_mod->where( 'gender_error', '=', false );
    $import_entry_mod->where( 'date_of_birth_error', '=', false );
    $import_entry_mod->where( 'language_error', '=', false );
    $import_entry_mod->where( 'cohort_error', '=', false );
    $import_entry_mod->where( 'date_error', '=', false );
    foreach( $this->get_record()->get_import_entry_list( $import_entry_mod ) as $db_entry )
      $db_entry->import();

    $this->get_record()->processed = true;
    $this->get_record()->save();
  }
}
?>
