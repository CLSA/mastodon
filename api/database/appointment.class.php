<?php
/**
 * appointment.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\database
 * @filesource
 */

namespace mastodon\database;
use mastodon\log, mastodon\util;
use mastodon\business as bus;
use mastodon\exception as exc;

/**
 * appointment: record
 *
 * @package mastodon\database
 */
class appointment extends record
{
  /**
   * Overrides the parent load method.
   * @author Patrick Emond
   * @access public
   */
  public function load()
  {
    parent::load();

    // appointments are not to the second, so remove the :00 at the end of the datetime field
    $this->datetime = substr( $this->datetime, 0, -3 );
  }

  /**
   * Identical to the parent's select method but restrict to a particular site.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param site $db_site The site to restrict the selection to.
   * @param modifier $modifier Modifications to the selection.
   * @param boolean $count If true the total number of records instead of a list
   * @return array( record ) | int
   * @static
   * @access public
   */
  public static function select_for_site( $db_site, $modifier = NULL, $count = false )
  {
    // if there is no site restriction then just use the parent method
    if( is_null( $db_site ) ) return parent::select( $modifier, $count );
    
    $select_tables = 'appointment, participant_primary_address, participant, address';
    
    // straight join the tables
    if( is_null( $modifier ) ) $modifier = new modifier();
    $modifier->where(
      'appointment.participant_id', '=', 'participant_primary_address.participant_id', false );
    $modifier->where( 'participant_primary_address.address_id', '=', 'address.id', false );
    $modifier->where( 'appointment.participant_id', '=', 'participant.id', false );

    $sql = sprintf( ( $count ? 'SELECT COUNT( %s.%s ) ' : 'SELECT %s.%s ' ).
                    'FROM %s '.
                    'WHERE ( participant.site_id = %d '.
                    '  OR address.region_id IN '.
                    '  ( SELECT id FROM region WHERE site_id = %d ) ) %s',
                    static::get_table_name(),
                    static::get_primary_key_name(),
                    $select_tables,
                    $db_site->id,
                    $db_site->id,
                    $modifier->get_sql( true ) );

    if( $count )
    {
      return intval( static::db()->get_one( $sql ) );
    }
    else
    {
      $id_list = static::db()->get_col( $sql );
      $records = array();
      foreach( $id_list as $id ) $records[] = new static( $id );
      return $records;
    }
  }

  /**
   * Identical to the parent's count method but restrict to a particular site.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param site $db_site The site to restrict the count to.
   * @param modifier $modifier Modifications to the count.
   * @return int
   * @static
   * @access public
   */
  public static function count_for_site( $db_site, $modifier = NULL )
  {
    return static::select_for_site( $db_site, $modifier, true );
  }
}
?>
