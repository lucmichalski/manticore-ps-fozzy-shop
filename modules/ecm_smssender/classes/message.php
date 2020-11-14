<?php
class MessageSMS
{

    public static function getPaymentData($id_order,$id_lang)
    {
        $message = Db::getInstance()->getValue("
            SELECT `payment_message` FROM `" . _DB_PREFIX_ . "ecm_smssender` WHERE `id_lang`=".$id_lang);
        return     self::cteateMessageFromTemplate($id_order,$message);

    }
    public static function getTTNData($id_order,$id_lang)
    {
        $message = Db::getInstance()->getValue("
            SELECT `shipping_message` FROM `" . _DB_PREFIX_ . "ecm_smssender` WHERE `id_lang`=".$id_lang);
        return     self::cteateMessageFromTemplate($id_order,$message);
    }
    public static function getRecvData($id_order,$id_lang)
    {
        $message = Db::getInstance()->getValue("
            SELECT `recv_message` FROM `" . _DB_PREFIX_ . "ecm_smssender` WHERE `id_lang`=".$id_lang);
            //d(self::cteateMessageFromTemplate($id_order,$message));
        return     self::cteateMessageFromTemplate($id_order,$message);
    }
    public static function getAlertData($id_order,$id_lang)
    {
        $message = Db::getInstance()->getValue("
            SELECT `alert_message` FROM `" . _DB_PREFIX_ . "ecm_smssender` WHERE `id_lang`=".$id_lang);
        return     self::cteateMessageFromTemplate($id_order,$message);
    }
    public static function getClosedStatusData($id_order,$id_lang)
    {
        $message = Db::getInstance()->getValue("
            SELECT `closed_status_message` FROM `" . _DB_PREFIX_ . "ecm_smssender` WHERE `id_lang`=".$id_lang);
        return     self::cteateMessageFromTemplate($id_order,$message);
    }

    public static function getNoname1StatusData($id_order,$id_lang)
    {
        $message = Db::getInstance()->getValue("
            SELECT `noname1_status_message` FROM `" . _DB_PREFIX_ . "ecm_smssender` WHERE `id_lang`=".$id_lang);
        return     self::cteateMessageFromTemplate($id_order,$message);
    }

    public static function getNoname2StatusData($id_order,$id_lang)
    {
        $message = Db::getInstance()->getValue("
            SELECT `noname2_status_message` FROM `" . _DB_PREFIX_ . "ecm_smssender` WHERE `id_lang`=".$id_lang);
        return     self::cteateMessageFromTemplate($id_order,$message);
    }

    public static function cteateMessageFromTemplate($id_order,$message)
    {
        $data = Db::getInstance()->ExecuteS("
            SELECT
            o.`reference`,
            o.`shipping_number`,
            o.`total_paid`,
            o.`total_shipping`,
            o.`total_paid` - o.`total_shipping` as total_without_shipping,
            o.`id_address_delivery`,
            c.`name`,
            cu.`firstname`,
            cu.`lastname`
            FROM `" . _DB_PREFIX_ . "orders` o
            LEFT JOIN `" . _DB_PREFIX_ . "carrier` c
            ON c.`id_carrier`= o.`id_carrier`
            LEFT JOIN `" . _DB_PREFIX_ . "customer` cu
            ON cu.id_customer = o.id_customer
            WHERE o.`id_order`=".$id_order);
            $address_data = Db::getInstance()->getRow("
            SELECT `city`,`address1` FROM `" . _DB_PREFIX_ . "address` WHERE `id_address`=".$data[0]['id_address_delivery']);
            $customer = $data[0]['firstname']." ".$data[0]['lastname'];
            $delivery_address = implode(", ",$address_data); 
        return
        str_replace(
        
        array("{id_order}","{reference_order}","{track_shipping}","{carrier_name}","{total_paid}","{total_shipping}","{firstname}","{lastname}","{customer}","{total_without_shipping}","{delivery_address}"),
            
        array($id_order,pSQL($data[0]['reference']),pSQL($data[0]['shipping_number']),pSQL($data[0]['name']),pSQL(Tools::ps_round($data[0]['total_paid'],2)),pSQL(Tools::ps_round($data[0]['total_shipping'],2)),pSQL($data[0]['firstname']),pSQL($data[0]['lastname']),$customer,pSQL(Tools::ps_round($data[0]['total_without_shipping'],2)),$delivery_address),
        
        $message);
    }
     public static function getPWDData($customer,$pwd,$id_lang)
    {
        $message = Db::getInstance()->getValue("
            SELECT `pwd_message` FROM `" . _DB_PREFIX_ . "ecm_smssender` WHERE `id_lang`=".$id_lang);
        $shop_name = Configuration::get('PS_SHOP_NAME');    
        return
        str_replace(array("{customer}","{shop_name}","{pwd}"),
            array($customer,$shop_name,$pwd),
            $message);
    }


}
