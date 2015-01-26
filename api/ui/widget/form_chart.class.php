<?php
/**
 * form_chart.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\widget;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * widget form chart
 */
class form_chart extends \cenozo\ui\widget
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
    parent::__construct( 'form', 'chart', $args );
  }

  /**
   * Finish setting the variables in a widget.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access protected
   */
  protected function setup()
  {
    parent::setup();

    $contact_form_class_name = lib::get_class_name( 'database\contact_form' );
    $consent_form_class_name = lib::get_class_name( 'database\consent_form' );
    $proxy_form_class_name = lib::get_class_name( 'database\proxy_form' );
    $session = lib::create( 'business\session' );

    $data = array();

    // get the consent form data
    $zero_mod = lib::create( 'database\modifier' );
    $zero_mod->where( 'invalid', '=', false );
    $zero_mod->where( 'complete', '=', false );
    $zero = $consent_form_class_name::count_for_entries( 0, '=', $zero_mod );

    $one_mod = lib::create( 'database\modifier' );
    $one_mod->where( 'invalid', '=', false );
    $one_mod->where( 'complete', '=', false );
    $one = $consent_form_class_name::count_for_entries( 1, '=', $one_mod );

    $two_mod = lib::create( 'database\modifier' );
    $two_mod->where( 'invalid', '=', false );
    $two_mod->where( 'complete', '=', false );
    $two = $consent_form_class_name::count_for_entries( 2, '>=', $two_mod );

    $data[] = array(
      'type' => 'Consent',
      'zero' => $zero,
      'one' => $one,
      'two' => $two );

    // get the contact form data
    $zero_mod = lib::create( 'database\modifier' );
    $zero_mod->where( 'invalid', '=', false );
    $zero_mod->where( 'complete', '=', false );
    $zero = $contact_form_class_name::count_for_entries( 0, '=', $zero_mod );

    $one_mod = lib::create( 'database\modifier' );
    $one_mod->where( 'invalid', '=', false );
    $one_mod->where( 'complete', '=', false );
    $one = $contact_form_class_name::count_for_entries( 1, '=', $one_mod );

    $two_mod = lib::create( 'database\modifier' );
    $two_mod->where( 'invalid', '=', false );
    $two_mod->where( 'complete', '=', false );
    $two = $contact_form_class_name::count_for_entries( 2, '>=', $two_mod );

    $data[] = array(
      'type' => 'Contact',
      'zero' => $zero,
      'one' => $one,
      'two' => $two );

    // get the proxy form data
    $zero_mod = lib::create( 'database\modifier' );
    $zero_mod->where( 'invalid', '=', false );
    $zero_mod->where( 'complete', '=', false );
    $zero = $proxy_form_class_name::count_for_entries( 0, '=', $zero_mod );

    $one_mod = lib::create( 'database\modifier' );
    $one_mod->where( 'invalid', '=', false );
    $one_mod->where( 'complete', '=', false );
    $one = $proxy_form_class_name::count_for_entries( 1, '=', $one_mod );

    $two_mod = lib::create( 'database\modifier' );
    $two_mod->where( 'invalid', '=', false );
    $two_mod->where( 'complete', '=', false );
    $two = $proxy_form_class_name::count_for_entries( 2, '>=', $two_mod );

    $data[] = array(
      'type' => 'Proxy',
      'zero' => $zero,
      'one' => $one,
      'two' => $two );

    $this->set_variable( 'application_title', $session->get_application()->title );
    $this->set_variable( 'title', 'Data Entry System: Incomplete Form Status' );
    $this->set_variable( 'data', $data );
  }
}
