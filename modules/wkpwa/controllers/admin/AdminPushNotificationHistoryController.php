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

class AdminPushNotificationHistoryController extends ModuleAdminController
{
    public function __construct()
    {
        $this->context = Context::getContext();
        $this->bootstrap = true;

        $this->table = 'wk_pwa_push_notification_history';
        $this->className = 'WkPwaPushNotificationHistory';
        $this->identifier = 'id_push_notification_history';

        $notificationTarget = Configuration::get('WK_PWA_PUSH_NOTIFICATION_IMPRESSION_GOAL');

        if (is_array(json_decode(Configuration::get('WK_PWA_PUSH_NOTIFICATION_TYPE_RECORD')))) {
            $allowedNotificationTypeForRecord = implode(
                ',',
                json_decode(Configuration::get('WK_PWA_PUSH_NOTIFICATION_TYPE_RECORD'))
            );
        } else {
            $allowedNotificationTypeForRecord = json_decode(Configuration::get('WK_PWA_PUSH_NOTIFICATION_TYPE_RECORD'));
        }

        $this->_select .= 'IF(
            round((a.`clicked_count`/a.`delivered_count` * 100 ),2) > '.$notificationTarget.',
            1,
            0
        ) badge_success,
        IF(round((a.`clicked_count`/a.`delivered_count` * 100 ),2) > '.$notificationTarget.', 0, 1 ) badge_danger';
        $this->_where .= ' AND a.`id_notification_type` IN ('.$allowedNotificationTypeForRecord.')';
        $this->list_no_link = 1;

        parent::__construct();

        $objPushNotification = new WkPwaPushNotification();
        $notificationType = $objPushNotification->getNotificationTypesForHistory();
        $customerTypes = $objPushNotification->getCustomerTypes();

        $this->fields_list = array(
            'id_push_notification_history' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ),
            'icon' => array(
                'title' => $this->l('Icon') ,
                'align' => 'center',
                'search' => false,
                'callback' => 'getNotificationIcon',
            ),
            'title' => array(
                'title' => $this->l('Title') ,
            ),
            'body' => array(
                'title' => $this->l('Body') ,
            ),
            'target_url' => array(
                'title' => $this->l('Target URL') ,
            ),
            'id_notification_type' => array(
                'title' => $this->l('Notification Type'),
                'type' => 'select',
                'align' => 'center',
                'list' => $notificationType,
                'class' => 'fixed-width-sm',
                'filter_key' => 'a!id_notification_type',
                'filter_type' => 'int',
                'havingFilter' => true,
                'callback' => 'getNotificationType',
            ),
            'customer_type' => array(
                'title' => $this->l('Target Customer Type'),
                'type' => 'select',
                'align' => 'center',
                'list' => $customerTypes,
                'class' => 'fixed-width-sm',
                'filter_key' => 'a!customer_type',
                'filter_type' => 'int',
                'havingFilter' => true,
                'callback' => 'getCustomerType',
            ),
            'customer_type_value' => array(
                'title' => $this->l('Customer Type value') ,
                'align' => 'center',
                'search' => false,
                'class' => 'fixed-width-xs',
                'callback' => 'getCustomerTypeValue',
            ),
            'delivered_count' => array(
                'title' => $this->l('Delivered'),
                'align' => 'center',
            ),
            'clicked_count' => array(
                'title' => $this->l('Clicked'),
                'align' => 'center',
                'filter_key' => 'a!clicked_count',
                'havingFilter' => true,
                'badge_success' => true,
                'badge_danger' => true,
                'callback' => 'getNotificationClickCount',
            ),
            'date_add' => array(
                'title' => $this->l('Date Add'),
                'align' => 'center',
                'type' => 'datetime',
                'filter_key' => 'a!date_add'
            ),
            'date_upd' => array(
                'title' => $this->l('Date Update'),
                'align' => 'center',
                'type' => 'datetime',
                'filter_key' => 'a!date_upd'
            ),
        );
        $this->addRowAction('delete');
        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'icon' => 'icon-trash',
                'confirm' => $this->l('Delete selected items?'),
            ),
        );
    }

    public function getCustomerType($idCustomerType)
    {
        $objPushNotification = new WkPwaPushNotification();
        $customerTypes = $objPushNotification->getCustomerTypes();
        return $customerTypes[$idCustomerType];
    }

    public function getCustomerTypeValue($idCustomerTypeValue, $row)
    {
        if ($row['customer_type'] == WkPwaPushNotification::CUSTOMER_TYPE_ALL) {
            return '-';
        } elseif ($row['customer_type'] == WkPwaPushNotification::CUSTOMER_TYPE_GROUP) {
            $group = new Group($idCustomerTypeValue, Configuration::get('PS_LANG_DEFAULT'));
            return $group->name;
        } elseif ($row['customer_type'] == WkPwaPushNotification::CUSTOMER_TYPE_PARTICULAR_CUSTOMER) {
            $customer = new Customer($idCustomerTypeValue);
            return $customer->firstname.' '.$customer->lastname;
        }
    }

    public function getNotificationClickCount($clickedCount, $row)
    {
        if ($row['delivered_count']) {
            $clickRatio = Tools::ps_round(($row['clicked_count']/$row['delivered_count']) * 100, 2)."%";
            return $clickedCount." (".$clickRatio.")";
        }
        return '0 (0%)';
    }

    public function getNotificationIcon($icon)
    {
        $imgUrl = _MODULE_DIR_.$this->module->name.'/views/img/notificationIcon/'.$icon;
        $image = "<img src='".$imgUrl."' style='max-width: 55px;' class='img-thumbnail'>";
        return $image;
    }

    public function getNotificationType($idNotificationType)
    {
        $objPushNotification = new WkPwaPushNotification();
        $notificationType = $objPushNotification->getNotificationTypes();
        return $notificationType[$idNotificationType];
    }

    public function initToolbar()
    {
        parent::initToolbar();

        $this->toolbar_title = $this->l('Notification History');
        unset($this->toolbar_btn['new']);
    }
}
