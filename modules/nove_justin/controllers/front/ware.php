<?php

include_once(dirname(__FILE__).'/../../classes/api_get.php');
class nove_justinwareModuleFrontController extends ModuleFrontController
{
    public $display_header = false;
    public $display_column_left = false;
    public $display_column_right = false;
    public $display_footer = false;
    public $ssl = false;

    public function postProcess()
    {
        parent::postProcess();
        $list=array();
        $type = Tools::GetValue('type');
        $uuid_owner = Tools::GetValue('uuid_owner');
        
        if ($type == 'town') {
          $select ='<option value="0" selected="selected">Выберите город</option>';
          $list = Justin_get::townList($uuid_owner);
          foreach ($list as $option) {
            $select .='<option value="'.$option['uuid'].'">'.$option['descr'].'</option>';
          }
           echo $select;
           die();
        }
        if ($type == 'ware') {
          $select ='<option value="0" selected="selected">Выберите отделение</option>';
          $list = Justin_get::wareList($uuid_owner);
          foreach ($list as $option) {
            $select .='<option value="'.$option['branch'].'">'.$option['descr'].'</option>';
          }
           echo $select;
            die();
        }
        if ($type == 'adr') {
            $id_cart= Tools::GetValue('id_cart');
            $uuid_region = Tools::GetValue('uuid_region');
            $uuid_town = Tools::GetValue('uuid_town');
            $uuid_ware = Tools::GetValue('uuid_ware');
              $sql_select = "SELECT * FROM `"._DB_PREFIX_."nv_justin_carts` WHERE `id_cart`=".$id_cart;
              $cart = Db::getInstance()->executeS($sql_select);
              if (count($cart) > 0)
                {
                 $sql_insert = "UPDATE `"._DB_PREFIX_."nv_justin_carts` SET `region` = '$uuid_region', `town` = '$uuid_town', `ware` = '$uuid_ware'  WHERE `id_cart` = $id_cart";
                }
              else
                {
                 $sql_insert = "INSERT INTO `"._DB_PREFIX_."nv_justin_carts` (`id_cart`, `region`, `town`, `ware`) VALUES ($id_cart, '$uuid_region', '$uuid_town', '$uuid_ware')";
                }
              Db::getInstance()->execute($sql_insert);
        }
        die();
    }
}