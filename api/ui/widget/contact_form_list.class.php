<?php
/**
 * contact_form_list.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\widget;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * widget contact_form list
 */
class contact_form_list extends base_form_list
{
  /**
   * Constructor
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param array $args An associative array of arguments to be processed by the widget
   * @access public
   */
  public function __construct( $args )
  {
    parent::__construct( 'contact', $args );
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
    // restrict by cohort, if necessary
    $restrict_cohort = $this->get_argument( 'restrict_cohort', 'any' );
    if( 'any' != $restrict_cohort )
    {
      $sub_mod = lib::create( 'database\modifier' );
      $sub_mod->where( 'contact_form_id', '=', 'contact_form.id', false );
      $sub_mod->where( 'cohort', '=', $restrict_cohort );
      $min_contact_form_entry_sql = sprintf(
        '( SELECT MIN( id ) FROM contact_form_entry %s )',
        $sub_mod->get_sql() );
      if( is_null( $modifier ) ) $modifier = lib::create( 'database\modifier' );
      $modifier->where( 'contact_form_entry.id', '=', $min_contact_form_entry_sql, false );
    }

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
    // restrict by cohort, if necessary
    $restrict_cohort = $this->get_argument( 'restrict_cohort', 'any' );
    if( 'any' != $restrict_cohort )
    {
      $sub_mod = lib::create( 'database\modifier' );
      $sub_mod->where( 'contact_form_id', '=', 'contact_form.id', false );
      $sub_mod->where( 'cohort', '=', $restrict_cohort );
      $min_contact_form_entry_sql = sprintf(
        '( SELECT MIN( id ) FROM contact_form_entry %s )',
        $sub_mod->get_sql() );
      if( is_null( $modifier ) ) $modifier = lib::create( 'database\modifier' );
      $modifier->where( 'contact_form_entry.id', '=', $min_contact_form_entry_sql, false );
    }

    return parent::determine_record_list( $modifier );
  }
}
?>
