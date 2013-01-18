<?php
/**
 * base_participant_base.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\push;

/**
 * Interface that specifies that a push class only sends machine requests based on the
 * sync state of a participant record.
 */
interface base_participant_base
{
  /**
   * Define the participant record which should be used for determining whether to
   * sync the data and with which external services.
   * In order for data to be passed to external services this method must be called
   * in the implementing class' extending class' prepare() methods.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access protected
   */
  public function set_participant_for_machine_requests( $db_participant );
}
