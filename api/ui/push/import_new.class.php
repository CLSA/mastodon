<?php
/**
 * import_new.class.php
 *
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\push;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * push: import new
 *
 * Import a list of new participants.
 */
class import_new extends \cenozo\ui\push
{
  /**
   * Constructor.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param array $args Push arguments
   * @access public
   */
  public function __construct( $args )
  {
    parent::__construct( 'import', 'new', $args );
  }

  /**
   * Validate the operation.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @throws exception\notice
   * @access protected
   */
  protected function validate()
  {
    parent::validate();

    if( 0 == $_SERVER['CONTENT_LENGTH'] )
      throw lib::create( 'exception\notice',
        'Tried to import participant data without a valid CSV file.',
        __METHOD__ );
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

    $import_class_name = lib::get_class_name( 'database\import' );

    // store the data
    $filename = $_SERVER['HTTP_X_FILENAME'];
    $data = utf8_encode( file_get_contents( 'php://input' ) );
    $md5 = md5( $data );

    // see if the file has already been processed either by name or md5 hash
    $db_import = $import_class_name::get_unique_record( 'name', $filename );
    if( !is_null( $db_import ) )
    {
      if( $db_import->processed )
        throw lib::create( 'exception\notice',
          'A file by the same name has already been imported.', __METHOD__ );

      // delete the old entries
      foreach( $db_import->get_import_entry_list() as $db_import_entry )
        $db_import_entry->delete();
    }
    else
    {
      $db_import = $import_class_name::get_unique_record( 'md5', $md5 );
      if( !is_null( $db_import ) )
      {
        if( $db_import->processed )
          throw lib::create( 'exception\notice',
            'This file has already been imported under a different name ('.
            $db_import->name.').', __METHOD__ );

        // delete the old entries
        foreach( $db_import->get_import_entry_list() as $db_import_entry )
          $db_import_entry->delete();
      }
    }

    if( is_null( $db_import ) ) $db_import = lib::create( 'database\import' );

    // update the import record
    $db_import->name = $filename;
    $db_import->date = util::get_datetime_object()->format( 'Y-m-d' );
    $db_import->processed = false;
    $db_import->md5 = $md5;
    $db_import->data = $data;
    $db_import->save();

    // now process the data
    $row = 1;
    foreach( preg_split( '/[\n\r]+/', $data ) as $line )
    {
      $values = str_getcsv( $line );

      // skip header line(s)
      if( 'first_name' == $values[0] || 'last_name' == $values[0] ) continue;

      if( 35 == count( $values ) )
      {
        $db_import_entry = lib::create( 'database\import_entry' );
        $db_import_entry->import_id = $db_import->id;
        $db_import_entry->row = $row;
        $db_import_entry->first_name = $values[0];
        $db_import_entry->last_name = $values[1];
        $db_import_entry->apartment = '' == $values[2] ? NULL : $values[2];
        $db_import_entry->street = $values[3];
        $db_import_entry->address_other = '' == $values[4] ? NULL : $values[4];
        $db_import_entry->city = $values[5];
        $db_import_entry->province = $values[6];
        $db_import_entry->postcode = $values[7];
        $db_import_entry->home_phone = $values[8];
        // null mobile phone entries may be 999-999-9999
        $db_import_entry->mobile_phone =
          '' == $values[9] || '999-999-9999' == $values[9] || $values[8] == $values[9] ?
          NULL : $values[9];
        if( 0 == strcasecmp( 'home', $values[10] ) )
          $db_import_entry->phone_preference = 'home';
        else if( 0 == strcasecmp( 'cell', $values[10] ) )
          $db_import_entry->phone_preference = 'mobile';
        $db_import_entry->email = '' == $values[11] ? NULL : $values[11];
        if( 0 == strcasecmp( 'f', $values[12] ) ) $db_import_entry->gender = 'female';
        else if( 0 == strcasecmp( 'm', $values[12] ) ) $db_import_entry->gender = 'male';
        else $db_import_entry->gender = '';
        $db_import_entry->date_of_birth = $values[13];
        $db_import_entry->monday = 0 == strcasecmp( 'y', $values[14] );
        $db_import_entry->tuesday = 0 == strcasecmp( 'y', $values[15] );
        $db_import_entry->wednesday = 0 == strcasecmp( 'y', $values[16] );
        $db_import_entry->thursday = 0 == strcasecmp( 'y', $values[17] );
        $db_import_entry->friday = 0 == strcasecmp( 'y', $values[18] );
        $db_import_entry->saturday = 0 == strcasecmp( 'y', $values[19] );
        $db_import_entry->time_9_10 = 0 == strcasecmp( 'y', $values[20] );
        $db_import_entry->time_10_11 = 0 == strcasecmp( 'y', $values[21] );
        $db_import_entry->time_11_12 = 0 == strcasecmp( 'y', $values[22] );
        $db_import_entry->time_12_13 = 0 == strcasecmp( 'y', $values[23] );
        $db_import_entry->time_13_14 = 0 == strcasecmp( 'y', $values[24] );
        $db_import_entry->time_14_15 = 0 == strcasecmp( 'y', $values[25] );
        $db_import_entry->time_15_16 = 0 == strcasecmp( 'y', $values[26] );
        $db_import_entry->time_16_17 = 0 == strcasecmp( 'y', $values[27] );
        $db_import_entry->time_17_18 = 0 == strcasecmp( 'y', $values[28] );
        $db_import_entry->time_18_19 = 0 == strcasecmp( 'y', $values[29] );
        $db_import_entry->time_19_20 = 0 == strcasecmp( 'y', $values[30] );
        $db_import_entry->time_20_21 = 0 == strcasecmp( 'y', $values[31] );
        $db_import_entry->language = '' == $values[32] ? NULL : $values[32];
        $db_import_entry->cohort = strtolower( $values[33] );
        $db_import_entry->date = $values[34];
        $db_import_entry->validate();
        try
        {
          $db_import_entry->save();
        }
        catch( \cenozo\exception\database $e )
        {
          throw lib::create( 'exception\notice',
            sprintf( 'There was a problem importing row %d.', $row ), __METHOD__, $e );
        }
      }

      $row++;
    }
  }
}
?>
