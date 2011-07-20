<?php
/**
 * participant.class.php
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
 * participant: record
 *
 * @package mastodon\database
 */
class participant extends person
{
  /**
   * Override get_address_list()
   * 
   * Since addresses are related to the person table and not the participant
   * table this method allows for direct access to the addresses.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param modifier $modifier A modifier to apply to the list
   * @return array( record )
   * @access public
   */
  public function get_address_list( $modifier = NULL )
  {
    return $this->get_person()->get_address_list( $modifier );
  }
  
  /**
   * Override get_address_count()
   * 
   * Since addresses are related to the person table and not the participant
   * table this method allows for direct access to the addresses.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param modifier $modifier A modifier to apply to the count
   * @return array( record )
   * @access public
   */
  public function get_address_count( $modifier = NULL )
  {
    return $this->get_person()->get_address_count( $modifier );
  }

  /**
   * Override get_phone_list()
   * 
   * Since phones are related to the person table and not the participant
   * table this method allows for direct access to the phones.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param modifier $modifier A modifier to apply to the list
   * @return array( record )
   * @access public
   */
  public function get_phone_list( $modifier = NULL )
  {
    return $this->get_person()->get_phone_list( $modifier );
  }
  
  /**
   * Override get_phone_count()
   * 
   * Since phones are related to the person table and not the participant
   * table this method allows for direct access to the phones.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param modifier $modifier A modifier to apply to the count
   * @return array( record )
   * @access public
   */
  public function get_phone_count( $modifier = NULL )
  {
    return $this->get_person()->get_phone_count( $modifier );
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

    // left join the participant_primary_address and address tables
    if( is_null( $modifier ) ) $modifier = new modifier();
    $sql = sprintf( ( $count ? 'SELECT COUNT( %s.%s ) ' : 'SELECT %s.%s ' ).
                    'FROM %s '.
                    'LEFT JOIN participant_primary_address '.
                    'ON %s.id = participant_primary_address.participant_id '.
                    'LEFT JOIN address '.
                    'ON participant_primary_address.address_id = address.id '.
                    'WHERE ( %s.site_id = %d '.
                    '  OR ( %s.site_id IS NULL '.
                    '    AND address.region_id IN ( '.
                    '      SELECT id FROM region WHERE site_id = %d ) ) ) %s',
                    static::get_table_name(),
                    static::get_primary_key_name(),
                    static::get_table_name(),
                    static::get_table_name(),
                    static::get_table_name(),
                    $db_site->id,
                    static::get_table_name(),
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
  
  /**
   * Get the participant's last consent
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @return consent
   * @access public
   */
  public function get_last_consent()
  {
    // check the primary key value
    if( is_null( $this->id ) )
    {
      log::warning( 'Tried to query participant with no id.' );
      return NULL;
    }

    // need custom SQL
    $consent_id = static::db()->get_one(
      sprintf( 'SELECT consent_id '.
               'FROM participant_last_consent '.
               'WHERE participant_id = %s',
               database::format_string( $this->id ) ) );
    return $consent_id ? new consent( $consent_id ) : NULL;
  }

  /**
   * Get the participant's "primary" address.  This is the highest ranking canadian address.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @return address
   * @access public
   */
  public function get_primary_address()
  {
    // check the primary key value
    if( is_null( $this->id ) )
    {
      log::warning( 'Tried to query participant with no id.' );
      return NULL;
    }
    
    // need custom SQL
    $address_id = static::db()->get_one(
      sprintf( 'SELECT address_id FROM participant_primary_address WHERE participant_id = %s',
               database::format_string( $this->id ) ) );
    return $address_id ? new address( $address_id ) : NULL;
  }

  /**
   * Get the participant's "first" address.  This is the highest ranking, active, available
   * address.
   * Note: this address may be in the United States
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @return address
   * @access public
   */
  public function get_first_address()
  {
    // check the primary key value
    if( is_null( $this->id ) )
    {
      log::warning( 'Tried to query participant with no id.' );
      return NULL;
    }
    
    // need custom SQL
    $address_id = static::db()->get_one(
      sprintf( 'SELECT address_id FROM participant_first_address WHERE participant_id = %s',
               database::format_string( $this->id ) ) );
    return $address_id ? new address( $address_id ) : NULL;
  }

  /**
   * Get the site that the participant belongs to.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @return site
   * @access public
   */
  public function get_primary_site()
  {
    $db_site = NULL;

    if( !is_null( $this->site_id ) )
    { // site is specifically defined
      $db_site = $this->get_site();
    }
    else
    {
      $db_address = $this->get_primary_address();
      if( !is_null( $db_address ) )
      { // there is a primary address
        $db_site = $db_address->get_region()->get_site();
      }
    }

    return $db_site;
  }
}
?>
