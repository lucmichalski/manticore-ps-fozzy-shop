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

class WkPwaClientNotificationTokenModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        $this->display_header = false;
        $this->display_footer = false;

        $token = Tools::getValue('token');
        $endpoint = Tools::getValue('endpoint');
        $userPublicKey = Tools::getValue('userPublicKey');
        $userAuthToken = Tools::getValue('userAuthToken');
        $action = Tools::getValue('action');

        if (!isset($this->context->cookie->id_guest)) {
            Guest::setNewGuest($this->context->cookie);
        }
        $idGuest = $this->context->cookie->id_guest;
        $idCustomer = $this->context->customer->id;

        $objPushNotiToken = new WkPwaPushNotificationToken();
        $subscriberDetail = $objPushNotiToken->getTokerDetail($token);
        if ($action == 'addToken') {
            if (!$subscriberDetail) {
                $objPwaHelper = new WkPwaHelper();

                $objPushNotiToken->id_guest = $idGuest;
                $objPushNotiToken->id_customer = $idCustomer;
                $objPushNotiToken->ip = Tools::getRemoteAddr();
                $objPushNotiToken->id_lang = $this->context->language->id;
                $objPushNotiToken->id_web_browser = $objPwaHelper->getIdBrowser();
                $objPushNotiToken->token = $token;
                $objPushNotiToken->endpoint = $endpoint;
                $objPushNotiToken->user_public_key = $userPublicKey;
                $objPushNotiToken->user_auth_token = $userAuthToken;
                $objPushNotiToken->active = 1;
                $objPushNotiToken->save();

                $idPushNotiToken = $objPushNotiToken->id;

                if ($idPushNotiToken) {
                    $objPushNotification = new WkPwaPushNotification();
                    $notificationDetail = $objPushNotification->getByIdNotificationType(
                        WkPwaPushNotification::WELCOME_NOTIFICATION
                    );
                    if ($notificationDetail) {
                        $objPushNotification->sendPushNotification($notificationDetail['id'], $idPushNotiToken);
                    }
                }
            } else {
                $objPushNotiToken = new WkPwaPushNotificationToken($subscriberDetail['id']);

                if ($objPushNotiToken->id_lang != $this->context->cookie->id_lang) {
                    $objPushNotiToken->id_lang = $this->context->cookie->id_lang;
                }
                if (!$subscriberDetail['id_customer'] && $idCustomer) {
                    $objPushNotiToken->id_guest = $idGuest;
                    $objPushNotiToken->id_customer = $idCustomer;
                } elseif ($subscriberDetail['id_customer'] && !$idCustomer) {
                    $objPushNotiToken->id_guest = $idGuest;
                    $objPushNotiToken->id_customer = 0;
                }

                $objPushNotiToken->save();
            }
        } elseif ($action == 'deleteToken') {
            if ($subscriberDetail) {
                $objPushNotiToken = new WkPwaPushNotificationToken($subscriberDetail['id']);
                $objPushNotiToken->delete();
            }
        }

        die(Tools::jsonEncode(array('success' => true)));
    }
}
