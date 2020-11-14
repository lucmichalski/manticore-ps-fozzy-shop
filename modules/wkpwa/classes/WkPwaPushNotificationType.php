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

class WkPwaPushNotificationType extends ObjectModel
{
    public $id_notification_type;
    public $name;
    public $active;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'wk_pwa_push_notification_type',
        'primary' => 'id_notification_type',
        'fields' => array(
            'name' => array('type' => self::TYPE_STRING, 'required' => true),
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => true),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => true),
        ),
    );

    public static function isNotificationTypeActive($idNotificationType)
    {
        $objPushNotificationType = new WkPwaPushNotificationType($idNotificationType);
        return (int)$objPushNotificationType->active;
    }
}
