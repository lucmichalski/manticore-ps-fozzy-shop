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

if (!class_exists('OOCFieldsModel')) {

    class OOCFieldsModel extends ObjectModel
    {
        public $id_sm_ooc_fields;
        public $id_sm_ooc_order;
        public $id_sm_ooc_order_fields;
        public $value;

        public $name;

        public static $definition = array(
            'table' => 'sm_ooc_fields',
            'primary' => 'id_sm_ooc_fields',
            'fields' => array(
                'id_sm_ooc_order' => array('type' =>  self::TYPE_INT, 'validate' => 'isunsignedInt'),
                'id_sm_ooc_order_fields' => array('type' =>  self::TYPE_INT, 'validate' => 'isunsignedInt'),
                'value' => array('type' =>  self::TYPE_HTML, 'validate' => 'isString'),
            ),
        );

        public function __construct($id = null, $id_lang = null, $id_shop = null)
        {
            parent::__construct($id, $id_lang, $id_shop);
            $field_type = new OOCOrderFieldsModel($this->id_sm_ooc_order_fields);
            $this->name = $field_type->name;
        }

        public function isEmailField()
        {
            $order_field = new OOCOrderFieldsModel($this->id_sm_ooc_order_fields);
            $type_order_field = new OOCTypeOrderFieldsModel($order_field->id_sm_ooc_type_order_field);
            return ($type_order_field->validate_func == 'isEmail') ? true : false;
        }

        public static function getObjectsByIDOrder($id_sm_ooc_order = null)
        {
            if (empty($id_sm_ooc_order)) {
                return;
            }
            $objects = array();
            $sql = 'SELECT `'.self::$definition['primary'].'` FROM `'._DB_PREFIX_.self::$definition['table'].'`'
                . ' WHERE id_sm_ooc_order = '.(int)$id_sm_ooc_order.' ORDER BY `'.self::$definition['primary'].'` ASC';
            $sql_result = Db::getInstance()->ExecuteS($sql);
            foreach ($sql_result as $ooc_field) {
                $objects[] = new OOCFieldsModel($ooc_field[self::$definition['primary']]);
            }
            return $objects;
        }
    }
}
