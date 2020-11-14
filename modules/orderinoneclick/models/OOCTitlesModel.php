<?php
/**
 * 2007-2016 PrestaShop
 *
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
 *  @copyright 2014-2016 Yuri Denisov
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_CAN_LOAD_FILES_')) {
    exit;
}

if (!class_exists('OOCTitlesModel')) {

    class OOCTitlesModel extends ObjectModel
    {
        public $id_sm_ooc_titles;
        public $id_shop;
        public $id_lang;
        public $title;
        public $description;

        public static $definition = array(
            'table' => 'sm_ooc_titles',
            'primary' => 'id_sm_ooc_titles',
            'fields' => array(
                'id_shop' => array('type' =>  self::TYPE_INT, 'validate' => 'isunsignedInt'),
                'id_lang' => array('type' =>  self::TYPE_INT, 'validate' => 'isunsignedInt'),
                'title' => array('type' =>  self::TYPE_HTML, 'validate' => 'isString'),
                'description' => array('type' =>  self::TYPE_HTML, 'validate' => 'isString'),
            ),
        );

        public static function getObjectByShopAndLang($id_shop = null, $id_lang = null)
        {
            if (!$id_shop || !$id_lang) {
                return null;
            }

            $sql = 'SELECT `'.self::$definition['primary'].'` FROM `'._DB_PREFIX_.self::$definition['table'].'`'
                . ' WHERE id_shop = '.(int)$id_shop.' AND id_lang = '.(int)$id_lang;
            $result = Db::getInstance()->getRow($sql);
            $id = $result[self::$definition['primary']];
            if ($id) {
                return new self($id);
            }

            return null;
        }
    }
}
