<?php
class Customer extends CustomerCore
{
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
