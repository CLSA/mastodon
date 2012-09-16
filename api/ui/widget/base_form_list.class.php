<?php
/**
 * base_form_list.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\widget;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * Base class for all form lists
 */
abstract class base_form_list extends \cenozo\ui\widget\base_list
{
  /**
   * Constructor
   * 
   * Defines all variables required by the form list.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param string $form_type The type of form being listed.
   * @param array $args An associative array of arguments to be processed by the widget
   * @access public
   */
  public function __construct( $form_type, $args )
  {
    parent::__construct( $form_type.'_form', $args );
    $this->form_type = $form_type;
  }

  /**
   * Processes arguments, preparing them for the operation.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @throws exception\notice
   * @access protected
   */
  protected function prepare()
  {
    parent::prepare();

    $this->add_column( 'id', 'number', 'ID', true );
    $this->add_column( 'date', 'date', 'Date Added', true );
    $this->add_column( 'typist_1', 'string', 'Typist 1', false );
    $this->add_column( 'typist_1_submitted', 'boolean', 'Submitted', false );
    $this->add_column( 'typist_2', 'string', 'Typist 2', false );
    $this->add_column( 'typist_2_submitted', 'boolean', 'Submitted', false );
    $this->add_column( 'conflict', 'boolean', 'Conflict', false );
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
    
    $restrict_cohort = $this->get_argument( 'restrict_cohort', 'any' );
    $this->set_variable( 'restrict_cohort', $restrict_cohort );

    $form_entry_list_method = sprintf( 'get_%s_entry_list', $this->get_subject() );
    $session = lib::create( 'business\session' );
    $db_user = $session->get_user();
    $db_role = $session->get_role();

    foreach( $this->get_record_list() as $record )
    {
      // determine who has worked on the form
      $typist_1 = 'n/a';
      $typist_1_submitted = false;
      $typist_2 = 'n/a';
      $typist_2_submitted = false;

      $form_entry_list = $record->$form_entry_list_method();
      $db_form_entry = current( $form_entry_list );
      if( $db_form_entry )
      {
        $typist_1 = $db_form_entry->get_user()->name;
        $typist_1_submitted = !$db_form_entry->deferred;
      }
      $db_form_entry = next( $form_entry_list );
      if( $db_form_entry )
      {
        $typist_2 = $db_form_entry->get_user()->name;
        $typist_2_submitted = !$db_form_entry->deferred;
      }

      // if both typists have submitted and this form is still in the list then there is a conflict
      $conflict = $typist_1_submitted && $typist_2_submitted;

      $this->add_row( $record->id,
        array( 'id' => $record->id,
               'date' => $record->date,
               'typist_1' => $typist_1,
               'typist_1_submitted' => $typist_1_submitted,
               'typist_2' => $typist_2,
               'typist_2_submitted' => $typist_2_submitted,
               'conflict' => $conflict ) );
    }
  }

  /**
   * Overrides the parent class method to restrict form list
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param database\modifier $modifier Modifications to the list.
   * @return int
   * @access protected
   */
  public function determine_record_count( $modifier = NULL )
  {
    $form_entry_list_class_name = lib::get_class_name( 'database\\'.$this->get_subject() );
    if( is_null( $modifier ) ) $modifier = lib::create( 'database\modifier' );
    $modifier->where( 'invalid', '=', false );
    $modifier->where( 'complete', '=', false );

    return parent::determine_record_count( $modifier );
  }
  
  /**
   * Overrides the parent class method to restrict the list
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param database\modifier $modifier Modifications to the list.
   * @return array( record )
   * @access protected
   */
  public function determine_record_list( $modifier = NULL )
  {
    $form_entry_list_class_name = lib::get_class_name( 'database\\'.$this->get_subject() );
    if( is_null( $modifier ) ) $modifier = lib::create( 'database\modifier' );
    $modifier->where( 'invalid', '=', false );
    $modifier->where( 'complete', '=', false );

    return parent::determine_record_list( $modifier );
  }

  /**
   * The type of form (ie: consent, contact, proxy)
   * @var string $form_type
   * @access private
   */
  private $form_type;
}
?>
