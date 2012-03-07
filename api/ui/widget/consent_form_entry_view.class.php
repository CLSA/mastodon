<?php
/**
 * consent_form_entry_view.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\ui
 * @filesource
 */

namespace mastodon\ui\widget;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * widget consent_form_entry view
 * 
 * @package mastodon\ui
 */
class consent_form_entry_view extends \cenozo\ui\widget\base_view
{
  /**
   * Constructor
   * 
   * Defines all variables which need to be set for the associated template.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param array $args An associative array of arguments to be processed by the widget
   * @access public
   */
  public function __construct( $args )
  {
    parent::__construct( 'consent_form_entry', 'view', $args );

    // validate the entry and insert error values as notes
    $args = array( 'id' => $this->get_argument( 'id' ) );
    $operation = lib::create( 'ui\pull\consent_form_entry_validate', $args );
    $errors = $operation->finish();

    $this->add_item( 'uid', 'string', 'CLSA ID',
      array_key_exists( 'uid', $errors ) ? $errors['uid'] : NULL, true );
    $this->add_item( 'option_1', 'boolean', 'Option #1',
      array_key_exists( 'option_1', $errors ) ? $errors['option_1'] : NULL, true );
    $this->add_item( 'option_2', 'boolean', 'Option #2',
      array_key_exists( 'option_2', $errors ) ? $errors['option_2'] : NULL, true );
    $this->add_item( 'date', 'date', 'Date',
      array_key_exists( 'date', $errors ) ? $errors['date'] : NULL, true );
  }

  /**
   * Finish setting the variables in a widget.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access public
   */
  public function finish()
  {
    parent::finish();

    // if the uid is valid then display the participant's name as a note
    $uid = $this->get_record()->uid;
    if( is_string( $uid ) )
    {
      $participant_class_name = lib::get_class_name( 'database\participant' );
      $db_participant = $participant_class_name::get_unique_record( 'uid', $uid );
      if( !is_null( $db_participant ) )
        $this->set_note( 'uid', sprintf(
          'Participant found: %s %s',
          $db_participant->first_name,
          $db_participant->last_name ) );
    }

    $this->set_item( 'uid', $this->get_record()->uid, false );
    $this->set_item( 'option_1', $this->get_record()->option_1, false );
    $this->set_item( 'option_2', $this->get_record()->option_2, false );
    $this->set_item( 'date', $this->get_record()->date, false );

    $this->finish_setting_items();

    $this->set_variable( 'form_name', 'consent_form' );
    $this->set_variable( 'form_id', $this->get_record()->consent_form_id );
  }
}
?>
