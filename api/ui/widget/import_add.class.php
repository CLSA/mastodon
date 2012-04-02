<?php
/**
 * import_add.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\ui
 * @filesource
 */

namespace mastodon\ui\widget;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * widget import add
 * 
 * @package mastodon\ui
 */
class import_add extends \cenozo\ui\widget
{
  /**
   * Constructor
   * 
   * Defines all variables which need to be set for the associated template.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param array $args An associative array of arguments to be processed by the widget
   * @access public
   */
  public function __construct( $args )
  {
    parent::__construct( 'import', 'add', $args );
  }

  /**
   * Finish setting the variables in a widget.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access public
   */
  public function finish()
  {
    parent::finish();

    $md5 = $this->get_argument( 'md5', false );
    $this->set_variable( 'md5', $md5 );

    if( $md5 )
    {
      // get the import file matching the md5 hash
      $import_class_name = lib::get_class_name( 'database\import' );
      $db_import = $import_class_name::get_unique_record( 'md5', $md5 );
      if( is_null( $db_import ) )
        throw lib::create( 'exception\argument', 'md5', $md5, __METHOD__ );

      $this->set_variable( 'id', $db_import->id );
      $this->set_variable( 'filename', $db_import->name );
      $this->set_variable( 'rows', $db_import->get_import_entry_count() );

      // get the number of valid rows
      $entry_mod = lib::create( 'database\modifier' );
      $entry_mod->where( 'address_error', '=', false );
      $entry_mod->where( 'province_error', '=', false );
      $entry_mod->where( 'postcode_error', '=', false );
      $entry_mod->where( 'home_phone_error', '=', false );
      $entry_mod->where( 'mobile_phone_error', '=', false );
      $entry_mod->where( 'duplicate_error', '=', false );
      $this->set_variable(
        'valid_count', $db_import->get_import_entry_count( $entry_mod ) );

      // get all address error rows
      $address_error_list = array();
      $entry_mod = lib::create( 'database\modifier' );
      $entry_mod->where( 'address_error', '=', true );
      foreach( $db_import->get_import_entry_list( $entry_mod ) as $db_entry )
        $address_error_list[] = $db_entry->row;
      $this->set_variable( 'address_error_list', $address_error_list );

      // get all province error rows
      $province_error_list = array();
      $entry_mod = lib::create( 'database\modifier' );
      $entry_mod->where( 'province_error', '=', true );
      foreach( $db_import->get_import_entry_list( $entry_mod ) as $db_entry )
        $province_error_list[] = $db_entry->row;
      $this->set_variable( 'province_error_list', $province_error_list );

      // get all postcode error rows
      $postcode_error_list = array();
      $entry_mod = lib::create( 'database\modifier' );
      $entry_mod->where( 'postcode_error', '=', true );
      foreach( $db_import->get_import_entry_list( $entry_mod ) as $db_entry )
        $postcode_error_list[] = $db_entry->row;
      $this->set_variable( 'postcode_error_list', $postcode_error_list );

      // get all home_phone error rows
      $home_phone_error_list = array();
      $entry_mod = lib::create( 'database\modifier' );
      $entry_mod->where( 'home_phone_error', '=', true );
      foreach( $db_import->get_import_entry_list( $entry_mod ) as $db_entry )
        $home_phone_error_list[] = $db_entry->row;
      $this->set_variable( 'home_phone_error_list', $home_phone_error_list );

      // get all mobile_phone error rows
      $mobile_phone_error_list = array();
      $entry_mod = lib::create( 'database\modifier' );
      $entry_mod->where( 'mobile_phone_error', '=', true );
      foreach( $db_import->get_import_entry_list( $entry_mod ) as $db_entry )
        $mobile_phone_error_list[] = $db_entry->row;
      $this->set_variable( 'mobile_phone_error_list', $mobile_phone_error_list );

      // get all duplicate error rows
      $duplicate_error_list = array();
      $entry_mod = lib::create( 'database\modifier' );
      $entry_mod->where( 'duplicate_error', '=', true );
      foreach( $db_import->get_import_entry_list( $entry_mod ) as $db_entry )
        $duplicate_error_list[] = $db_entry->row;
      $this->set_variable( 'duplicate_error_list', $duplicate_error_list );
    }
  }
}
?>
