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

class AdminPushNotificationSubscriberController extends ModuleAdminController
{
    public function __construct()
    {
        $this->context = Context::getContext();
        $this->bootstrap = true;

        $this->table = 'wk_pwa_push_notification_token';
        $this->className = 'WkPwaPushNotificationToken';
        $this->identifier = 'id';
        parent::__construct();

        $this->_select .= 'l.`name` AS language_name,
        wb.`name` AS browser_name,
        IF(a.`id_customer` > 0, CONCAT(c.`firstname`, " ", c.`lastname`), \''.$this->l('Visitor').'\') AS customer_name,
        c.email AS customer_email,
        a.`id_customer` AS order_customer,
        a.`active` AS subscribed,
        IF(a.`expired`, 1, 0) badge_danger,
        IF(a.`expired`, 0, 1) badge_success,
        IF(a.`expired`, \''.$this->l('Expired').'\', \''.$this->l('Working').'\') AS expired';

        $this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = a.`id_customer`)';
        $this->_join .= ' INNER JOIN `'._DB_PREFIX_.'lang` l ON (l.`id_lang` = a.`id_lang`)';
        $this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'web_browser` wb ON (wb.`id_web_browser` = a.`id_web_browser`)';
        $this->list_no_link = 1;

        $this->subscribeStatus = array(
            0 => $this->l('Unsubscribe'),
            1 => $this->l('Subscribe'),
        );

        $this->expiryStatus = array(
            0 => $this->l('Working'),
            1 => $this->l('Expired'),
        );

        $this->fields_list = array(
            'id' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ),
            'customer_name' => array(
                'title' => $this->l('Customer'),
                'align' => 'center',
                'havingFilter' => true,
                'callback' => 'getCustomerInfo',
            ),
            'ip' => array(
                'title' => $this->l('IP Address'),
                'class' => 'fixed-width-sm',
            ),
            'language_name' => array(
                'title' => $this->l('Subscriber Language'),
                'align' => 'center',
                'havingFilter' => true,
            ),
            'browser_name' => array(
                'title' => $this->l('Browser'),
                'align' => 'center',
                'type' => 'text',
                'havingFilter' => true,
                'class' => 'fixed-width-sm',
            ),
            'order_customer' => array(
                'title' => $this->l('Total Paid'),
                'align' => 'center',
                'type' => 'price',
                'currency' => true,
                'search' => false,
                'class' => 'fixed-width-sm',
                'havingFilter' => true,
                'callback' => 'getCustomerTotalPaid'
            ),
            'expired' => array(
                'title' => $this->l('Token Expired'),
                'type' => 'select',
                'align' => 'center',
                'list' => $this->expiryStatus,
                'badge_danger' => true,
                'badge_success' => true,
                'filter_key' => 'a!expired',
                'filter_type' => 'int',
                'order_key' => 'expired',
            ),
            'date_add' => array(
                'title' => $this->l('Date Add'),
                'align' => 'center',
                'type' => 'datetime',
                'filter_key' => 'a!date_add'
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

    public function renderList()
    {
        unset($this->toolbar_btn['new']);
        return parent::renderList();
    }

    public function getCustomerInfo($customerName, $params)
    {
        if ($params['id_customer']) {
            $customerLink = '<a href="'.$this->context->link->getAdminLink('AdminCustomers').'&id_customer='.
            $params['id_customer'].'&viewcustomer">'.$params['customer_name'].'<br>'.'('.$params['customer_email'].
            ')</a>';
            return $customerLink;
        } else {
            return $customerName;
        }
    }

    public function getSubscriberStatus($isSubscribe)
    {
        if ((int)$isSubscribe) {
            $response = $this->l('Subscribe');
        } else {
            $response = $this->l('Unsubscribe');
        }

        return $response;
    }

    public function getCustomerTotalPaid($idCustomer)
    {
        if ($idCustomer) {
            $customerOrderTotal = WkPwaHelper::getCustomerOrdersTotalPaid($idCustomer, 0, true);
            $response = '<span class="badge badge-success">'.$customerOrderTotal.'</span>';
            return $response;
        } else {
            return '-';
        }
    }
}
