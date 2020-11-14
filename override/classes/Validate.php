<?php
class Validate extends ValidateCore
{
/*
    * module: ecm_checkout
    * date: 2020-11-11 13:58:06
    * version: 0.4.0
    */
    public static function isEmail($email)
    {
        if(!empty($email)){
			$result = preg_match(Tools::cleanNonUnicodeSupport('/^[a-z\p{L}0-9!#$%&\'*+\/=?^`{}|~_-]+[.a-z\p{L}0-9!#$%&\'*+\/=?^`{}|~_-]*@[a-z\p{L}0-9]+(?:[.]?[_a-z\p{L}0-9-])*\.[a-z\p{L}0-9]+$/ui'), $email);
        	if(!$result)
        	$result = self::isPhoneNumber($email);
		}
		return $result;
	}
}
