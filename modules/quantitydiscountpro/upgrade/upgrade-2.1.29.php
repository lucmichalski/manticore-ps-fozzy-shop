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

function upgrade_module_2_1_29()
{
    $columnExists = Db::getInstance()->getRow(
        "SELECT *
        FROM information_schema.COLUMNS
        WHERE
            TABLE_SCHEMA = '"._DB_NAME_."'
        AND TABLE_NAME = '"._DB_PREFIX_."quantity_discount_rule'
        AND COLUMN_NAME = 'id_shop'"
    );

    if (!$columnExists) {
        Db::getInstance()->execute(
            "ALTER TABLE `"._DB_PREFIX_."quantity_discount_rule`
            ADD `id_shop` int(11) unsigned NOT NULL DEFAULT '1' AFTER `id_quantity_discount_rule`"
        );
    }

    $columnExists = Db::getInstance()->getRow(
        "SELECT *
        FROM information_schema.COLUMNS
        WHERE
            TABLE_SCHEMA = '"._DB_NAME_."'
        AND TABLE_NAME = '"._DB_PREFIX_."quantity_discount_rule_family'
        AND COLUMN_NAME = 'id_shop'"
    );

    if (!$columnExists) {
        Db::getInstance()->execute(
            "ALTER TABLE `"._DB_PREFIX_."quantity_discount_rule_family`
            ADD `id_shop` int(11) unsigned NOT NULL DEFAULT '1' AFTER `id_quantity_discount_rule_family`"
        );
    }

    if (Shop::isFeatureActive()) {
        $newFamilies = array();
        $rules = Db::getInstance()->executeS(
            "SELECT *
            FROM `"._DB_PREFIX_."quantity_discount_rule`"
        );

        foreach ($rules as $rule) {
            $conditions = Db::getInstance()->executeS(
                "SELECT *
                FROM `"._DB_PREFIX_."quantity_discount_rule_condition_shop`
                WHERE `id_quantity_discount_rule` = ".(int)$rule['id_quantity_discount_rule']
            );

            $shops = array();
            if (count($conditions)) {
                foreach ($conditions as $condition) {
                    $shops[] = $condition['id_shop'];
                }
            } else {
                $shops = Shop::getShops(false, null, true);
            }

            foreach ($shops as $shop) {
                if ($shop == 1) {
                    continue;
                }

                if (!isset($newFamilies[(int)$rule['id_family']][$shop])
                	|| !Validate::isLoadedObject($newFamilies[(int)$rule['id_family']][$shop])) {
                    if (Validate::isLoadedObject($quantityDiscountRuleFamily = new QuantityDiscountRuleFamily((int)$rule['id_family']))) {
                        $old_id = $quantityDiscountRuleFamily->id;
                        unset($quantityDiscountRuleFamily->id);
                        $quantityDiscountRuleFamily->id_shop = $shop;

                        $quantityDiscountRuleFamily->add();

                        $newFamilies[(int)$rule['id_family']][$shop] = (int)Db::getInstance()->Insert_ID();
                    }
                }

                if (Validate::isLoadedObject($quantityDiscountRule = new QuantityDiscountRule((int)$rule['id_quantity_discount_rule']))) {
                    $old_id = $quantityDiscountRule->id;
                    unset($quantityDiscountRule->id);
                    $quantityDiscountRule->id_shop = $shop;
                    $quantityDiscountRule->active = 1;
                    $quantityDiscountRule->id_family = $newFamilies[(int)$rule['id_family']][$shop];

                    $quantityDiscountRule->add();
                    QuantityDiscountRule::duplicateTableRecords($quantityDiscountRule->id, $old_id);
                }
            }

            if (!in_array('1', $shops)) {
                $quantityDiscountRule = new QuantityDiscountRule((int)$rule['id_quantity_discount_rule']);
                $quantityDiscountRule->delete();
            }
        }
    }

    //Remove empty families
    $families = QuantityDiscountRuleFamily::getQuantityDiscountRuleFamilies(false);
    foreach ($families as $family) {
        $quantityDiscountRules = Db::getInstance()->executeS(
            "SELECT *
            FROM `"._DB_PREFIX_."quantity_discount_rule`
            WHERE `id_family` = ".(int)$family['id_quantity_discount_rule_family']
        );

        if (!count($quantityDiscountRules)) {
            $quantityDiscountRuleFamily = new QuantityDiscountRuleFamily((int)$family['id_quantity_discount_rule_family']);
            $quantityDiscountRuleFamily->delete();
        }
    }


    Db::getInstance()->execute(
        "DROP TABLE IF EXISTS `"._DB_PREFIX_."quantity_discount_rule_condition_shop`"
    );

    Db::getInstance()->execute(
        "DELETE
        FROM `"._DB_PREFIX_."quantity_discount_rule_condition`
        WHERE `id_type` = 14"
    );

    return true;
}
