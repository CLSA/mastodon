<?php
/**
 * get.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 */

namespace mastodon\service\participant_data_template;
use cenozo\lib, cenozo\log, cenozo\util;

class get extends \cenozo\service\get
{
  /**
   * Extend parent property
   */
  protected static $base64_column_list = ['data' => 'application/pdf'];
}
