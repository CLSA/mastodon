<?php
/**
 * head.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 */

namespace mastodon\service\participant_data_template;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * The base class of all head services
 */
class head extends \cenozo\service\head
{
  /**
   * Extends parent method
   */
  protected function setup()
  {
    parent::setup();

    $this->columns['filename'] = array(
      'data_type' => 'varchar',
      'default' => 'varchar(255)',
      'required' => $this->columns['data']['required']
    );
  }
}
