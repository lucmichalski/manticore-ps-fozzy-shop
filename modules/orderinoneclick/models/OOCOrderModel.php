<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    Yuri Denisov <contact@splashmart.ru>
 *  @copyright 2014-2017 Yuri Denisov
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_CAN_LOAD_FILES_')) {
    exit;
}

if (!class_exists('OOCOrderModel')) {
    include_once(dirname(__FILE__).'/OOCUserFieldsCorrModel.php');

    class OOCOrderModel extends ObjectModel
    {
        public $id_sm_ooc_order;
        public $id_shop;
        public $id_sm_ooc_group;
        public $id_sm_ooc_cart;
        public $id_customer;
        public $id_guest;
        public $date;
        public $comment;

        // Этих полей нет в таблице
        public $ooc_cart = null;
        public $ooc_fields = null;

        public static $definition = array(
            'table' => 'sm_ooc_order',
            'primary' => 'id_sm_ooc_order',
            'multishop' => true,
            'fields' => array(
                'id_shop' => array('type' =>  self::TYPE_INT, 'validate' => 'isunsignedInt'),
                'id_sm_ooc_group' => array('type' =>  self::TYPE_INT, 'validate' => 'isunsignedInt'),
                'id_sm_ooc_cart' => array('type' =>  self::TYPE_INT, 'validate' => 'isunsignedInt'),
                'id_customer' => array('type' =>  self::TYPE_INT, 'validate' => 'isunsignedInt'),
                'id_guest' => array('type' =>  self::TYPE_INT, 'validate' => 'isunsignedInt'),
                'date' => array('type' =>  self::TYPE_DATE, 'validate' => 'isDateFormat'),
                'comment' => array('type' =>  self::TYPE_HTML, 'validate' => 'isString'),
            ),
        );

        public function __construct($id = null, $id_lang = null, $id_shop = null)
        {
            parent::__construct($id, $id_lang, $id_shop);

            $this->ooc_cart = $id ? new OOCCartModel($this->id_sm_ooc_cart) : null;
            $this->ooc_fields = $id ? OOCFieldsModel::getObjectsByIDOrder($this->id_sm_ooc_order) : null;
        }

        public function delete()
        {
            $result = true;

            $result = $result && $this->ooc_cart->delete();

            if (!empty($this->ooc_fields)) {
                foreach ($this->ooc_fields as $field) {
                    $result = $result && $field->delete();
                }
            }

            return $result && parent::delete();
        }

        public function getCustomerEmails()
        {
            $email = array();
            foreach ($this->ooc_fields as $field) {
                $ooc_order_field = new OOCOrderFieldsModel($field->id_sm_ooc_order_fields);
                $corr = OOCUserFieldsCorrModel::getCorrByIDType($ooc_order_field->id_sm_ooc_type_order_field);
                if ($corr == 'email' && !empty($field->value)) {
                    $email[] = $field->value;
                }
            }
            return !empty($email) ? $email : null;
        }

        public function getCustomerName()
        {
            $name = null;
            foreach ($this->ooc_fields as $field) {
                $ooc_order_field = new OOCOrderFieldsModel($field->id_sm_ooc_order_fields);
                $corr = OOCUserFieldsCorrModel::getCorrByIDType($ooc_order_field->id_sm_ooc_type_order_field);
                if ($corr == 'name' && !empty($field->value)) {
                    $name = $name.' '.$field->value;
                }
            }
            return $name;
        }

        public function getCustomerPhones()
        {
            $phones = array();
            foreach ($this->ooc_fields as $field) {
                $ooc_order_field = new OOCOrderFieldsModel($field->id_sm_ooc_order_fields);
                $corr = OOCUserFieldsCorrModel::getCorrByIDType($ooc_order_field->id_sm_ooc_type_order_field);
                if ($corr == 'phone' && !empty($field->value)) {
                    $phones[] = $field->value;
                }
            }
            return !empty($phones) ? $phones : null;
        }

        public static function getNumNotViewedOrders($id_shop = null)
        {
            if (!$id_shop) {
                return;
            }

            $sql = 'SELECT count(`'.self::$definition['primary'].'`) as num FROM `'
                . _DB_PREFIX_.self::$definition['table'].'`'
                .  ' WHERE id_sm_ooc_group = '.(int)Configuration::get('SM_OOC_DEFAULT_GROUP_ORDERS')
                . ' AND id_shop = '.(int)$id_shop;
            $sql_result = Db::getInstance()->getRow($sql);

            return $sql_result['num'];
        }

        public static function getObjectsByIDCustomer($id_customer = null, $without_id_ooc = null)
        {
            if (empty($id_customer)) {
                return;
            }
            $objects = array();
            $sql = 'SELECT `'.self::$definition['primary'].'` FROM `'._DB_PREFIX_.self::$definition['table'].'`'
                . ' WHERE id_customer = '.(int)$id_customer;
            $sql_result = Db::getInstance()->ExecuteS($sql);
            foreach ($sql_result as $ooc_order) {
                if ($ooc_order[self::$definition['primary']] != $without_id_ooc) {
                    $objects[] = new self($ooc_order[self::$definition['primary']]);
                }
            }
            return $objects;
        }

        public static function getObjectsByEmail(array $emails = null, $without_id_ooc = null)
        {
            if (empty($emails)) {
                return;
            }
            $objects = array();
            $sql = 'SELECT `'.self::$definition['primary'].'` FROM `'._DB_PREFIX_.self::$definition['table'].'`';
            $sql_result = Db::getInstance()->ExecuteS($sql);
            foreach ($sql_result as $ooc_order) {
                if ($ooc_order[self::$definition['primary']] != $without_id_ooc) {
                    $ooc = new self($ooc_order[self::$definition['primary']]);
                    $ooc_order_emails = $ooc->getEmailsFromOrder();
                    $customer = new Customer($ooc->id_customer);
                    if ($customer->email) {
                        $ooc_order_emails[] = $customer->email;
                    }
                    $present = false;
                    foreach ($emails as $email) {
                        if (in_array($email, $ooc_order_emails)) {
                            $present = true;
                        }
                    }
                    if ($present) {
                        $objects[] = $ooc;
                    }
                }
            }
            return $objects;
        }

        public function getEmailsFromOrder()
        {
            $emails = array();
            foreach ($this->ooc_fields as $field) {
                if ($field->isEmailField()) {
                    $emails[] = $field->value;
                }
            }
            return $emails;
        }
    }
}
