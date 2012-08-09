<?php
/**
 * base_form_entry_view.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\widget;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * Base class for form entry view widgets
 */
abstract class base_form_entry_view extends \cenozo\ui\widget\base_view
{
  /**
   * Constructor
   * 
   * Defines all variables which need to be set for the associated template.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param string $form_type The type of form being viewed.
   * @param array $args An associative array of arguments to be processed by the widget
   * @access public
   */
  public function __construct( $form_type, $args )
  {
    parent::__construct( $form_type.'_form_entry', 'view', $args );
    $this->form_type = $form_type;
  }

  /**
   * Sets up the operation with any pre-execution instructions that may be necessary.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access protected
   */
  protected function setup()
  {
    parent::setup();

    // if a uid field exists then automatically display the associated user as a note
    $form_entry_class_name = lib::get_class_name( sprintf( 'database\%s', $this->get_subject() ) );
    if( $form_entry_class_name::column_exists( 'uid' ) )
    {
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
    }

    // validate the entry and insert error values as notes
    $args = array( 'id' => $this->get_argument( 'id' ) );
    $operation = lib::create( sprintf( 'ui\pull\%s_validate', $this->get_subject() ), $args );
    $operation->process();
    $errors = $operation->get_data();
    foreach( $errors as $type => $error ) $this->set_note( $type, $error, true );

    // get the form's subject
    $form_name = $this->form_type.'_form';
    $form_id_name = $form_name.'_id';

    $this->set_variable( 'form_name', $form_name );
    $this->set_variable( 'form_id', $this->get_record()->$form_id_name );
  }

  /**
   * The type of form (ie: consent, contact, proxy)
   * @var string $form_type
   * @access private
   */
  private $form_type;
}
?>
