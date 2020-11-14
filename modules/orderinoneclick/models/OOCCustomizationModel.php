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

if (!class_exists('OOCCustomizationModel')) {

    class OOCCustomizationModel extends ObjectModel
    {
        public $id_sm_ooc_customization;
        public $id_sm_ooc_cart_product;
        public $customization_type;

        public $customization_value;

        public static $definition = array(
            'table' => 'sm_ooc_customization',
            'primary' => 'id_sm_ooc_customization',
            'fields' => array(
                'id_sm_ooc_cart_product' => array('type' =>  self::TYPE_INT, 'validate' => 'isunsignedInt'),
                'customization_type' => array('type' =>  self::TYPE_INT, 'validate' => 'isunsignedInt'),
                'customization_value' => array('type' =>  self::TYPE_HTML, 'validate' => 'isString'),
            ),
        );

        public function add($auto_date = true, $null_values = false)
        {
            $result = parent::add($auto_date, $null_values);

            if ($this->customization_type == Product::CUSTOMIZE_FILE) {
                $result = $result && Tools::copy(
                    _PS_UPLOAD_DIR_.$this->customization_value,
                    dirname(__FILE__).'/../files/'.$this->customization_value
                );
                $result = $result && Tools::copy(
                    _PS_UPLOAD_DIR_.$this->customization_value.'_small',
                    dirname(__FILE__).'/../files/'.$this->customization_value.'_small'
                );
            }

            return $result;
        }

        public function delete()
        {
            $result = true;
            if ($this->customization_type == Product::CUSTOMIZE_FILE) {
                $result = unlink(dirname(__FILE__).'/../files/'.$this->customization_value);
                $result = $result && unlink(dirname(__FILE__).'/../files/'.$this->customization_value.'_small');
            }
            return $result && parent::delete();
        }

        public static function getObjectsByOOCProductID($id_product)
        {
            if (empty($id_product)) {
                return;
            }
            $objects = array();
            $sql = 'SELECT `'.self::$definition['primary'].'` FROM `'._DB_PREFIX_.self::$definition['table'].'`'
                . ' WHERE id_sm_ooc_cart_product = '.(int)$id_product;
            $sql_result = Db::getInstance()->ExecuteS($sql);
            foreach ($sql_result as $ooc_customization) {
                $objects[] = new OOCCustomizationModel($ooc_customization[self::$definition['primary']]);
            }
            return !empty($objects) ? $objects : null;
        }
    }
}
