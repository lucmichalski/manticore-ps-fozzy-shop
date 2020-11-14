<?php
/**
* Quantity Discount Pro
*
* NOTICE OF LICENSE
*
* This product is licensed for one customer to use on one installation (test stores and multishop included).
* Site developer has the right to modify this module to suit their needs, but can not redistribute the module in
* whole or in part. Any other use of this module constitues a violation of the user agreement.
*
* DISCLAIMER
*
* NO WARRANTIES OF DATA SAFETY OR MODULE SECURITY
* ARE EXPRESSED OR IMPLIED. USE THIS MODULE IN ACCORDANCE
* WITH YOUR MERCHANT AGREEMENT, KNOWING THAT VIOLATIONS OF
* PCI COMPLIANCY OR A DATA BREACH CAN COST THOUSANDS OF DOLLARS
* IN FINES AND DAMAGE A STORES REPUTATION. USE AT YOUR OWN RISK.
*
*  @author    idnovate.com <info@idnovate.com>
*  @copyright 2019 idnovate.com
*  @license   See above
*/

class CartRule extends CartRuleCore
{
    public static function autoRemoveFromCart($context = null)
    {
        if (Module::isEnabled('quantitydiscountpro')) {
            include_once(_PS_MODULE_DIR_.'quantitydiscountpro/quantitydiscountpro.php');
            $quantityDiscount = new QuantityDiscountRule();
            $quantityDiscount->createAndRemoveRules(null, $context);
        }

        parent::autoRemoveFromCart($context);
    }

    public static function autoAddToCart(Context $context = null)
    {
        parent::autoAddToCart($context);

        if (Module::isEnabled('quantitydiscountpro')) {
            include_once(_PS_MODULE_DIR_.'quantitydiscountpro/quantitydiscountpro.php');
            $quantityDiscount = new QuantityDiscountRule();
            $quantityDiscount->createAndRemoveRules(null, $context);
        }
    }

    public function update($null_values = false)
    {
        $r = parent::update($null_values);

        if (Module::isEnabled('quantitydiscountpro')) {
            include_once(_PS_MODULE_DIR_.'quantitydiscountpro/quantitydiscountpro.php');
            if ((bool)Configuration::get('PS_CART_RULE_FEATURE_ACTIVE') != (bool)QuantityDiscountRule::isCurrentlyUsed(null, true)
                || (bool)QuantityDiscountRule::isCurrentlyUsed(null, true)) {
                Configuration::updateGlobalValue('PS_CART_RULE_FEATURE_ACTIVE', true);
            }
        }

        return $r;
    }

    public function delete()
    {
        $r = parent::delete();

        if (Module::isEnabled('quantitydiscountpro')) {
            include_once(_PS_MODULE_DIR_.'quantitydiscountpro/quantitydiscountpro.php');
            if ((bool)Configuration::get('PS_CART_RULE_FEATURE_ACTIVE') != (bool)QuantityDiscountRule::isCurrentlyUsed(null, true)
                || (bool)QuantityDiscountRule::isCurrentlyUsed(null, true)) {
                Configuration::updateGlobalValue('PS_CART_RULE_FEATURE_ACTIVE', true);
            }
        }

        return $r;
    }

    public static function getCustomerCartRules($id_lang, $id_customer, $active = false, $includeGeneric = true, $inStock = false, Cart $cart = null, $free_shipping_only = false)
    {
        $result = parent::getCustomerCartRules($id_lang, $id_customer, $active, $includeGeneric, $inStock, $cart, $free_shipping_only);
        if (!Module::isEnabled('quantitydiscountpro')) {
            return $result;
        }

        include_once(_PS_MODULE_DIR_.'quantitydiscountpro/quantitydiscountpro.php');
        $quantityDiscount = new QuantityDiscountRule();

        return array_merge($result, $quantityDiscount->getHighlightedQuantityDiscountRules());
    }

