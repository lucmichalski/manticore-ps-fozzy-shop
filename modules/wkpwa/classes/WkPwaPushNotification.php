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

use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

class WkPwaPushNotification extends ObjectModel
{
    public $id;
    public $id_notification_type;
    public $icon;
    public $title;
    public $body;
    public $target_url;
    public $customer_type;
    public $customer_type_value;
    public $push_schedule;
    public $remainder_count;
    public $remainder_interval;
    public $order_status;
    public $date_add;
    public $date_upd;

    private $webPush;

    const CUSTOMER_TYPE_ALL = 0;
    const CUSTOMER_TYPE_GROUP = 1;
    const CUSTOMER_TYPE_PARTICULAR_CUSTOMER = 2;

    const DEFAULT_DATE_TIME = '0000-00-00';

    const MANUAL_NOTIFICATION = 1;
    const NEW_PRODUCT_NOTIFICATION = 2;
    const ORDER_STATUS_NOTIFICATION = 3;
    const WELCOME_NOTIFICATION = 4;
    const CART_REMINDER_NOTIFICATION = 5;

    public static $definition = array(
        'table' => 'wk_pwa_push_notification',
        'primary' => 'id',
        'fields' => array(
            'id_notification_type' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'icon' => array('type' => self::TYPE_STRING),
            'title' => array('type' => self::TYPE_STRING, 'required' => true),
            'body' => array('type' => self::TYPE_STRING, 'required' => true),
            'target_url' => array('type' => self::TYPE_STRING, 'validate' => 'isUrl'),
            'customer_type' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'customer_type_value' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'push_schedule' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'remainder_count' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'remainder_interval' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'order_status' => array('type' => self::TYPE_STRING),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => true),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => true),
        ),
    );

    public $notificationType = array();
    public $customerTypes = array();

    public function __construct($id = null)
    {
        parent::__construct($id);

        $this->moduleInstance = new WkPwa();
        $this->notificationType = array(
            self::MANUAL_NOTIFICATION => $this->moduleInstance->l('Manual Notification', 'WkPwaPushNotification'),
            self::NEW_PRODUCT_NOTIFICATION => $this->moduleInstance->l('Product Notification', 'WkPwaPushNotification'),
            self::ORDER_STATUS_NOTIFICATION => $this->moduleInstance->l('Order Status Update Notification', 'WkPwaPushNotification'),
            self::WELCOME_NOTIFICATION => $this->moduleInstance->l('Welcome Notification', 'WkPwaPushNotification'),
            self::CART_REMINDER_NOTIFICATION => $this->moduleInstance->l('Cart Reminder Notification', 'WkPwaPushNotification')
        );

        $this->customerTypes = array(
            WkPwaPushNotification::CUSTOMER_TYPE_ALL => $this->moduleInstance->l('All Subscribers', 'WkPwaPushNotification'),
            WkPwaPushNotification::CUSTOMER_TYPE_GROUP => $this->moduleInstance->l('Subscribers Customer Group', 'WkPwaPushNotification'),
            WkPwaPushNotification::CUSTOMER_TYPE_PARTICULAR_CUSTOMER => $this->moduleInstance->l('Particular Customer (subscriber)', 'WkPwaPushNotification')
        );
    }

    public function delete()
    {
        if (!$this->deleteNotificationImg($this->id)
            || !parent::delete()) {
            return false;
        }

        return true;
    }

    public function deleteNotificationImg($idPushNotification)
    {
        if (!$idPushNotification) {
            return false;
        }

        $objPushNotification = new WkPwaPushNotification($idPushNotification);
        $imgPath = _PS_MODULE_DIR_.'wkpwa/views/img/notificationIcon/'.$objPushNotification->icon;
        if (file_exists($imgPath) && !WkPwaPushNotificationHistory::isIconUsed($objPushNotification->icon)) {
            unlink($imgPath); // delete file
        }

        return true;
    }

    public function isIconUsed($icon)
    {
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'wk_pwa_push_notification` WHERE `icon`=\''.pSQL($icon).'\'';
        return Db::getInstance()->getRow($sql);
    }

    public function getNotificationTypes()
    {
        return $this->notificationType;
    }

    public function getNotificationTypesForHistory()
    {
        $notificationType = array();
        $allowedNotificationTypeForRecord = json_decode(Configuration::get('WK_PWA_PUSH_NOTIFICATION_TYPE_RECORD'));
        foreach ($allowedNotificationTypeForRecord as $idNotificationType) {
            $notificationType[$idNotificationType] = $this->notificationType[$idNotificationType];
        }

        return $notificationType;
    }

    public function getCustomerTypes()
    {
        return $this->customerTypes;
    }

    public function getByIdNotificationType($idNotificationType, $customerType = 0, $customerTypeValue = 0)
    {
        $sql = 'SELECT *
                FROM `'._DB_PREFIX_.'wk_pwa_push_notification`
                WHERE `id_notification_type` = '.(int)$idNotificationType.'
                AND `customer_type` = '.(int)$customerType.'
                AND `customer_type_value` = '.(int)$customerTypeValue;
        $notificationDetail =  Db::getInstance()->executeS($sql);

        if (count($notificationDetail) == 1) {
            return reset($notificationDetail);
        }

        return false;
    }

    public function getNotificationDetailsById($idPushNotification)
    {
        $sql = 'SELECT *
                FROM `'._DB_PREFIX_.'wk_pwa_push_notification`
                WHERE `id` = '.(int)$idPushNotification;
        return Db::getInstance()->getRow($sql);
    }

    public function replaceStringByValue(&$string, $varValArr)
    {
        if (!$varValArr) {
            return false;
        }

        foreach ($varValArr as $variable => $value) {
            $string = str_replace($variable, $value, $string);
        }

        return true;
    }

    public function getScheduledPushNotification($date = false)
    {
        if (!$date) {
            $date = date('Y-m-d');
        }

        $sql = 'SELECT *
                FROM `'._DB_PREFIX_.'wk_pwa_push_notification`
                WHERE `push_schedule` = \''.pSQL($date).'\'';
        return Db::getInstance()->executeS($sql);
    }

    public function getAbandonedCart()
    {
        $sql = 'SELECT c.*
                FROM `'._DB_PREFIX_.'cart` AS c
                LEFT JOIN `'._DB_PREFIX_.'orders` AS o ON (o.`id_cart` = c.`id_cart`)
                WHERE (TIME_TO_SEC(
                    TIMEDIFF(\''.pSQL(date('Y-m-d H:i:00', time())).'\', c.`date_add`)
                ) BETWEEN 86400 AND 172800)
                AND IF(IFNULL(o.`id_order`, 0) > 0, 0, 1)
                AND c.`id_cart` NOT IN (
                    SELECT `id_element`
                    FROM `'._DB_PREFIX_.'wk_pwa_push_notification_history`
                    WHERE `id_notification_type` = '.(int) WkPwaPushNotification::CART_REMINDER_NOTIFICATION.')
                AND c.`id_cart` IN (
                    SELECT MAX(`id_cart`) AS `id_cart`
                    FROM `'._DB_PREFIX_.'cart`
                    GROUP BY `id_customer`
                )';

        return Db::getInstance()->executeS($sql);
    }

    public function getPayloadData($idPushNotification, $idElement = 0)
    {
        $objPushNotification = new WkPwaPushNotification($idPushNotification);
        $payload = array();
        $payloadError = array(
            'error' => false,
            'msg' => ''
        );
        if ($objPushNotification->id_notification_type == self::MANUAL_NOTIFICATION
            || $objPushNotification->id_notification_type == self::WELCOME_NOTIFICATION
        ) {
            $notificationIcon = WkPwaHelper::getBaseDirUrl().'modules/wkpwa/views/img/notificationIcon/';
            $notificationIcon .= $objPushNotification->icon;
        } elseif ($objPushNotification->id_notification_type == self::NEW_PRODUCT_NOTIFICATION) {
            $idProduct = $idElement;
            $product = new Product((int)$idProduct, false, Configuration::get('PS_LANG_DEFAULT'));
            if (!Validate::isLoadedObject($product)) {
                $payloadError = array(
                    'error' => true,
                    'msg' => $this->moduleInstance->l("Product did not exist, please check !!", 'WkPwaPushNotification')
                );
            } else {
                $varValArr = array(
                    '{$product_name}' => $product->name,
                    '{$product_price}' => Tools::displayPrice($product->price)
                );
                $this->replaceStringByValue($objPushNotification->title, $varValArr);
                $this->replaceStringByValue($objPushNotification->body, $varValArr);

                // Link to product page
                $objPushNotification->target_url = $product->getLink();

                $iconName = WkPwaHelper::generateRandomString().'.png';
                $iconDir = _PS_MODULE_DIR_.'wkpwa/views/img/notificationIcon/';
                $imgPath = $iconDir.$iconName;

                $cover = Product::getCover($idProduct);
                if ($cover) {
                    $imageObj = new Image($cover['id_image']);
                    $imgSrc = _PS_PROD_IMG_DIR_.$imageObj->getImgPath().'.jpg';
                    ImageManager::resize($imgSrc, $imgPath, '192', '192', 'png');
                } else {
                    $imgSrc = _PS_IMG_DIR_.Configuration::get('PS_LOGO');
                    ImageManager::resize($imgSrc, $imgPath, '192', '192', 'png');
                }
                $notificationIcon = WkPwaHelper::getBaseDirUrl().'modules/wkpwa/views/img/notificationIcon/'.$iconName;
            }
        } elseif ($objPushNotification->id_notification_type == self::ORDER_STATUS_NOTIFICATION) {
            $order = new Order((int)$idElement);
            $orderState = new OrderState($order->current_state, Configuration::get('PS_LANG_DEFAULT'));
            $varValArr = array(
                '{$order_reference}' => $order->reference,
                '{$order_status}' => $orderState->name,
                '{$order_total}' => Tools::displayPrice($order->total_paid_tax_incl, new Currency($order->id_currency))
            );
            $this->replaceStringByValue($objPushNotification->title, $varValArr);
            $this->replaceStringByValue($objPushNotification->body, $varValArr);

            $notificationIcon = WkPwaHelper::getBaseDirUrl().'modules/wkpwa/views/img/notificationIcon/';
            $notificationIcon .= $objPushNotification->icon;
        } elseif ($objPushNotification->id_notification_type == self::CART_REMINDER_NOTIFICATION) {
            $cart = new Cart((int)$idElement);
            $cartProducts = $cart->getProducts(false, false, null, false);
            $cartTotal = $cart->getOrderTotal(false, Cart::BOTH, $cartProducts);
            if ($cartTotal > 0) {
                $cartTotal = Tools::displayPrice(
                    $cartTotal,
                    Currency::getCurrencyInstance((int)$cart->id_currency),
                    false
                );
                $varValArr = array(
                    '{$nb_cart_product}' => count($cartProducts),
                    '{$cart_total}' => $cartTotal
                );
                $this->replaceStringByValue($objPushNotification->title, $varValArr);
                $this->replaceStringByValue($objPushNotification->body, $varValArr);

                $notificationIcon = WkPwaHelper::getBaseDirUrl().'modules/wkpwa/views/img/notificationIcon/';
                $notificationIcon .= $objPushNotification->icon;
            } else {
                $payloadError = array(
                    'error' => true,
                    'msg' => $this->moduleInstance->l("Cart total is 0", 'WkPwaPushNotification')
                );
            }
        }

        if ($payloadError['error']) {
            return $payloadError;
        }

        $payload = array(
            'title' => $objPushNotification->title,
            'body' => $objPushNotification->body,
            'target_url' => $objPushNotification->target_url,
            'icon' => $notificationIcon,
            'badge' => WkPwaHelper::getBaseDirUrl().'modules/wkpwa/views/img/appIcon/'.WkPwaHelper::_PWA_FAVICON_NAME_.
            '-72x72.png',
        );

        return $payload;
    }

    public function sendPushNotification(
        $idPushNotification = 0,
        $idElement = 0,
        $startIndex = 0,
        $dataSelectionLimit = 0,
        $idPushNotificationHistory = 0
    ) {
        ob_clean();
        set_time_limit(0);

        $wkError = array();
        $notificationDeliveredCount = 0;
        $expiredIdSubscribers = array();
        $idPushNotifHistory = 0;

        if (Configuration::get('PS_SSL_ENABLED') && Tools::usingSecureMode()) {
            if (Configuration::get('WK_PWA_PUSH_NOTIFICATION_ENABLE')) {
                // Check Notification type is enabled
                $objPushNotification = new WkPwaPushNotification($idPushNotification);
                if (WkPwaPushNotificationType::isNotificationTypeActive($objPushNotification->id_notification_type)) {
                    require_once WkPwaHelper::getLibraryPath();
                    $payload = $this->getPayloadData($idPushNotification, $idElement);

                    if (!isset($payload['error'])) {
                        $objPushNotificationToken = new WkPwaPushNotificationToken();
                        $subscribers = $objPushNotificationToken->getNotificationSubscribers(
                            $idPushNotification,
                            $idElement,
                            $startIndex,
                            $dataSelectionLimit
                        );

                        if ($subscribers) {
                            $WK_PWA_SERVER_KEY = trim(Configuration::get('WK_PWA_SERVER_KEY'));
                            $WK_PWA_APP_PUBLIC_SERVER_KEY = trim(Configuration::get('WK_PWA_APP_PUBLIC_SERVER_KEY'));
                            $WK_PWA_APP_PRIVATE_SERVER_KEY = trim(Configuration::get('WK_PWA_APP_PRIVATE_SERVER_KEY'));

                            $enterDataInHistory = false;
                            $allowedNotificationTypeForRecord = json_decode(Configuration::get(
                                'WK_PWA_PUSH_NOTIFICATION_TYPE_RECORD'
                            ));
                            if (in_array(
                                $objPushNotification->id_notification_type,
                                $allowedNotificationTypeForRecord
                            )) {
                                $enterDataInHistory = true;
                            } else {
                                if ($objPushNotification->id_notification_type == self::CART_REMINDER_NOTIFICATION &&
                                    $objPushNotification->remainder_count > 0
                                ) {
                                    $enterDataInHistory = true;
                                }
                            }

                            // Save data in notification history table
                            if ($enterDataInHistory) {
                                if (!$idPushNotificationHistory) {
                                    $idPushNotifHistory = WkPwaPushNotificationHistory::submitPushNotificationHistoryData(
                                        $idPushNotification,
                                        $payload,
                                        $idElement
                                    );
                                } else {
                                    $idPushNotifHistory = $idPushNotificationHistory;
                                    $objPushNotificationHistory = new WkPwaPushNotificationHistory($idPushNotifHistory);
                                    $notificationDeliveredCount = $objPushNotificationHistory->delivered_count;
                                }

                                if ($idPushNotifHistory) {
                                    $payload['identifier'] = $idPushNotifHistory;
                                }
                            }

                            if ($WK_PWA_SERVER_KEY && $WK_PWA_APP_PUBLIC_SERVER_KEY && $WK_PWA_APP_PUBLIC_SERVER_KEY) {
                                $response = $this->pushNotification(
                                    $subscribers,
                                    $payload,
                                    $WK_PWA_SERVER_KEY,
                                    $WK_PWA_APP_PUBLIC_SERVER_KEY,
                                    $WK_PWA_APP_PRIVATE_SERVER_KEY
                                );

                                if ($response) {
                                    if (!is_array($response)) {
                                        // All Notifications Delivered
                                        $notificationDeliveredCount += count($subscribers);
                                    } else {
                                        foreach ($subscribers as $key => $subscriber) {
                                            if ($response[$key]['success']) {
                                                // Delivered
                                                $notificationDeliveredCount += 1;
                                            } else {
                                                // Not delivered
                                                $expiredIdSubscribers[] = $subscriber['id'];
                                            }
                                        }
                                    }
                                }

                                if ($idPushNotifHistory) {
                                    Db::getInstance()->update(
                                        'wk_pwa_push_notification_history',
                                        array('delivered_count' => $notificationDeliveredCount),
                                        'id_push_notification_history = '.(int)$idPushNotifHistory
                                    );
                                }

                                if ($expiredIdSubscribers) {
                                    $expiredIdSubscribers = implode(",", $expiredIdSubscribers);
                                    Db::getInstance()->update(
                                        'wk_pwa_push_notification_token',
                                        array('expired' => 1),
                                        'id IN ('.$expiredIdSubscribers.')'
                                    );
                                    $expiredIdSubscribers = explode(",", $expiredIdSubscribers);
                                }
                            }
                        }
                    } else {
                        $wkError[] = $payload['msg'];
                    }
                } else {
                    $wkError[] = $this->notificationType[$objPushNotification->id_notification_type].' '.
                    $this->moduleInstance->l('is not active', 'WkPwaPushNotification');
                }
            } else {
                $wkError[] = $this->moduleInstance->l('Please allow push notification from General configuration for sending notification', 'WkPwaPushNotification');
            }
        } else {
            $wkError[] = $this->moduleInstance->l("Please enable SSL on your shop for sending push notifications", 'WkPwaPushNotification');
        }

        return (array(
            'success' => empty($wkError) ? true : false,
            'message' => $wkError,
            'idPushNotificationHistory' => $idPushNotifHistory,
            'notificationDeliveredCount' => $notificationDeliveredCount,
            'subscriberTokenExpireCount' => count($expiredIdSubscribers),
        ));
    }

    public function sendPushNotificationByHistory($idPushNotificationHistory)
    {
        $wkError = array();
        if (Configuration::get('WK_PWA_PUSH_NOTIFICATION_ENABLE')) {
            // Check Notification type is enabled
            require_once WkPwaHelper::getLibraryPath();

            $objPushNotificationHistory = new WkPwaPushNotificationHistory($idPushNotificationHistory);
            $payload = $objPushNotificationHistory->getPayloadDataFromHistory($idPushNotificationHistory);
            if ($payload) {
                $subscribers = $objPushNotificationHistory->getNotificationHistorySubscribers(
                    $idPushNotificationHistory
                );
                if ($subscribers) {
                    $WK_PWA_SERVER_KEY = trim(Configuration::get('WK_PWA_SERVER_KEY'));
                    $WK_PWA_APP_PUBLIC_SERVER_KEY = trim(Configuration::get('WK_PWA_APP_PUBLIC_SERVER_KEY'));
                    $WK_PWA_APP_PRIVATE_SERVER_KEY = trim(Configuration::get('WK_PWA_APP_PRIVATE_SERVER_KEY'));

                    $payload['identifier'] = $idPushNotificationHistory;

                    if ($WK_PWA_SERVER_KEY && $WK_PWA_APP_PUBLIC_SERVER_KEY && $WK_PWA_APP_PUBLIC_SERVER_KEY) {
                        $notificationDeliveredCount = 0;
                        $expiredIdSubscribers = array();

                        $response = $this->pushNotification(
                            $subscribers,
                            $payload,
                            $WK_PWA_SERVER_KEY,
                            $WK_PWA_APP_PUBLIC_SERVER_KEY,
                            $WK_PWA_APP_PRIVATE_SERVER_KEY
                        );
                        if ($response) {
                            if (!is_array($response)) {
                                // All Notifications Delivered
                                $notificationDeliveredCount += count($subscribers);
                            } else {
                                foreach ($subscribers as $key => $subscriber) {
                                    if ($response[$key]['success']) {
                                        // Delivered
                                        $notificationDeliveredCount += 1;
                                    } else {
                                        // Not delivered
                                        $expiredIdSubscribers[] = $subscriber['id'];
                                    }
                                }
                            }
                        }

                        if ($idPushNotificationHistory) {
                            $objPushNotificationHistory->last_remainder_date = date('Y-m-d');
                            $objPushNotificationHistory->remainder_left -= 1;
                            $objPushNotificationHistory->save();
                        }

                        if ($expiredIdSubscribers) {
                            $expiredIdSubscribers = implode(",", $expiredIdSubscribers);
                            Db::getInstance()->update(
                                'wk_pwa_push_notification_token',
                                array('expired' => 1),
                                'id IN \''.pSQL($expiredIdSubscribers).'\''
                            );
                        }
                    }
                }
            }
        } else {
            $wkError[] = $this->moduleInstance->l('Please allow push notification from General configuration for sending notification', 'WkPwaPushNotification');
        }

        return (array(
            'success' => empty($wkError) ? true : false,
            'message' => $wkError
        ));
    }

    public function pushNotification(
        $subscribers,
        $payload,
        $serverKey,
        $applicationPublicServiceKey,
        $applicationPrivateServiceKey
    ) {

        $this->webPush = new WebPush(array(
            'GCM' => $serverKey,
            'VAPID' => array(
                'subject' => WkPwaHelper::getBaseDirUrl(),
                'publicKey' => $applicationPublicServiceKey,
                'privateKey' => $applicationPrivateServiceKey,
            ),
        ));
        // disable automatic padding in tests to speed these up
        $this->webPush->setAutomaticPadding(false);

        foreach ($subscribers as $subscriber) {
            $payload['targetId'] = $subscriber['id'];

            $this->webPush->sendNotification(
                $subscriber['endpoint'],
                json_encode($payload),
                $subscriber['user_public_key'],
                $subscriber['user_auth_token']
            );
        }

        return $this->webPush->flush(10);
    }

    public function getPushNotificationDefaultVariables()
    {
        $context = Context::getContext();
        $psLogo = _PS_IMG_.Configuration::get('PS_LOGO');
        $defaultVariables = array(
            'customerTypes' => $this->getCustomerTypes(),
            'notificationTypes' => $this->getNotificationTypes(),
            'groups' => Group::getGroups($context->language->id),
            'CUSTOMER_TYPE_GROUP' => WkPwaPushNotification::CUSTOMER_TYPE_GROUP,
            'CUSTOMER_TYPE_PARTICULAR_CUSTOMER' => WkPwaPushNotification::CUSTOMER_TYPE_PARTICULAR_CUSTOMER,
            'psLogo' => $psLogo,
            'defaultSchedulePushTime' => date('Y-m-d', strtotime('+1 day')),
            'DEFAULT_DATE_TIME' => WkPwaPushNotification::DEFAULT_DATE_TIME,
        );

        return $defaultVariables;
    }

    public function getCompleteNotificationDetails($idPushNotification)
    {
        $notificationDetail = $this->getNotificationDetailsById($idPushNotification);
        if ($notificationDetail) {
            if ($notificationDetail['icon']) {
                $imgUrl = _PS_MODULE_DIR_.'wkpwa/views/img/notificationIcon/'.$notificationDetail['icon'];
                if (file_exists($imgUrl)) {
                    $imgUrl = _MODULE_DIR_.'wkpwa/views/img/notificationIcon/'.$notificationDetail['icon'];
                    $notificationDetail['icon'] = $imgUrl;
                } else {
                    $notificationDetail['icon'] = '';
                }
            }

            $notificationDetail['active'] = WkPwaPushNotificationType::isNotificationTypeActive(
                $notificationDetail['id_notification_type']
            );

            if ($notificationDetail['order_status']) {
                $notificationDetail['order_status'] = json_decode($notificationDetail['order_status']);
            }

            if ($notificationDetail['customer_type'] == WkPwaPushNotification::CUSTOMER_TYPE_PARTICULAR_CUSTOMER) {
                $customer = new Customer($notificationDetail['customer_type_value']);
                $notificationDetail['customer_type_value_text'] = $customer->firstname." ".$customer->lastname;
            }
        }

        return $notificationDetail;
    }

    // $fields = array(
    //     'edit' => trim(Tools::getValue('id')) ? 1 : 0,
    //     'active' => Tools::getValue('active'),
    //     'idPushNotification' => trim(Tools::getValue('id')),
    //     'idNotificationType' => WkPwaPushNotification::CART_REMINDER_NOTIFICATION,
    //     'title' => trim(Tools::getValue('title')),
    //     'body' => trim(Tools::getValue('body')),
    //     'targetUrl' => trim(Tools::getValue('target_url')),
    //     'notificationIcon' => $_FILES['icon'],
    //     'remainderCount' => trim(Tools::getValue('remainder_count')),
    //     'remainderInterval' => trim(Tools::getValue('remainder_interval')),
    //     'orderStatus' => Tools::getValue('order_status'),
    //     'customerType' => trim(Tools::getValue('customer_type')),
    //     'customerTypeIdGroupValue' => trim(Tools::getValue('customer_type_idGroup_value')),
    //     'customerTypeIdCustomerValue' => trim(Tools::getValue('customer_type_idCustomer_value')),
    //     'schedulePushSwitch' => trim(Tools::getValue('schedule_push_switch')),
    //     'pushSchedule' => trim(Tools::getValue('push_schedule')),
    // );
    public function procressPushNotificationFields($fields)
    {
        $objPushNotification = new WkPwaPushNotification();
        $fields['customerTypeValue'] = 0;
        if (isset($fields['customerType'])) {
            if ($fields['idNotificationType'] == WkPwaPushNotification::MANUAL_NOTIFICATION) {
                if ($fields['customerType'] == WkPwaPushNotification::CUSTOMER_TYPE_GROUP) {
                    $fields['customerTypeValue'] = $fields['customerTypeIdGroupValue'];
                } elseif ($fields['customerType'] == WkPwaPushNotification::CUSTOMER_TYPE_PARTICULAR_CUSTOMER) {
                    $fields['customerTypeValue'] = $fields['customerTypeIdCustomerValue'];
                }
            }
        } else {
            $fields['customerType'] = WkPwaPushNotification::CUSTOMER_TYPE_ALL;
        }

        $currentDateTime = date('Y-m-d');
        if (!isset($fields['schedulePushSwitch']) ||
            (isset($fields['schedulePushSwitch']) && !$fields['schedulePushSwitch'])
        ) {
            $fields['pushSchedule'] = WkPwaPushNotification::DEFAULT_DATE_TIME;
        }

        if ($fields['idNotificationType'] != WkPwaPushNotification::CART_REMINDER_NOTIFICATION) {
            $fields['remainderCount'] = 0;
            $fields['remainderInterval'] = 0;
        }

        if (!isset($fields['active'])) {
            $fields['active'] = 1;
        }

        if ($fields['idNotificationType'] == WkPwaPushNotification::NEW_PRODUCT_NOTIFICATION) {
            $fields['targetUrl'] = null;
        }

        $wkError = array();
        // Validation here
        if (!$fields['title']) {
            $wkError[] = $this->moduleInstance->l('Notification title is required.', 'WkPwaPushNotification');
        } elseif ($fields['title'] && !Validate::isCleanHtml($fields['title'])) {
            $wkError[] = $this->moduleInstance->l('Please enter a valid Title', 'WkPwaPushNotification');
        }

        if (!$fields['body']) {
            $wkError[] = $this->moduleInstance->l('Notification body is required.', 'WkPwaPushNotification');
        } elseif ($fields['body'] && !Validate::isCleanHtml($fields['body'])) {
            $wkError[] = $this->moduleInstance->l('Please enter a valid Notification Content', 'WkPwaPushNotification');
        }

        if ($fields['idNotificationType'] != WkPwaPushNotification::NEW_PRODUCT_NOTIFICATION) {
            if (!$fields['targetUrl']) {
                $wkError[] = $this->moduleInstance->l('Notification target URL is required.', 'WkPwaPushNotification');
            } else {
                if (!Validate::isUrl($fields['targetUrl']) || !Validate::isCleanHtml($fields['targetUrl'])) {
                    $wkError[] = $this->moduleInstance->l('Please enter a valid Target URL', 'WkPwaPushNotification');
                }
            }
        }

        if (!$fields['edit'] && ($fields['idNotificationType'] != WkPwaPushNotification::MANUAL_NOTIFICATION)) {
            $notificationDetail = $objPushNotification->getByIdNotificationType($fields['idNotificationType']);
            if ($notificationDetail) {
                $wkError[] = $this->moduleInstance->l('This notification type is already created.', 'WkPwaPushNotification');
            }
        }

        if (isset($fields['orderStatus'])) {
            if (!$fields['orderStatus'] &&
                $fields['idNotificationType'] == WkPwaPushNotification::ORDER_STATUS_NOTIFICATION
            ) {
                $wkError[] = $this->moduleInstance->l('Please select atleast one order status for sending push notification on order status update.', 'WkPwaPushNotification');
            }
        } else {
            $fields['orderStatus'] = array();
        }

        if ($fields['idNotificationType'] == WkPwaPushNotification::MANUAL_NOTIFICATION) {
            if (!Validate::isUnsignedInt($fields['customerType'])) {
                $wkError[] = $this->moduleInstance->l('Something went wrong, please try again!!', 'WkPwaPushNotification');
            } else {
                if ($fields['customerType'] > 0) {
                    if (!$fields['customerTypeValue']) {
                        $wkError[] = $this->moduleInstance->l('Customer type value is required', 'WkPwaPushNotification');
                    } elseif (!Validate::isUnsignedInt($fields['customerTypeValue'])) {
                        $wkError[] = $this->moduleInstance->l('Something went wrong in setting value of selected customer type, please try again!!', 'WkPwaPushNotification');
                    }
                }
            }
        }

        if ((isset($fields['schedulePushSwitch']) && $fields['schedulePushSwitch']) &&
            $fields['idNotificationType'] == WkPwaPushNotification::MANUAL_NOTIFICATION
        ) {
            if (!$fields['pushSchedule']) {
                $wkError[] = $this->moduleInstance->l('Please enter push notification schedule time', 'WkPwaPushNotification');
            } else {
                if (!Validate::isDate($fields['pushSchedule'])) {
                    $wkError[] = $this->moduleInstance->l('Invalid date entered for push notification schedule time', 'WkPwaPushNotification');
                } elseif ($currentDateTime >= $fields['pushSchedule']) {
                    $wkError[] = $this->moduleInstance->l('Push notification must be scheduled after current date', 'WkPwaPushNotification');
                }
            }
        }

        if ($fields['idNotificationType'] == WkPwaPushNotification::CART_REMINDER_NOTIFICATION) {
            if (!$fields['remainderCount']) {
                $wkError[] = $this->moduleInstance->l('Cart reminder count is required field.', 'WkPwaPushNotification');
            } elseif (!Validate::isUnsignedInt($fields['remainderCount'])) {
                $wkError[] = $this->moduleInstance->l('Invalid value entered in cart reminder count field.', 'WkPwaPushNotification');
            }

            if (!$fields['remainderInterval']) {
                $wkError[] = $this->moduleInstance->l('Cart reminder interval is required field.', 'WkPwaPushNotification');
            } elseif (!Validate::isUnsignedInt($fields['remainderInterval'])) {
                $wkError[] = $this->moduleInstance->l('Invalid value entered of cart reminder interval field.', 'WkPwaPushNotification');
            }
        }

        if (isset($fields['notificationIcon']) && $fields['notificationIcon']['size']) {
            $invalidImg = ImageManager::validateUpload(
                $fields['notificationIcon'],
                Tools::getMaxUploadSize(Configuration::get('PS_LIMIT_UPLOAD_IMAGE_VALUE') * 1048576)
            );
            if ($invalidImg) {
                $wkError[] = $invalidImg;
            }
        }

        $idPushNotification = 0;
        if (!count($wkError)) {
            if ($fields['edit']) {
                $objPushNotification = new WkPwaPushNotification($fields['idPushNotification']);
            } else {
                $objPushNotification = new WkPwaPushNotification();
            }

            $objPushNotification->id_notification_type = $fields['idNotificationType'];
            $objPushNotification->title = $fields['title'];
            $objPushNotification->body = $fields['body'];
            $objPushNotification->target_url = $fields['targetUrl'];
            $objPushNotification->customer_type = $fields['customerType'];
            $objPushNotification->customer_type_value = $fields['customerTypeValue'];
            $objPushNotification->push_schedule = $fields['pushSchedule'];
            $objPushNotification->remainder_count = $fields['remainderCount'];
            $objPushNotification->remainder_interval = $fields['remainderInterval'];
            $objPushNotification->order_status = $fields['orderStatus'] ? json_encode($fields['orderStatus']) : null;
            $objPushNotification->save();
            $idPushNotification = $objPushNotification->id;

            $objPushNotificationType = new WkPwaPushNotificationType($fields['idNotificationType']);
            $objPushNotificationType->active = $fields['active'];
            $objPushNotificationType->save();

            if (isset($fields['notificationIcon'])) {
                $iconName = '';
                if ($fields['notificationIcon']['size'] || (!$fields['notificationIcon']['size'] && !$fields['edit'])) {
                    // $imgExt = pathinfo($fields['notificationIcon']['name'], PATHINFO_EXTENSION);
                    $iconName = WkPwaHelper::generateRandomString().'.png';
                    $iconDir = _PS_MODULE_DIR_.'wkpwa/views/img/notificationIcon/';
                    $imgPath = $iconDir.$iconName;
                }

                if (!$fields['notificationIcon']['size']) {
                    if (!$fields['edit']) {
                        $imgSrc = _PS_IMG_DIR_.Configuration::get('PS_LOGO');
                        ImageManager::resize($imgSrc, $imgPath, '192', '192', 'png');
                    }
                } else {
                    if ($objPushNotification->icon && $idPushNotification) {
                        if (file_exists($iconDir.$objPushNotification->icon)) {
                            if (!WkPwaPushNotificationHistory::isIconUsed($objPushNotification->icon)) {
                                unlink($iconDir.$objPushNotification->icon); // delete file
                            }
                        }
                    }

                    ImageManager::resize($fields['notificationIcon']['tmp_name'], $imgPath, '192', '192', 'png');
                }


                if ($iconName) {
                    $objPushNotification->icon = $iconName;
                    $objPushNotification->save();
                }
            }
        }

        return (array(
            'idPushNotification' => $fields['edit'] ? $fields['idPushNotification'] : $idPushNotification,
            'errors' => $wkError
        ));
    }
}
