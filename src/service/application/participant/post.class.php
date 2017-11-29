<?php
/**
 * post.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 */

namespace mastodon\service\application\participant;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * The base class of all post services.
 */
class post extends \cenozo\service\write
{
  /**
   * Extends parent constructor
   */
  public function __construct( $path, $args, $file )
  {
    parent::__construct( 'POST', $path, $args, $file );
  }

  /**
   * Override parent method
   */
  protected function execute()
  {
    parent::execute();

    // adding relationship between application and participant is special
    $post_object = $this->get_file_as_object();
    if( !is_int( $post_object ) && !is_array( $post_object ) )
    {
      $this->status->set_code( 400 );
      throw lib::create( 'exception\argument', 'post_object', $post_object, __METHOD__ );
    }

    $modifier = lib::create( 'database\modifier' );
    if( is_array( $post_object ) ) $modifier->where( 'participant.id', 'IN', $post_object );
    else $modifier->where( 'participant.id', '=', $post_object );
    $this->get_parent_record()->release_participants( $modifier );
  }
}
