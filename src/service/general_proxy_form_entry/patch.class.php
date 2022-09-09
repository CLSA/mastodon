<?php
/**
 * patch.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 */

namespace mastodon\service\general_proxy_form_entry;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * Special service for handling the patch meta-resource
 */
class patch extends \cenozo\service\patch
{
  /**
   * Override parent method
   */
  protected function validate()
  {
    parent::validate();

    $patch_array = $this->get_file_as_array();

    // only allow non-international postcodes in "A1A 1A1" format, zip codes in "01234" format
    $postcode_re = '/^(([A-Z][0-9][A-Z] [0-9][A-Z][0-9])|([0-9]{5}))$/';
    if( array_key_exists( 'proxy_postcode', $patch_array ) )
    {
      $db_general_proxy_form_entry = $this->get_leaf_record();
      if( !$db_general_proxy_form_entry->proxy_address_international &&
          !preg_match( $postcode_re, $patch_array['proxy_postcode'] ) )
      {
        $this->set_data( 'invalid format' );
        $this->status->set_code( 400 );
      }
    }

    if( array_key_exists( 'informant_postcode', $patch_array ) )
    {
      $db_general_proxy_form_entry = $this->get_leaf_record();
      if( !$db_general_proxy_form_entry->informant_address_international &&
          !preg_match( $postcode_re, $patch_array['informant_postcode'] ) )
      {
        $this->set_data( 'invalid format' );
        $this->status->set_code( 400 );
      }
    }

    // only allow non-international phone numbers in "XXX-XXX-XXXX" format
    if( array_key_exists( 'proxy_phone', $patch_array ) )
    {
      $db_general_proxy_form_entry = $this->get_leaf_record();
      if( !$db_general_proxy_form_entry->proxy_phone_international &&
          !util::validate_north_american_phone_number( $patch_array['proxy_phone'] ) )
      {
        $this->set_data( 'invalid format' );
        $this->status->set_code( 400 );
      }
    }

    if( array_key_exists( 'informant_phone', $patch_array ) )
    {
      $db_general_proxy_form_entry = $this->get_leaf_record();
      if( !$db_general_proxy_form_entry->informant_phone_international &&
          !util::validate_north_american_phone_number( $patch_array['informant_phone'] ) )
      {
        $this->set_data( 'invalid format' );
        $this->status->set_code( 400 );
      }
    }
  }
}
