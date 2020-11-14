<?php
class Ecm_checkoutOverride extends Ecm_checkout
{
    public function __construct()
    {
         parent::__construct();
        $this->items = array(
            "'optin'",
            "'newsletter'",
            "'phone'",
            "'phone_mobile'",
            "'company'",
            "'firstname'",
            "'lastname'",
            "'middlename'",
            "'address1'",
            "'address2'",
            "'postcode'",
            "'city'",
            "'id_state'",
            "'id_country'",
            "'other'",
            "'vat_number'",
            "'dni'",
            "'street'",
            "'house'",
            "'apartment'",
            "'level'",
            "'door'",
            "'intercom'",
            "'elevator'",
            "'concierge'",
            "'zone'",
            "'zone_name'",
            "'lat'",
            "'lng'",
            "'valid_adr'",
            "'is_dm'"
        );
        
        $this->layout = array(
            array('id'  => 'avant', 'name'=> $this->l('Avant')),
            array('id'  => 's', 'name'=> $this->l('Standart')),
            array('id'  => 'w','name'=> $this->l('Warehouse')),
            array('id'  => 'fozzy','name'=> $this->l('Fozzy'))
        );
	}    
}