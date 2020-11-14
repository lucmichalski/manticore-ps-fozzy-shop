<?php
class Validate extends ValidateCore
{
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
