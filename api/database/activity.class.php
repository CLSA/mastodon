<?php
/**
 * activity.class.php
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
 * activity: record
 *
 * @package mastodon\database
 */
class activity extends record
{
  public static function get_min_datetime( $modifier = NULL )
  {
    $sql = sprintf( 'SELECT MIN( datetime ) FROM %s %s',
                    static::get_table_name(),
                    is_null( $modifier ) ? '' : $modifier->get_sql() );
    $datetime = static::db()->get_one( $sql );
    
    return is_null( $datetime )
      ? NULL
      : util::get_datetime_object( util::from_server_datetime( $datetime ) );
  }

  public static function get_max_datetime( $modifier = NULL )
  {
    $sql = sprintf( 'SELECT MAX( datetime ) FROM %s %s',
                    static::get_table_name(),
                    is_null( $modifier ) ? '' : $modifier->get_sql() );

    $datetime = static::db()->get_one( $sql );

    return is_null( $datetime )
      ? NULL
      : util::get_datetime_object( util::from_server_datetime( $datetime ) );
  }
}
?>
