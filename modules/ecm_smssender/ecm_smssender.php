<?php
if (!defined('_PS_VERSION_')) {
    exit;
}
require_once _PS_MODULE_DIR_ . 'ecm_smssender/classes/turbosms.php';
require_once _PS_MODULE_DIR_ . 'ecm_smssender/classes/message.php';
class Ecm_smssender extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'ecm_smssender';
        $this->tab = 'administration';
        $this->version = '1.1';
        $this->author = 'elcommerce';
        $this->need_instance = 1;
        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('SMS sender');
        $this->description = $this->l('Send SMS to customer via servise turbosms.ua');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => '1.7.99');
    }
    public function install()
    {
        Configuration::updateValue('ECM_SMSSENDER_COS', false);
        Configuration::updateValue('ECM_SMSSENDER_ACCOUNT_ALFA', 'FOZZYSHOP');
        Configuration::updateValue('ecm_smssender_status_pay', '921');
        Configuration::updateValue('ecm_smssender_status_alert', '921');
        Configuration::updateValue('ecm_smssender_status_en', '921');
        Configuration::updateValue('ecm_smssender_status_closed', '921');
        Configuration::updateValue('ecm_smssender_status_noname1', '921');
        Configuration::updateValue('ecm_smssender_status_noname2', '921');
        include dirname(__FILE__) . '/sql/install.php';
        return parent::install() &&
        $this->registerHook('actionOrderStatusUpdate') &&
        $this->registerHook('displayAdminOrder');
    }

    public function uninstall()
    {
        Configuration::deleteByName('ECM_SMSSENDER_COS');
        Configuration::deleteByName('ECM_SMSSENDER_ACCOUNT_ALFA');
        include dirname(__FILE__) . '/sql/uninstall.php';
        return parent::uninstall();
    }

    public function getContent()
    {
        if (((bool) Tools::isSubmit('submitEcm_smssenderModule')) == true) {
            $this->postProcess();
        }
        $id_lang = $this->context->language->id;
        $statuses_ = OrderState::getOrderStates((int) $id_lang);
        $status_pay_selected = Configuration::get('ecm_smssender_status_pay');
        $status_alert_selected = Configuration::get('ecm_smssender_status_alert');
        $status_en_selected = Configuration::get('ecm_smssender_status_en');
        $status_closed_selected = Configuration::get('ecm_smssender_status_closed');
        $status_noname1_selected = Configuration::get('ecm_smssender_status_noname1');
        $status_noname2_selected = Configuration::get('ecm_smssender_status_noname2');

        foreach ($statuses_ as $status) {
            $id = $status['id_order_state'];
            $statuses["$id"] = $status['name'];
        }
        $this->context->smarty->assign(
            array(
                'module_template_dir', $this->_path,
                'statuses' => $statuses,
                'status_pay_selected' => $status_pay_selected,
                'module_path' => _MODULE_DIR_ . $this->name,
                'status_alert_selected' => $status_alert_selected,
                'status_en_selected' => $status_en_selected,
                'status_closed_selected' => $status_closed_selected,
                'status_noname1_selected' => $status_noname1_selected,
                'status_noname2_selected' => $status_noname2_selected,
                'show_form' => Configuration::get('ECM_SMSSENDER_COS'),
            )
        );
        $output = $this->context->smarty->fetch($this->local_path . 'views/templates/admin/configure.tpl');

        return $this->renderForm() . $output;
    }

    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitEcm_smssenderModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
        . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $languages = $this->context->controller->getLanguages();
        $fields_value = $this->getConfigFormValues();

        foreach ($languages as $language) {
            $fields_value['ECM_SMSSENDER_PAYM'][$language['id_lang']] = Db::getInstance()->getValue("
                SELECT `payment_message` FROM `" . _DB_PREFIX_ . "ecm_smssender` WHERE `id_lang` =" . $language['id_lang']);
            $fields_value['ECM_SMSSENDER_SHIP'][$language['id_lang']] = Db::getInstance()->getValue("
                SELECT `shipping_message` FROM `" . _DB_PREFIX_ . "ecm_smssender`WHERE `id_lang` =" . $language['id_lang']);
            $fields_value['ECM_SMSSENDER_PWD'][$language['id_lang']] = Db::getInstance()->getValue("
                SELECT `pwd_message` FROM `" . _DB_PREFIX_ . "ecm_smssender`WHERE `id_lang` =" . $language['id_lang']);
            $fields_value['ECM_SMSSENDER_RECV'][$language['id_lang']] = Db::getInstance()->getValue("
                SELECT `recv_message` FROM `" . _DB_PREFIX_ . "ecm_smssender`WHERE `id_lang` =" . $language['id_lang']);
            $fields_value['ECM_SMSSENDER_ALERT'][$language['id_lang']] = Db::getInstance()->getValue("
                SELECT `alert_message` FROM `" . _DB_PREFIX_ . "ecm_smssender`WHERE `id_lang` =" . $language['id_lang']);
            $fields_value['ECM_SMSSENDER_CLOSEDSTATUS'][$language['id_lang']] = Db::getInstance()->getValue("
                SELECT `closed_status_message` FROM `" . _DB_PREFIX_ . "ecm_smssender`WHERE `id_lang` =" . $language['id_lang']);
            $fields_value['ECM_SMSSENDER_NONAMESTATUS1'][$language['id_lang']] = Db::getInstance()->getValue("
                SELECT `noname1_status_message` FROM `" . _DB_PREFIX_ . "ecm_smssender`WHERE `id_lang` =" . $language['id_lang']);
            $fields_value['ECM_SMSSENDER_NONAMESTATUS2'][$language['id_lang']] = Db::getInstance()->getValue("
                SELECT `noname2_status_message` FROM `" . _DB_PREFIX_ . "ecm_smssender`WHERE `id_lang` =" . $language['id_lang']);
        }
        $helper->tpl_vars = array(
            'fields_value' => $fields_value, /* Add values for your inputs */
            'languages' => $languages,
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-user"></i>',
                        'name' => 'ECM_SMSSENDER_ACCOUNT',
                        'label' => $this->l('Accaunt'),
                        'desc' => $this->l('Enter a valid accaunt TurboSMS'),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-key"></i>',
                        'name' => 'ECM_SMSSENDER_ACCOUNT_PASSWORD',
                        'label' => $this->l('Password'),
                        'desc' => $this->l('Enter a valid password TurboSMS'),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-sign-in"></i>',
                        'name' => 'ECM_SMSSENDER_ACCOUNT_ALFA',
                        'desc' => $this->l('Enter a valid alfa name for SMS signature'),
                        'label' => $this->l('Alfa name'),
                    ),
                    array(
                        'type' => 'textarea',
                        'col' => 6,
                        'rows' => 6,
                        'lang' => true,
                        'name' => 'ECM_SMSSENDER_PAYM',
                        'label' => $this->l('Payment message'),
                        'desc' => $this->l('Enter a payment message. Use variable {id_order},{reference_order},{total_paid}'),
                    ),
                    array(
                        'type' => 'textarea',
                        'col' => 6,
                        'rows' => 6,
                        'lang' => true,
                        'name' => 'ECM_SMSSENDER_SHIP',
                        'label' => $this->l('Shipping message'),
                        'desc' => $this->l('Enter a payment message. Use variable {id_order},{reference_order},{track_shipping},{carrier_name},{total_shipping},{firstname},{lastname}'),
                    ),
                    array(
                        'type' => 'textarea',
                        'col' => 6,
                        'rows' => 6,
                        'lang' => true,
                        'name' => 'ECM_SMSSENDER_RECV',
                        'label' => $this->l('Recvisites message'),
                        'desc' => $this->l('Enter a message with financial recvisites. Use variable {id_order},{reference_order},{total_paid},{total_shipping},{total_without_shipping}'),
                    ),
                    array(
                        'type' => 'textarea',
                        'col' => 6,
                        'rows' => 6,
                        'lang' => true,
                        'name' => 'ECM_SMSSENDER_ALERT',
                        'label' => $this->l('Alert message'),
                        'desc' => $this->l('Enter a message with alert message. Use variable {id_order},{reference_order},{customer},{track_shipping}'),
                    ),
                    array(
                        'type' => 'textarea',
                        'col' => 6,
                        'rows' => 6,
                        'lang' => true,
                        'name' => 'ECM_SMSSENDER_PWD',
                        'label' => $this->l('Registration message'),
                        'desc' => $this->l('Enter a registration message. Use variable {pwd},{customer},{shop_name}'),
                    ),
                    array(
                        'type' => 'textarea',
                        'col' => 6,
                        'rows' => 6,
                        'lang' => true,
                        'name' => 'ECM_SMSSENDER_CLOSEDSTATUS',
                        'label' => $this->l('Message when order status changes to "Closed"'),
                        'desc' => $this->l('Enter a message when the order status changes to closed.'),
                    ),
                    array(
                        'type' => 'textarea',
                        'col' => 6,
                        'rows' => 6,
                        'lang' => true,
                        'name' => 'ECM_SMSSENDER_NONAMESTATUS1',
                        'label' => $this->l('Additional field №1'),
                        'desc' => $this->l('Enter a message when changing status for additional field №1'),
                    ),
                    array(
                        'type' => 'textarea',
                        'col' => 6,
                        'rows' => 6,
                        'lang' => true,
                        'name' => 'ECM_SMSSENDER_NONAMESTATUS2',
                        'label' => $this->l('Additional field №2'),
                        'desc' => $this->l('Enter a message when changing status for additional field №2'),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Send SMS to customer'),
                        'name' => 'ECM_SMSSENDER_COS',
                        'desc' => $this->l('Send SMS to customer when order state is changed'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    protected function getConfigFormValues()
    {

        return array(
            'ECM_SMSSENDER_COS' => Configuration::get('ECM_SMSSENDER_COS'),
            'ECM_SMSSENDER_ACCOUNT' => Configuration::get('ECM_SMSSENDER_ACCOUNT'),
            'ECM_SMSSENDER_ACCOUNT_PASSWORD' => Configuration::get('ECM_SMSSENDER_ACCOUNT_PASSWORD'),
            'ECM_SMSSENDER_ACCOUNT_ALFA' => Configuration::get('ECM_SMSSENDER_ACCOUNT_ALFA'),
        );
    }
    protected function getConfigFormLangValues()
    {
        foreach ($_POST as $key => $value) {
            $keys = explode('_', $key);

            if ($keys[0] != 'ECM') {
                continue;
            }

            $name = $keys[0] . '_' . $keys[1] . '_' . $keys[2];

            switch ($name) {
                case 'ECM_SMSSENDER_PAYM':
                    Db::getInstance()->update('ecm_smssender', array('payment_message' => pSQL($value)), 'id_lang =' . $keys[3]);
                    break;
                case 'ECM_SMSSENDER_SHIP':
                    Db::getInstance()->update('ecm_smssender', array('shipping_message' => pSQL($value)), 'id_lang =' . $keys[3]);
                    break;
                case 'ECM_SMSSENDER_RECV':
                    Db::getInstance()->update('ecm_smssender', array('recv_message' => pSQL($value)), 'id_lang =' . $keys[3]);
                    break;
                case 'ECM_SMSSENDER_ALERT':
                    Db::getInstance()->update('ecm_smssender', array('alert_message' => pSQL($value)), 'id_lang =' . $keys[3]);
                    break;
                case 'ECM_SMSSENDER_PWD':
                    Db::getInstance()->update('ecm_smssender', array('pwd_message' => pSQL($value)), 'id_lang =' . $keys[3]);
                    break;
                case 'ECM_SMSSENDER_CLOSEDSTATUS':
                    Db::getInstance()->update('ecm_smssender', array('closed_status_message' => pSQL($value)), 'id_lang =' . $keys[3]);
                    break;
                case 'ECM_SMSSENDER_CLOSEDSTATUS':
                    Db::getInstance()->update('ecm_smssender', array('closed_status_message' => pSQL($value)), 'id_lang =' . $keys[3]);
                    break;

                case 'ECM_SMSSENDER_NONAMESTATUS1':
                    Db::getInstance()->update('ecm_smssender', array('noname1_status_message' => pSQL($value)), 'id_lang =' . $keys[3]);
                    break;
                case 'ECM_SMSSENDER_NONAMESTATUS2':
                    Db::getInstance()->update('ecm_smssender', array('noname2_status_message' => pSQL($value)), 'id_lang =' . $keys[3]);
                    break;
            }
        }
    }
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();
        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
        $this->getConfigFormLangValues();
    }

    public function hookActionOrderStatusUpdate($params)
    {
        
        $data = Db::getInstance()->ExecuteS("
                    SELECT
                    o.`shipping_number`,
                    o.`id_lang`,
                    a.`phone`,
                    a.`phone_mobile`
                    FROM `" . _DB_PREFIX_ . "orders` o
                    LEFT JOIN `" . _DB_PREFIX_ . "address` a
                    ON o.`id_address_delivery` = a.`id_address`
                    WHERE o.`id_order`=" . $params['id_order']);
                    
        if ($data[0]['phone_mobile']) {
            $login = Configuration::get('ECM_SMSSENDER_ACCOUNT');
            $pwd = Configuration::get('ECM_SMSSENDER_ACCOUNT_PASSWORD');
            $sender = Configuration::get('ECM_SMSSENDER_ACCOUNT_ALFA');
            if ($login && $pwd && $sender) {
           
                try {
                    if ($smssender = new Client($login, $pwd, $sender)) {
                        if ((int)$params['newOrderStatus']->id == (int)Configuration::get('ecm_smssender_status_pay')) {
                            try {
                                $smssender->send($data[0]['phone_mobile'], MessageSMS::getPaymentData($params['id_order'], $data[0]['id_lang']));
                            } catch (Exception $ex) {}
                        }

                        if ((int)$params['newOrderStatus']->id == (int)Configuration::get('ecm_smssender_status_alert')) {
                            try {
                                $smssender->send($data[0]['phone_mobile'], MessageSMS::getAlertData($params['id_order'], $data[0]['id_lang']));
                            } catch (Exception $ex) {}
                        }

                        if ((int)$params['newOrderStatus']->id == (int)Configuration::get('ecm_smssender_status_en')) {
                            try {
                                $smssender->send($data[0]['phone_mobile'], MessageSMS::getTTNData($params['id_order'], $data[0]['id_lang']));
                            } catch (Exception $ex) {}
                        }

                        if ((int)$params['newOrderStatus']->id == (int)Configuration::get('ecm_smssender_status_closed')) {
                            try {
                                $smssender->send($data[0]['phone_mobile'], MessageSMS::getClosedStatusData($params['id_order'], $data[0]['id_lang']));
                            } catch (Exception $ex) {}
                        }

                        if ((int)$params['newOrderStatus']->id == (int)Configuration::get('ecm_smssender_status_noname1')) {
                            try {
                                $smssender->send($data[0]['phone_mobile'], MessageSMS::getNoname1StatusData($params['id_order'], $data[0]['id_lang']));
                            } catch (Exception $ex) {}
                        }

                        if ((int)$params['newOrderStatus']->id == (int)Configuration::get('ecm_smssender_status_noname2')) {
                            try {
                                $smssender->send($data[0]['phone_mobile'], MessageSMS::getNoname2StatusData($params['id_order'], $data[0]['id_lang']));
                            } catch (Exception $ex) {}
                        }

                    }
                } catch (Exception $ex) {}
            }
        }

    }

    public function hookdisplayAdminOrder($params)
    {
        global $cookie;
        $this->context->smarty->assign(array(
            'id_order' => $params['id_order'],
            'id_lang' => Db::getInstance()->Execute("SELECT `id_lang` FROM `" . _DB_PREFIX_ . "orders` WHERE `id_order` = " . $params['id_order']),
            'module_path' => _MODULE_DIR_ . $this->name,
        ));
        return $this->display(__FILE__, 'displayAdminOrder.tpl');
    }

    public function getSMSQuestionList() {
        $arr =  Db::getInstance()->ExecuteS("
                SELECT `ps_ecm_smsquestion`.`id_question`, `ps_ecm_smsanswer`.`id_question` as `answer_to_question`, `question_ru`, `answer_ru` FROM `ps_ecm_smsquestion`
                LEFT JOIN `ps_ecm_smsanswer`
                ON `ps_ecm_smsquestion`.`id_question`=`ps_ecm_smsanswer`.`id_question`");

        $i = 1;
        $data_question = array();
        foreach ($arr as $value) {
            if($value['id_question'] == $i) {
                $data_question[$i] = array('id_question' => $value['id_question'], 'question_ru' => $value['question_ru']);
                $i++;
            }
        }

        return $data_question;
    }

    public function getSMSAnswerList() {
        $arr =  Db::getInstance()->ExecuteS("
                SELECT `ps_ecm_smsquestion`.`id_question`, `ps_ecm_smsanswer`.`id_question` as `answer_to_question`, `ps_ecm_smsanswer`.`id_answer`, `question_ru`, `answer_ru` FROM `ps_ecm_smsquestion`
                LEFT JOIN `ps_ecm_smsanswer`
                ON `ps_ecm_smsquestion`.`id_question`=`ps_ecm_smsanswer`.`id_question`");

        $b = 1;
        $data_answer = array();
        foreach ($arr as $value) {
            if($value['answer_to_question'] == $b) {
                $data_answer[$b][] = array('id_answer' => $value['id_answer'], 'answer_ru' => $value['answer_ru']);
            } else {
                $b++;
                $data_answer[$b][] = array('id_answer' => $value['id_answer'], 'answer_ru' => $value['answer_ru']);
            }
        }
        return $data_answer;
    }

    public function addSMSQuestionList() {
        $arr_list = $_POST;
        if(!empty($arr_list['id_order'])) {
            $id_customer = Db::getInstance()->getValue("SELECT `id_customer` FROM `" . _DB_PREFIX_ . "orders` WHERE `id_order` =" . $arr_list['id_order']);
            for ($i = 0; $i < count($arr_list['question']); $i++) {
                Db::getInstance()->insert('ecm_smslistanswer', array('id_customer' => $id_customer, 'id_order' => $arr_list['id_order'], 'id_question_ru' => $arr_list['question'][$i], 'id_answer_ru' => $arr_list['answer_'.$i]));
            }
            header( 'Location: /', true, 303 );
        } else {
            header( 'Location: /', true, 303 );
        }

    }
}

