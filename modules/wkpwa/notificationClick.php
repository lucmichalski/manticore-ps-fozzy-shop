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

include_once '../../config/config.inc.php';
include_once(_PS_MODULE_DIR_.'wkpwa/classes/define.php');


$idPushNotificationHistory = Tools::getValue('identifier');
// $idSubscriber = Tools::getValue('targetId');
if ($idPushNotificationHistory) {
    $objPushNotificationHistory = new WkPwaPushNotificationHistory((int)$idPushNotificationHistory);
    $objPushNotificationHistory->clicked_count += 1;
    $objPushNotificationHistory->save();

    // NOTE:: @todo Git Issue #37, this issue will perfectly be resolved by sending
    // the idPushNotificationHistory variable in URL, but for now we will implement the below way because most of the
    // times this issue is handeled automatically but in future below code must be improved
    if ($objPushNotificationHistory->id_notification_type == WkPwaPushNotification::CART_REMINDER_NOTIFICATION) {
        $idCart = (int)$objPushNotificationHistory->id_element;
        if ($idCart) {
            $cart = new Cart($idCart);
            $context = Context::getContext();
            $context->cookie->id_cart = $cart;
            $context->cart = $cart;

            CartRule::autoAddToCart($context);
            $context->cookie->write();
        }
    }
}


header('Content-type: text/plain');
echo('1');
die;
