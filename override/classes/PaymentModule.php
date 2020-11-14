<?php
class PaymentModule extends PaymentModuleCore
{
    /**
     * add ORDER BY hm.`position`
     */
    /*
    * module: ecm_checkout
    * date: 2020-11-11 13:58:06
    * version: 0.4.0
    */
    public static function getInstalledPaymentModules()
    {
        $hook_payment = 'Payment';
        if (Db::getInstance()->getValue('SELECT `id_hook` FROM `' . _DB_PREFIX_ . 'hook` WHERE `name` = \'paymentOptions\'')) {
            $hook_payment = 'paymentOptions';
        }
        return Db::getInstance()->executeS('
        SELECT DISTINCT m.`id_module`, h.`id_hook`, m.`name`, hm.`position`
        FROM `' . _DB_PREFIX_ . 'module` m
        LEFT JOIN `' . _DB_PREFIX_ . 'hook_module` hm ON hm.`id_module` = m.`id_module`'
        . Shop::addSqlRestriction(false, 'hm') . '
        LEFT JOIN `' . _DB_PREFIX_ . 'hook` h ON hm.`id_hook` = h.`id_hook`
        INNER JOIN `' . _DB_PREFIX_ . 'module_shop` ms ON (m.`id_module` = ms.`id_module` AND ms.id_shop=' . (int) Context::getContext()->shop->id . ')
        WHERE h.`name` = \'' . pSQL($hook_payment) . '\' ORDER BY hm.`position`');
    }
}