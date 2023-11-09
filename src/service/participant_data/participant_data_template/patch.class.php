<?php
/**
 * patch.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 */

namespace mastodon\service\participant_data\participant_data_template;
use cenozo\lib, cenozo\log, mastodon\util;

class patch extends \cenozo\service\patch
{
  /**
   * Extend parent property
   */
  protected static $base64_column_list = ['data' => 'application/pdf'];
}
