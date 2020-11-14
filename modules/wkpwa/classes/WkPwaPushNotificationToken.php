<?php
/**
* 2010-2020 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2020 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class WkPwaPushNotificationToken extends ObjectModel
{
    public $id;
    public $id_guest;
    public $id_customer;
    public $ip;
    public $id_lang;
    public $id_web_browser;
    public $token;
    public $endpoint;
    public $user_public_key;
    public $user_auth_token;
    public $active;
    public $expired;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'wk_pwa_push_notification_token',
        'primary' => 'id',
        'fields' => array(
            'id_guest' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_customer' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'ip' => array('type' => self::TYPE_STRING),
            'id_lang' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_web_browser' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'token' => array('type' => self::TYPE_STRING),
            'endpoint' => array('type' => self::TYPE_STRING),
            'user_public_key' => array('type' => self::TYPE_STRING),
            'user_auth_token' => array('type' => self::TYPE_STRING),
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'expired' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        ),
    );

    // NOTE:: Don't forget one customer can subscribe from multiple browsers and may have multiple rows(tokens) in DB
    public function getNotificationSubscribers(
        $idPushNotification,
        $idElement = 0,
        $startIndex = 0,
        $dataSelectionLimit = 0
    ) {
        $objPushNotification = new WkPwaPushNotification((int)$idPushNotification);

        if ($objPushNotification->id_notification_type == WkPwaPushNotification::ORDER_STATUS_NOTIFICATION) {
            $sql = 'SELECT ppnt.*
                    FROM `'._DB_PREFIX_.'wk_pwa_push_notification_token` AS ppnt
                    INNER JOIN `'._DB_PREFIX_.'orders` AS o ON (o.`id_customer` = ppnt.`id_customer`)
                    WHERE ppnt.`active` = 1 AND ppnt.`expired` = 0 AND o.`id_order` = '.(int)$idElement;
        } elseif ($objPushNotification->id_notification_type == WkPwaPushNotification::WELCOME_NOTIFICATION) {
            $sql = 'SELECT ppnt.*
                    FROM `'._DB_PREFIX_.'wk_pwa_push_notification_token` AS ppnt
                    WHERE ppnt.`active` = 1 AND ppnt.`expired` = 0 AND ppnt.`id` = '.(int)$idElement;
        } elseif ($objPushNotification->id_notification_type == WkPwaPushNotification::CART_REMINDER_NOTIFICATION) {
            $sql = 'SELECT DISTINCT  ppnt.*
                    FROM `'._DB_PREFIX_.'wk_pwa_push_notification_token` AS ppnt
                    INNER JOIN `'._DB_PREFIX_.'cart` AS c ON (
                        IF(c.`id_customer` > 0, (c.`id_customer` = ppnt.`id_customer`), c.`id_guest` = ppnt.`id_guest`)
                    )
                    WHERE ppnt.`active` = 1 AND ppnt.`expired` = 0 AND c.`id_cart` = '.(int)$idElement;
        } else {
            if ($objPushNotification->customer_type == WkPwaPushNotification::CUSTOMER_TYPE_GROUP) {
                if ($objPushNotification->customer_type_value == 1) {
                    $sql = 'SELECT ppnt.*
                        FROM `'._DB_PREFIX_.'wk_pwa_push_notification_token` AS ppnt
                        INNER JOIN `'._DB_PREFIX_.'guest` AS g ON (g.`id_guest` = ppnt.`id_guest`)
                        WHERE ppnt.`active` = 1 AND ppnt.`expired` = 0 AND g.`id_customer` = 0';
                } else {
                    $sql = 'SELECT ppnt.*
                        FROM `'._DB_PREFIX_.'wk_pwa_push_notification_token` AS ppnt
                        INNER JOIN `'._DB_PREFIX_.'customer` AS c ON (c.`id_customer` = ppnt.`id_customer`)
                        WHERE ppnt.`active` = 1 AND ppnt.`expired` = 0 AND c.`id_default_group` = '.
                        (int)$objPushNotification->customer_type_value;
                }
            } elseif ($objPushNotification->customer_type == WkPwaPushNotification::CUSTOMER_TYPE_PARTICULAR_CUSTOMER) {
                $sql = 'SELECT ppnt.*
                        FROM `'._DB_PREFIX_.'wk_pwa_push_notification_token` AS ppnt
                        WHERE ppnt.`active` = 1 AND ppnt.`expired` = 0 AND ppnt.`id_customer` = '.
                        (int)$objPushNotification->customer_type_value;
            } else {
                $sql = 'SELECT *
                        FROM `'._DB_PREFIX_.'wk_pwa_push_notification_token`
                        WHERE `active` = 1 AND `expired` = 0';
            }
        }

        if ($startIndex || $dataSelectionLimit) {
            $sql .= ' LIMIT '.$startIndex.','.$dataSelectionLimit;
        }

        return Db::getInstance()->executeS($sql);
    }

    public function getTokerDetail($token)
    {
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'wk_pwa_push_notification_token` WHERE `token`=\''.pSQL($token).'\'';
        $tokensDetail = Db::getInstance()->getRow($sql);
        if ($tokensDetail) {
            return $tokensDetail;
        }
        return false;
    }

    public function getTokenDetailByIdGuest($idGuest)
    {
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'wk_pwa_push_notification_token` WHERE `id_guest`='.(int)$idGuest;
        return Db::getInstance()->getRow($sql);
    }

    public static function searchSubscriberByName($queryTxt)
    {
        $sql = 'SELECT DISTINCT c.*
                FROM `'._DB_PREFIX_.'wk_pwa_push_notification_token` AS ppnt
                INNER JOIN `'._DB_PREFIX_.'customer` AS c ON (c.`id_customer` = ppnt.`id_customer`)
                WHERE (
                    c.`email` LIKE "%'.pSQL($queryTxt).'%"
                    OR c.`firstname` LIKE "%'.pSQL($queryTxt).'%"
                    OR c.`lastname` LIKE "%'.pSQL($queryTxt).'%"
                    OR CONCAT(c.`firstname`, " ", c.`lastname`) LIKE "%'.pSQL($queryTxt).'%"
                )';
        return Db::getInstance()->executeS($sql);
    }

    public function deleteCustomerData($idCustomer)
    {
        if ($this->getCustomerData($idCustomer)) {
            $sql = 'DELETE FROM `'._DB_PREFIX_.'wk_pwa_push_notification_token`
                    WHERE `id_customer` = '.(int)$idCustomer;
            return Db::getInstance()->execute($sql);
        }

        return true;
    }

    public function getCustomerData($idCustomer)
    {
        $sql = 'SELECT `id`, `ip`, `id_lang`, `token`, `endpoint`, `user_public_key`, `user_auth_token`,
                `active`, `date_add`, `date_upd`
                FROM `'._DB_PREFIX_.'wk_pwa_push_notification_token`
                WHERE `id_customer` = '.(int)$idCustomer;
        return Db::getInstance()->executeS($sql);
    }
}
