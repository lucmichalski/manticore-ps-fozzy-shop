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

class AdminCartReminderPushNotificationController extends ModuleAdminController
{
    public function __construct()
    {
        $this->context = Context::getContext();
        $this->bootstrap = true;

        $this->table = 'wk_pwa_push_notification';
        $this->className = 'WkPwaPushNotification';
        $this->identifier = 'id';

        $this->display = 'add';

        parent::__construct();

        $this->toolbar_title = $this->l('Manage Cart Reminder Notification');
    }

    public function renderForm()
    {
        $objPushNotification = new WkPwaPushNotification();
        $notificationDetail = $objPushNotification->getByIdNotificationType(
            WkPwaPushNotification::CART_REMINDER_NOTIFICATION
        );
        if ($notificationDetail) {
            $notificationDetail = $objPushNotification->getCompleteNotificationDetails($notificationDetail['id']);
            $this->context->smarty->assign('notificationDetail', $notificationDetail);
        }

        $defaultVariables = $objPushNotification->getPushNotificationDefaultVariables();
        $defaultVariables['edit'] = $notificationDetail ? 1 : 0;
        $this->context->smarty->assign($defaultVariables);

        $this->fields_form = array(
            'submit' => array(
                'title' => $this->l('Save'),
            ),
        );

        return parent::renderForm();
    }

    public function processSave()
    {
        $objPushNotification = new WkPwaPushNotification();

        $fields = array(
            'edit' => trim(Tools::getValue('id')) ? 1 : 0,
            'active' => Tools::getValue('active'),
            'idPushNotification' => trim(Tools::getValue('id')),
            'idNotificationType' => WkPwaPushNotification::CART_REMINDER_NOTIFICATION,
            'title' => trim(Tools::getValue('title')),
            'body' => trim(Tools::getValue('body')),
            'targetUrl' => trim(Tools::getValue('target_url')),
            'notificationIcon' => $_FILES['icon'],
            'remainderCount' => trim(Tools::getValue('remainder_count')),
            'remainderInterval' => trim(Tools::getValue('remainder_interval')),
        );
        $notification = $objPushNotification->procressPushNotificationFields($fields);

        if (count($notification['errors'])) {
            $this->errors = $notification['errors'];
            if (Tools::getValue('id')) {
                $this->display = 'edit';
            } else {
                $this->display = 'add';
            }
        } else {
            if (Tools::getValue('id')) {
                Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
            } else {
                Tools::redirectAdmin(self::$currentIndex.'&conf=3&token='.$this->token);
            }
        }
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        $this->addCSS(_MODULE_DIR_.$this->module->name.'/views/css/admin/push_notification.css');
        $this->addJS(_MODULE_DIR_.$this->module->name.'/views/js/admin/push_notification.js');
    }
}
