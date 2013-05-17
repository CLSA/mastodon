<?php
/**
 * user_view.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\widget;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * widget user view
 */
class user_view extends \cenozo\ui\widget\user_view
{
  /**
   * Overrides the access list widget's method (include all services)
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param database\modifier $modifier Modifications to the list.
   * @return int
   * @access protected
   */
  public function determine_access_count( $modifier = NULL )
  {
    $access_class_name = lib::get_class_name( 'database\access' );
    $site_restricted_list_class_name = lib::get_class_name( 'ui\widget\site_restricted_list' );
    if( NULL == $modifier ) $modifier = lib::create( 'database\modifier' );
    $modifier->where( 'user_id', '=', $this->get_record()->id );
    if( !$site_restricted_list_class_name::may_restrict() )
      $modifier->where( 'access.site_id', '=', lib::create( 'business\session' )->get_site()->id );
    return $access_class_name::count( $modifier );
  }

  /**
   * Overrides the access list widget's method (include all services)
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param database\modifier $modifier Modifications to the list.
   * @return array( record )
   * @access protected
   */
  public function determine_access_list( $modifier = NULL )
  {
    $access_class_name = lib::get_class_name( 'database\access' );
    $site_restricted_list_class_name = lib::get_class_name( 'ui\widget\site_restricted_list' );
    if( NULL == $modifier ) $modifier = lib::create( 'database\modifier' );
    $modifier->where( 'user_id', '=', $this->get_record()->id );
    if( !$site_restricted_list_class_name::may_restrict() )
      $modifier->where( 'access.site_id', '=', lib::create( 'business\session' )->get_site()->id );
    return $access_class_name::select( $modifier );
  }
}
