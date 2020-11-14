<?php
class Customer extends CustomerCore
{
    
   public static function getCustomersFull($only_active = null)
    {
        $sql = 'SELECT *
				FROM `'._DB_PREFIX_.'customer`
				WHERE 1 '.Shop::addSqlRestriction(Shop::SHARE_CUSTOMER).
				($only_active ? ' AND `active` = 1' : '').'
				ORDER BY `id_customer` ASC';
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    } 
    
    /*
    * module: ecm_checkout
    * date: 2020-11-11 13:58:07
    * version: 0.4.0
    */
    public function getByEmail($email, $plaintextPassword = null, $ignoreGuest = true)
    {
        if (Validate::isPhoneNumber($email)) {
            $email = Db::getInstance()->getValue("SELECT `email` FROM `"._DB_PREFIX_."customer` WHERE `phone` = '{$email}'");
		}	
		if(!$email) {
			return false;
			die(Tools::displayError('Email by phone not found!'));
		}
		Parent::getByEmail($email, $plaintextPassword, $ignoreGuest);
		return $this;
	}
}
