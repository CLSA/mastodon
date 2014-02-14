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
    $region_class_name = lib::get_class_name( 'database\region' );
    $source_class_name = lib::get_class_name( 'database\source' );

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
      if( 'first_name' == $values[0] ||
          'last_name' == $values[0] ||
          'id' == $values[0] ) continue;

      if( 35 == count( $values ) )
      {
        $db_source = $source_class_name::get_unique_record( 'name', 'rdd' );
        $db_import_entry = lib::create( 'database\import_entry' );
        $db_import_entry->import_id = $db_import->id;
        $db_import_entry->source_id = $db_source->id;
        $db_import_entry->row = $row;
        $db_import_entry->first_name = $values[0];
        $db_import_entry->last_name = $values[1];
        $db_import_entry->apartment = 0 == strlen( $values[2] ) ? NULL : $values[2];
        $db_import_entry->street = $values[3];
        $db_import_entry->address_other = 0 == strlen( $values[4] ) ? NULL : $values[4];
        $db_import_entry->city = $values[5];
        $db_import_entry->province = $values[6];
        $db_import_entry->postcode = $values[7];
        $db_import_entry->home_phone = $values[8];
        // null mobile phone entries may be 999-999-9999
        $db_import_entry->mobile_phone =
          0 == strlen( $values[9] ) || '999-999-9999' == $values[9] || $values[8] == $values[9] ?
          NULL : $values[9];
        if( 0 == strcasecmp( 'home', $values[10] ) )
          $db_import_entry->phone_preference = 'home';
        else if( 0 == strcasecmp( 'cell', $values[10] ) )
          $db_import_entry->phone_preference = 'mobile';
        $db_import_entry->email = 0 == strlen( $values[11] ) ? NULL : $values[11];
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
        $db_import_entry->language = 0 == strlen( $values[32] ) ? NULL : $values[32];
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
      else if( 18 == count( $values ) )
      { // limesurvey export csv file
        // check for an empty line
        if( 0 == strlen( $values[3] ) && 0 == strlen( $values[4] ) ) continue;

        $appartment = trim( preg_replace( '/apt\.?/i', '', $values[6] ) );
        if( 0 == strlen( $appartment ) ||
            '--' == $appartment ||
            '---' == $appartment ||
            0 == strcasecmp( 'aucun', $appartment ) ||
            0 == strcasecmp( 'na', $appartment ) ||
            0 == strcasecmp( 'n/a', $appartment ) ) $appartment = NULL;
            
        $address1 = trim( $values[7] );
        if( '--' == $address1 ||
            '---' == $address1 ||
            0 == strcasecmp( 'aucun', $address1 ) ||
            0 == strcasecmp( 'na', $address1 ) ||
            0 == strcasecmp( 'n/a', $address1 ) ) $address = '';

        $address2 = trim( $values[8] );
        if( '--' == $address2 ||
            '---' == $address2 ||
            0 == strcasecmp( 'aucun', $address2 ) ||
            0 == strcasecmp( 'na', $address2 ) ||
            0 == strcasecmp( 'n/a', $address2 ) ||
            $address1 == $address2 ) $address2 = '';

        if( 0 == strlen( $address1 ) && 0 < strlen( $address2 ) )
        {
          $address1 = $address2;
          $address2 = '';
        }

        $region = $values[10];
        $db_region = $region_class_name::get_unique_record( 'abbreviation', $region );
        if( is_null( $db_region ) )
          $db_region = $region_class_name::get_unique_record( 'name', $region );

        $db_source = $source_class_name::get_unique_record( 'name', 'clsapr' );
        $db_import_entry = lib::create( 'database\import_entry' );
        $db_import_entry->import_id = $db_import->id;
        $db_import_entry->source_id = $db_source->id;
        $db_import_entry->row = $row;
        $db_import_entry->first_name = $values[3];
        $db_import_entry->last_name = $values[4];
        $db_import_entry->apartment = $appartment;
        $db_import_entry->street = $address1;
        $db_import_entry->address_other = 0 == strlen( $address2 ) ? NULL : $address2;
        $db_import_entry->city = $values[9];
        $db_import_entry->province = is_null( $db_region ) ? NULL : $db_region->abbreviation;
        $db_import_entry->postcode = trim( $values[11] );
        if( 6 == strlen( $db_import_entry->postcode ) )
          $db_import_entry->postcode = substr( $db_import_entry->postcode, 0, 3 ).' '
                                     . substr( $db_import_entry->postcode, 3, 3 );
        $db_import_entry->home_phone =
          0 == strlen( $values[12] ) ||
          0 === strpos( preg_replace( '/[^0-9]/', '', $values[12] ), '9999999' ) ?
          NULL : $values[12];
        $db_import_entry->mobile_phone =
          0 == strlen( $values[13] ) ||
          0 === strpos( preg_replace( '/[^0-9]/', '', $values[13] ), '9999999' ) ||
          $values[12] == $values[13] ?
          NULL : $values[13];

        $db_import_entry->phone_preference = 'home';

        $email = $values[14];
        $db_import_entry->email = util::validate_email( $email ) ? $email : NULL;

        // column index 2 has "GENDER XX-XX" (sex age-group)
        $parts = explode( ' ', $values[5] );
        if( 2 == count( $parts ) )
        {
          $db_import_entry->gender = strtolower( $parts[0] );

          $year = date( 'Y' );
          if( '45-54' == $parts[1] )
            $db_import_entry->date_of_birth = sprintf( '%d-01-01', $year - 50 );
          else if( '55-64' == $parts[1] )
            $db_import_entry->date_of_birth = sprintf( '%d-01-01', $year - 60 );
          else if( '65-74' == $parts[1] )
            $db_import_entry->date_of_birth = sprintf( '%d-01-01', $year - 70 );
          else if( '75-85' == $parts[1] )
            $db_import_entry->date_of_birth = sprintf( '%d-01-01', $year - 80 );
        }
        $db_import_entry->monday = false;
        $db_import_entry->tuesday = false;
        $db_import_entry->wednesday = false;
        $db_import_entry->thursday = false;
        $db_import_entry->friday = false;
        $db_import_entry->saturday = false;
        $db_import_entry->time_9_10 = false;
        $db_import_entry->time_10_11 = false;
        $db_import_entry->time_11_12 = false;
        $db_import_entry->time_12_13 = false;
        $db_import_entry->time_13_14 = false;
        $db_import_entry->time_14_15 = false;
        $db_import_entry->time_15_16 = false;
        $db_import_entry->time_16_17 = false;
        $db_import_entry->time_17_18 = false;
        $db_import_entry->time_18_19 = false;
        $db_import_entry->time_19_20 = false;
        $db_import_entry->time_20_21 = false;
        $db_import_entry->language = 0 == strcasecmp( 'french', $values[2] ) ? 'fr' : 'en';
        $db_import_entry->cohort = 'tracking';
        $db_import_entry->date = util::get_datetime_object()->format( 'Y-m-d' );
        $operator_first = $values[15];
        $operator_last = $values[16];
        $operator = trim( $operator_first.' '.$operator_last );
        $uid = $values[17];
        $db_import_entry->note = sprintf(
          'Pre-recruitment UID %s, operator %s',
          $uid ? $uid : 'unknown',
          $operator ? $operator : 'unknown' );
        $db_import_entry->validate();
        try
        {
          $db_import_entry->save();
        }
        catch( \cenozo\exception\database $e )
        {
          $output = $e->get_message();
          $parts = explode( $output[0], $output );
            
          $message = sprintf( 'There was a problem importing row %d%s',
                              $row,
                              array_key_exists( 2, $parts ) ? ' ('.$parts[2].')' : '' );
          throw lib::create( 'exception\notice', $message, __METHOD__, $e );
        }
      }

      $row++;
    }
  }
}
