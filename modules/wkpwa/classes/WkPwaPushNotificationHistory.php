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

class WkPwaPushNotificationHistory extends ObjectModel
{
    public $id_push_notification_history;
    public $id_notification_type;
    public $icon;
    public $title;
    public $body;
    public $target_url;
    public $customer_type;
    public $customer_type_value;
    public $id_element;
    public $remainder_left;
    public $remainder_interval;
    public $last_remainder_date;
    public $delivered_count;
    public $clicked_count;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'wk_pwa_push_notification_history',
        'primary' => 'id_push_notification_history',
        'fields' => array(
            'id_notification_type' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'icon' => array('type' => self::TYPE_STRING),
            'title' => array('type' => self::TYPE_STRING, 'required' => true),
            'body' => array('type' => self::TYPE_STRING, 'required' => true),
            'target_url' => array('type' => self::TYPE_STRING, 'validate' => 'isUrl', 'required' => true),
            'customer_type' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'customer_type_value' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_element' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'remainder_left' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'remainder_interval' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'last_remainder_date' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'delivered_count' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'clicked_count' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => true),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => true),
        ),
    );

    public static function isIconUsed($icon, $idPushNotificationHistory = 0)
    {
        $sql = 'SELECT *
        FROM `'._DB_PREFIX_.'wk_pwa_push_notification_history`
        WHERE `icon`=\''.pSQL($icon).'\'';

        if ($idPushNotificationHistory) {
            $sql .= ' AND `id_push_notification_history` != '.(int)$idPushNotificationHistory;
        }
        return Db::getInstance()->getRow($sql);
    }

    public static function submitPushNotificationHistoryData($idPushNotification, $payload, $idElement = 0)
    {
        $objPushNotification = new WkPwaPushNotification($idPushNotification);
        $objPushNotificationHistory = new WkPwaPushNotificationHistory();
        $objPushNotificationHistory->id_notification_type = $objPushNotification->id_notification_type;

        if ($objPushNotification->id_notification_type == WkPwaPushNotification::NEW_PRODUCT_NOTIFICATION) {
            $prodIconLinkParts = explode('/', $payload['icon']);
            $objPushNotificationHistory->icon = $prodIconLinkParts[(count($prodIconLinkParts) - 1)];
            $objPushNotificationHistory->target_url = $payload['target_url'];
        } else {
            $objPushNotificationHistory->icon = $objPushNotification->icon;
            $objPushNotificationHistory->target_url = $objPushNotification->target_url;
        }

        $objPushNotificationHistory->title = $payload['title'];
        $objPushNotificationHistory->body = $payload['body'];
        $objPushNotificationHistory->customer_type = $objPushNotification->customer_type;
        $objPushNotificationHistory->customer_type_value = $objPushNotification->customer_type_value;
        $objPushNotificationHistory->id_element = $idElement;
        if ($objPushNotification->remainder_count) {
            $remainderLeft = (int)$objPushNotification->remainder_count - 1;
        } else {
            $remainderLeft = 0;
        }
        $objPushNotificationHistory->remainder_left = $remainderLeft;
        $objPushNotificationHistory->remainder_interval = $objPushNotification->remainder_interval;
        $objPushNotificationHistory->last_remainder_date = date('Y-m-d');
        $objPushNotificationHistory->delivered_count = 0;
        $objPushNotificationHistory->clicked_count = 0;
        if ($objPushNotificationHistory->save()) {
            return $objPushNotificationHistory->id;
        }

        return false;
    }

    public function delete()
    {
        if (!$this->deleteNotificationImg($this->id)
            || !parent::delete()) {
            return false;
        }

        return true;
    }

    public function deleteNotificationImg($idPushNotificationHistory)
    {
        if (!$idPushNotificationHistory) {
            return false;
        }

        $objPushNotificationHistory = new WkPwaPushNotificationHistory($idPushNotificationHistory);
        $imgPath = _PS_MODULE_DIR_.'wkpwa/views/img/notificationIcon/'.$objPushNotificationHistory->icon;
        if (file_exists($imgPath) &&
            !WkPwaPushNotification::isIconUsed($objPushNotificationHistory->icon) &&
            !self::isIconUsed($objPushNotificationHistory->icon, $idPushNotificationHistory)) {
            unlink($imgPath); // delete file
        }

        return true;
    }

    public function getCartReminderNoitifcation()
    {
        $sql = 'SELECT ppnh.*
                FROM `'._DB_PREFIX_.'wk_pwa_push_notification_history` AS ppnh
                LEFT JOIN `'._DB_PREFIX_.'orders` AS o ON (o.`id_cart` = ppnh.`id_element`)
                WHERE ppnh.`id_notification_type`='.(int)WkPwaPushNotification::CART_REMINDER_NOTIFICATION.'
                AND ppnh.`remainder_left` > 0 AND CURDATE() > ppnh.`last_remainder_date`
                AND IF(DATEDIFF(CURDATE(), ppnh.`last_remainder_date`) >= ppnh.`remainder_interval`, 1, 0)
                AND IF(IFNULL(o.`id_order`, 0) > 0, 0, 1)';

        return Db::getInstance()->executeS($sql);
    }

    public function getPayloadDataFromHistory($idPushNotificationHistory)
    {
        $objPushNotificationHistory = new WkPwaPushNotificationHistory($idPushNotificationHistory);
        $notificationIcon = WkPwaHelper::getBaseDirUrl().'modules/wkpwa/views/img/notificationIcon/';
        $notificationIcon .= $objPushNotificationHistory->icon;

        $payload = array(
            'title' => $objPushNotificationHistory->title,
            'body' => $objPushNotificationHistory->body,
            'target_url' => $objPushNotificationHistory->target_url,
            'icon' => $notificationIcon,
            'badge' => WkPwaHelper::getBaseDirUrl().'modules/wkpwa/views/img/appIcon/'.WkPwaHelper::_PWA_FAVICON_NAME_.
            '-72x72.png',
        );

        return $payload;
    }

    public function getNotificationHistorySubscribers($idPushNotificationHistory)
    {
        $objPushNotificationHistory = new WkPwaPushNotificationHistory($idPushNotificationHistory);

        if ($objPushNotificationHistory->id_notification_type == WkPwaPushNotification::CART_REMINDER_NOTIFICATION) {
            $sql = 'SELECT DISTINCT  ppnt.*
                FROM `'._DB_PREFIX_.'wk_pwa_push_notification_token` AS ppnt
                INNER JOIN `'._DB_PREFIX_.'cart` AS c ON (
                    IF(c.`id_customer` > 0, (c.`id_customer` = ppnt.`id_customer`), c.`id_guest` = ppnt.`id_guest`)
                )
                WHERE ppnt.`active` = 1
                AND ppnt.`expired` = 0
                AND c.`id_cart` = '.(int)$objPushNotificationHistory->id_element;

            return Db::getInstance()->executeS($sql);
        }
    }
}