    protected function getCartRuleCombinations()
    {
        if (!Module::isEnabled('quantitydiscountpro')) {
            return parent::getCartRuleCombinations();
        }

        $array = array();
        $array['selected'] = Db::getInstance()->executeS('
        SELECT cr.*, crl.*, 1 as selected
        FROM '._DB_PREFIX_.'cart_rule cr
        LEFT JOIN '._DB_PREFIX_.'cart_rule_lang crl ON (cr.id_cart_rule = crl.id_cart_rule AND crl.id_lang = '.(int)Context::getContext()->language->id.')
        WHERE cr.id_cart_rule != '.(int)$this->id.'
        AND (
            cr.cart_rule_restriction = 0
            OR cr.id_cart_rule IN (
                SELECT IF(id_cart_rule_1 = '.(int)$this->id.', id_cart_rule_2, id_cart_rule_1)
                FROM '._DB_PREFIX_.'cart_rule_combination
                WHERE '.(int)$this->id.' = id_cart_rule_1
                OR '.(int)$this->id.' = id_cart_rule_2
            )
        )
        AND cr.id_cart_rule NOT IN (SELECT id_cart_rule FROM `'._DB_PREFIX_.'quantity_discount_rule_cart`)');

        $array['unselected'] = Db::getInstance()->executeS('
        SELECT cr.*, crl.*, 1 as selected
        FROM '._DB_PREFIX_.'cart_rule cr
        LEFT JOIN '._DB_PREFIX_.'cart_rule_lang crl ON (cr.id_cart_rule = crl.id_cart_rule AND crl.id_lang = '.(int)Context::getContext()->language->id.')
        WHERE cr.cart_rule_restriction = 1
        AND cr.id_cart_rule != '.(int)$this->id.'
        AND cr.id_cart_rule NOT IN (
            SELECT IF(id_cart_rule_1 = '.(int)$this->id.', id_cart_rule_2, id_cart_rule_1)
            FROM '._DB_PREFIX_.'cart_rule_combination
            WHERE '.(int)$this->id.' = id_cart_rule_1
            OR '.(int)$this->id.' = id_cart_rule_2
        )
        AND cr.id_cart_rule NOT IN (SELECT id_cart_rule FROM `'._DB_PREFIX_.'quantity_discount_rule_cart`)');
        return $array;
    }

    public function getAssociatedRestrictions($type, $active_only, $i18n)
    {
        if (!Module::isEnabled('quantitydiscountpro')) {
            return parent::getAssociatedRestrictions($type, $active_only, $i18n);
        }

        $array = array('selected' => array(), 'unselected' => array());

        if (!in_array($type, array('country', 'carrier', 'group', 'cart_rule', 'shop'))) {
            return false;
        }

        $shop_list = '';
        if ($type == 'shop') {
            $shops = Context::getContext()->employee->getAssociatedShops();
            if (count($shops)) {
                $shop_list = ' AND t.id_shop IN ('.implode(array_map('intval', $shops), ',').') ';
            }
        }

        if (!Validate::isLoadedObject($this) || $this->{$type.'_restriction'} == 0) {
            $array['selected'] = Db::getInstance()->executeS('
            SELECT t.*'.($i18n ? ', tl.*' : '').', 1 as selected
            FROM `'._DB_PREFIX_.$type.'` t
            '.($i18n ? 'LEFT JOIN `'._DB_PREFIX_.$type.'_lang` tl ON (t.id_'.$type.' = tl.id_'.$type.' AND tl.id_lang = '.(int)Context::getContext()->language->id.')' : '').'
            WHERE 1
            '.($active_only ? 'AND t.active = 1' : '').'
            '.(in_array($type, array('carrier', 'shop')) ? ' AND t.deleted = 0' : '').'
            '.($type == 'cart_rule' ? 'AND t.id_cart_rule != '.(int)$this->id.' AND t.id_cart_rule NOT IN (SELECT id_cart_rule FROM `'._DB_PREFIX_.'quantity_discount_rule_cart`)' : '') .
            $shop_list.
            ' ORDER BY name ASC');
        } else {
            if ($type == 'cart_rule') {
                $array = $this->getCartRuleCombinations();
            } else {
                $resource = Db::getInstance()->query(
                    'SELECT t.*'.($i18n ? ', tl.*' : '').', IF(crt.id_'.$type.' IS NULL, 0, 1) as selected
                    FROM `'._DB_PREFIX_.$type.'` t
                    '.($i18n ? 'LEFT JOIN `'._DB_PREFIX_.$type.'_lang` tl ON (t.id_'.$type.' = tl.id_'.$type.' AND tl.id_lang = '.(int)Context::getContext()->language->id.')' : '').'
                    LEFT JOIN (SELECT id_'.$type.' FROM `'._DB_PREFIX_.'cart_rule_'.$type.'` WHERE id_cart_rule = '.(int)$this->id.') crt ON t.id_'.($type == 'carrier' ? 'reference' : $type).' = crt.id_'.$type.'
                    WHERE 1 '.($active_only ? ' AND t.active = 1' : '').
                        $shop_list
                        .(in_array($type, array('carrier', 'shop')) ? ' AND t.deleted = 0' : '').
                        ' ORDER BY name ASC',
                    false
                );

                while ($row = Db::getInstance()->nextRow($resource)) {
                    $array[($row['selected'] || $this->{$type.'_restriction'} == 0) ? 'selected' : 'unselected'][] = $row;
                }
            }
        }

        return $array;
    }
}
