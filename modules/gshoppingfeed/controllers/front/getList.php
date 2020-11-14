<?php
/**
 * 2016 Terranet
 *
 * NOTICE OF LICENSE
 *
 * @author    Terranet
 * @copyright 2016 Terranet
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class GshoppingfeedGetListModuleFrontController extends ModuleFrontController
{
    public function __construct()
    {
        $token = Tools::getValue('token');
        if (!Tools::getValue('token') || empty($token)
            || !Tools::getValue('key')
            || md5(_COOKIE_KEY_ . Tools::getValue('key')) != Tools::getValue('token')
        ) {
            Tools::redirect('index.php?controller=index');
        }

        parent::__construct();
    }

    public function initContent()
    {
        if (Tools::getValue('key')
            && Validate::isInt(Tools::getValue('key'))
            && (int)Tools::getValue('key') > 0) {
            $ret = array();
            $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'gshoppingfeed` WHERE `id_gshoppingfeed` = ' . (int)Tools::getValue('key');
            $gshoppingfeed = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
            if ($gshoppingfeed && count($gshoppingfeed) > 0) {
                $ret['name_feed'] = !empty($gshoppingfeed['name']) ? $gshoppingfeed['name'] : Configuration::get('PS_SHOP_NAME');
                $ret['brand_type'] = $gshoppingfeed['brand_type'];
                $ret['only_active'] = (int)$gshoppingfeed['only_active'];
                $ret['description_crop'] = (int)$gshoppingfeed['description_crop'];
                $ret['modify_uppercase_title'] = (int)$gshoppingfeed['modify_uppercase_title'];
                $ret['modify_uppercase_description'] = (int)$gshoppingfeed['modify_uppercase_description'];
                $ret['parts_payment_enabled'] = (int)$gshoppingfeed['parts_payment_enabled'];
                $ret['product_title_in_product_type'] = (int)$gshoppingfeed['product_title_in_product_type'];
                $ret['identifier_exists_mpn'] = (int)$gshoppingfeed['identifier_exists_mpn'];
                $ret['visible_product_hide'] = (int)$gshoppingfeed['visible_product_hide'];
                $ret['mpn_force_on'] = (int)$gshoppingfeed['mpn_force_on'];
                $ret['max_parts_payment'] = (int)$gshoppingfeed['max_parts_payment'];
                $ret['interest_rates'] = (float)$gshoppingfeed['interest_rates'];
                $ret['exclude_ids'] = $gshoppingfeed['exclude_ids'];
                $ret['title_suffix'] = $gshoppingfeed['title_suffix'];
                $ret['description_suffix'] = $gshoppingfeed['description_suffix'];
                $ret['export_attributes'] = $gshoppingfeed['export_attributes'];
                $ret['export_attributes_only_first'] = $gshoppingfeed['export_attributes_only_first'];
                $ret['export_attribute_url'] = $gshoppingfeed['export_attribute_url'];
                $ret['export_attribute_prices'] = $gshoppingfeed['export_attribute_prices'];
                $ret['export_attribute_images'] = $gshoppingfeed['export_attribute_images'];
                $ret['export_feature'] = $gshoppingfeed['export_feature'];
                $ret['use_additional_shipping_cost'] = $gshoppingfeed['use_additional_shipping_cost'];
                $ret['type_image'] = $gshoppingfeed['type_image'];
                $ret['type_description'] = $gshoppingfeed['type_description'];
                $ret['id_currency'] = $gshoppingfeed['id_currency'];
                $ret['id_country'] = $gshoppingfeed['id_country'];
                $ret['id_carrier'] = json_decode($gshoppingfeed['id_carrier']);
                $ret['select_lang'] = $gshoppingfeed['select_lang'];
                $ret['get_features_gender'] = $gshoppingfeed['get_features_gender'];
                $ret['get_features_age_group'] = $gshoppingfeed['get_features_age_group'];
                $ret['instance_of_tax'] = $gshoppingfeed['instance_of_tax'];
                $ret['get_attribute_size'] = json_decode($gshoppingfeed['get_attribute_size']);
                $ret['get_attribute_color'] = json_decode($gshoppingfeed['get_attribute_color']);
                $ret['get_attribute_pattern'] = json_decode($gshoppingfeed['get_attribute_pattern']);
                $ret['get_attribute_material'] = json_decode($gshoppingfeed['get_attribute_material']);
                $ret['export_non_available'] = $gshoppingfeed['export_non_available'];
                $ret['export_product_quantity'] = $gshoppingfeed['export_product_quantity'];
                $ret['additional_image'] = $gshoppingfeed['additional_image'];
                $ret['min_price'] = $gshoppingfeed['min_price_filter'];
                $ret['max_price'] = $gshoppingfeed['max_price_filter'];
                $ret['select_manufacturers'] = json_decode($gshoppingfeed['manufacturers_filter']);
                $ret['category_filter'] = json_decode($gshoppingfeed['category_filter']);
                $ret['mpn_type'] = json_decode($gshoppingfeed['mpn_type']);
                $ret['gtin_type'] = json_decode($gshoppingfeed['gtin_type']);
                $ret['id_gshoppingfeed'] = $gshoppingfeed['id_gshoppingfeed'];
                $ret['additional_each_product'] = $gshoppingfeed['additional_each_product'];
                $ret['filtered_by_associated_type'] = $gshoppingfeed['filtered_by_associated_type'];
                $ret['features_custom_modification'] = Gshoppingfeed::getCustomFeatureById((int)$gshoppingfeed['id_gshoppingfeed'], (int)$gshoppingfeed['select_lang']);
                $ret['custom_attribute'] = Gshoppingfeed::getCustomAttrById((int)$gshoppingfeed['id_gshoppingfeed'], (int)$gshoppingfeed['select_lang']);

                if (Tools::getValue('only_rebuild', false) == 1) {
                    return $this->module->generationList($ret, 'only_rebuild');
                }

                if (Tools::getValue('only_download')) {
                    $generate_path = _PS_MODULE_DIR_ . $this->module->name . DIRECTORY_SEPARATOR . 'export' . DIRECTORY_SEPARATOR . (int)Tools::getValue('key');
                    $generate_file = 'export.xml';
                    $generate_path_file = $generate_path . DIRECTORY_SEPARATOR . $generate_file;

                    $download_file_name = Date('m-d-y') . '_google';
                    header('Content-disposition: attachment; filename="' . $download_file_name . '.xml"');
                    header('Content-type: "text/xml"; charset="utf8"');
                    readfile($generate_path_file);

                    exit();
                }

                return $this->module->generationList($ret);
            }
        }

        return false;
    }
}
