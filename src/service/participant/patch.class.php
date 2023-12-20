<?php
/**
 * patch.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 */

namespace mastodon\service\participant;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * Special service for handling the patch meta-resource
 */
class patch extends \cenozo\service\participant\patch
{
  /**
   * Override parent method
   */
  protected function prepare()
  {
    $this->extract_parameter_list[] = 'application_id';

    parent::prepare();
  }

  /**
   * Override parent method
   */
  protected function validate()
  {
    parent::validate();

    if( $this->may_continue() )
    {
      // a specific application_id may be provided when updating the participant's preferred site
      $application_id = $this->get_argument( 'application_id', NULL );
      if( !is_null( $application_id ) )
      {
        try
        {
          $this->db_application = lib::create( 'database\application', $application_id );
        }
        catch( \cenozo\exception\runtime $e )
        {
          $this->status->set_code( 400 );
        }
      }
    }
  }

  /**
   * Override parent method
   */
  protected function set_preferred_site()
  {
    $this->get_leaf_record()->set_preferred_site(
      $this->db_application,
      $this->get_argument( 'preferred_site_id' )
    );
  }

  /**
   * Which application to set the preferred site for
   * @var database\application
   * @access protected
   */
  protected $db_application = NULL;
}
