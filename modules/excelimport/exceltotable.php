<?php
// Отвечаем только на Ajax
//if ($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') {return;}


include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');

//ini_set('display_errors', 1);
//error_reporting(E_ALL ^ E_NOTICE);

function remove_tags ($str, $allowed_tags = null, $remove_id = false, $remove_class = false, $remove_style = false, $remove_other = false)
  {
   if ($remove_id) $str = preg_replace('/id=(["\'])[^\1]*?\1/i', '', $str, -1);
   if ($remove_class) $str = preg_replace('/class=(["\'])[^\1]*?\1/i', '', $str, -1);
   if ($remove_style) $str = preg_replace('/style=(["\'])[^\1]*?\1/i', '', $str, -1);
   if ($remove_other) 
    {
      $str = preg_replace('/onload=(["\'])[^\1]*?\1/i', '', $str, -1);
      $str = preg_replace('/onclick=(["\'])[^\1]*?\1/i', '', $str, -1);
      $str = preg_replace('/onmouseover=(["\'])[^\1]*?\1/i', '', $str, -1);
      $str = preg_replace('/title=(["\'])[^\1]*?\1/i', '', $str, -1);
      $str = preg_replace('/alt=(["\'])[^\1]*?\1/i', '', $str, -1);
      $str = preg_replace('/dir=(["\'])[^\1]*?\1/i', '', $str, -1);
    }
   $str = preg_replace ("_x000D_ ",'',$str);
   $str = preg_replace ("/__ /",'',$str);
   $str = strip_tags($str, $allowed_tags);
   return $str;
  }


// Получаем от клиента номер итерации
//$url = 'upload/toprepare.xlsx';
$url = $_POST['url']; if (empty($url)) return;

if (isset($_POST['offset']) && $_POST['offset']) $offset=(int)$_POST['offset'];
else $offset = 0;

if (isset($_POST['id_lang']) && $_POST['id_lang']) $id_lang=(int)$_POST['id_lang'];
else $id_lang = 1;

if (isset($_POST['id_shop']) && $_POST['id_shop']) $id_shop=(int)$_POST['id_shop'];
else $id_shop = 1;
//$offset = 1;
$kolline = 1000;

if (isset($_POST['import_options']) && $_POST['import_options']) 
    {
    $import_option=$_POST['import_options'];
    }
  else
    {
    return;
    }        
//$import_option = 'newproduct';

 require_once "classes/simplexlsx.class.php";
 $xlsx = new SimpleXLSX( $url );

if ($offset == 0)
{
  $sql_drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'exceltable`';
  $sql_create = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."exceltable` (
      `id_excel` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `id_product` int(10),
      `id_lang` int(10) NOT NULL DEFAULT '1',
      `id_shop` int(10) NOT NULL DEFAULT '1',
      `active` tinyint(1) NOT NULL DEFAULT '1',
      `name` varchar(128),
      `categories` text,
      `price` decimal(20,6) NOT NULL DEFAULT '0.000000',
      `wholesale_price` decimal(20,6) NOT NULL DEFAULT '0.000000',
      `on_sale` tinyint(1)  NOT NULL DEFAULT '0',
      `reduction_s` decimal(20,6),
      `reduction_p` decimal(20,6),
      `reduction_from` datetime,
      `reduction_to` datetime,
      `reference` varchar(32),
      `supplier_reference` varchar(32),
      `supplier` varchar(128),
      `manufacturer` varchar(128),
      `ean13` varchar(13),
      `width` decimal(20,6) NOT NULL DEFAULT '0.000000',
      `height` decimal(20,6) NOT NULL DEFAULT '0.000000',
      `depth` decimal(20,6) NOT NULL DEFAULT '0.000000',
      `weight` decimal(20,6) NOT NULL DEFAULT '0.000000',
      `quantity` decimal(20,6) NOT NULL DEFAULT '0',
      `description_short` text,
      `description` text,
      `images` text,
      `images_del` tinyint(1) NOT NULL DEFAULT '0',
      `features` text,
      `online_only` tinyint(1) NOT NULL DEFAULT '0',
      `available_for_order` tinyint(1) NOT NULL DEFAULT '1',
      `available_now` varchar(255) DEFAULT NULL,
      `available_later` varchar(255) DEFAULT NULL,
      `meta_title` varchar(128) DEFAULT NULL,
      `meta_keywords` varchar(255) DEFAULT NULL,
      `meta_description` varchar(255) DEFAULT NULL,
      `link_rewrite` varchar(128) DEFAULT NULL,
      `cur` int(20) NOT NULL DEFAULT '1',
      `tovar` int(20) NOT NULL DEFAULT '1',
      `date_upd` datetime NOT NULL,
      `offset` int(10) NOT NULL DEFAULT '1',
      `checkin` int(10) NOT NULL DEFAULT '1',
      `name_ukr` varchar(128),
      `des_ukr` text,
      PRIMARY KEY (`id_excel`),
      KEY `id_product` (`id_product`),
      KEY `reference` (`reference`)      
    ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8";
  
  Db::getInstance()->execute($sql_drop);
  Db::getInstance()->execute($sql_create);
}
  if ($offset == 0) $iteration = 1;
  else $iteration = $offset*$kolline + 1; 
  $excelfile = $xlsx->rowslimit(1,$iteration,$kolline);
  //d($excelfile);
  $allrows = $excelfile[1];
  $excelfile = $excelfile[0];
  $cols = count ($excelfile);
  unset ($xlsx);
 // dump($excelfile);
 // die();
  $sql_insert = 'INSERT INTO `'._DB_PREFIX_.'exceltable`(`id_product`, `id_lang`, `id_shop`,`active`, `name`, `categories`, `price`, `wholesale_price`, `on_sale`, `reduction_s`, `reduction_p`, `reduction_from`, `reduction_to`, `reference`, `supplier_reference`, `supplier`, `manufacturer`, `ean13`, `width`, `height`, `depth`, `weight`, `quantity`, `description_short`, `description`, `images`, `images_del`, `features`, `online_only`, `available_for_order`, `available_now`, `available_later`, `meta_title`, `meta_keywords`, `meta_description`, `link_rewrite`, `tovar`, `cur`, `date_upd`,`name_ukr`,`des_ukr`) VALUES ';
  $a = array(0, 1, '-', '-', 0, 0, 0, 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '-', '-', '-', '-', '-', 0, 0, 0, 0, 0, '-', '-', '-', 0, '-', 0, 1, '-', '-', '-', '-', '-', '-', 1, 1,'-', '-',date('Y-m-d H:i:s'));
//  d($a);
  foreach ($excelfile as $key=>$row)
    {
    if ($key == 0 && $offset == 0) continue;
    
    $row = $row + $a;
    
    if ( Configuration::get('PS_EXCEL_CLHTML') == 1 )
                {
                $row[22] = remove_tags($row[22], '<a><img><p><br><br/><br /><b><strong>',true,true,true,true);
                $row[21] = remove_tags($row[21], '<a><img><p><br><br/><br /><b><strong>',true,true,true,true);
                }
  //  dump($row);
 //   die();
    if ($row[0]  == '') $row[0]  = 0;
    if ($row[1]  == '') $row[1]  = 0;
    if ($row[2]  == '') $row[2]  = '-';
    if ($row[3]  == '') $row[3]  = 0;
    if ($row[4]  == '') $row[4]  = 0;
    if ($row[5]  == '') $row[5]  = 0;
    if ($row[6]  == '') $row[6]  = 0;
    if ($row[7]  == '') $row[7]  = 0;
    else $row[7] = $row[7]*1;
    if ($row[8]  == '') $row[8]  = 0;
    else $row[8] = $row[8]*1;
    if ($row[11] == '') $row[11] = '-';
    if ($row[12] == '') $row[12] = '-';
    if ($row[13] == '') $row[13] = '-';
    if ($row[14] == '') $row[14] = '-';
    if ($row[15] == '') $row[15] = '-';
    if ($row[16] == '') $row[16] = 0;
    if ($row[17] == '') $row[17] = 0;
    if ($row[18] == '') $row[18] = 0;
    if ($row[19] == '') $row[19] = 0;
    if ($row[20] == '') $row[20] = 0;
    if ($row[21] == '') $row[21] = '-';
    if ($row[22] == '') $row[22] = '-';
    if ($row[23] == '') $row[23] = '-';
    if ($row[24] == '') $row[24] = 0;
    if ($row[25] == '') $row[25] = '-';
    if ($row[26] == '') $row[26] = 0;
    if ($row[27] === '') $row[27] = 1;
    if ($row[28] == '') $row[28] = '-';
    if ($row[29] == '') $row[29] = '-';
    if ($row[30] == '') $row[30] = '-';
    if ($row[31] == '') $row[31] = '-';
    if ($row[32] == '') $row[32] = '-';
    if ($row[33] == '') $row[33] = '-';
    if ($row[34] === '') $row[34] = 1;
    if ($row[35] === '') $row[35] = 1;
    if ($row[36] == '') $row[36] = '-';
    if ($row[37] == '') $row[37] = '-';
    
    if ($row[0]  == 0 && $row[2]  == '-' && $row[11]  == '-' && $row[15]  == '-') continue;
    
    $row[2]  = preg_replace ("/'/",'`',$row[2]);
    $row[3]  = str_replace ('.',',',(string)$row[3]);
    $row[11] = preg_replace ("/'/",'`',$row[11]);
    $row[12] = preg_replace ("/'/",'`',$row[12]);
    $row[13] = preg_replace ("/'/",'`',$row[13]);
    $row[14] = preg_replace ("/'/",'`',$row[14]);
    $row[15] = preg_replace ("/'/",'`',$row[15]);
    $row[21] = preg_replace ("/'/",'`',$row[21]);
    $row[22] = preg_replace ("/'/",'`',$row[22]);
    $row[23] = preg_replace ("/'/",'`',$row[23]);
    $row[25] = preg_replace ("/'/",'`',$row[25]);
    $row[28] = preg_replace ("/'/",'`',$row[28]);
    $row[29] = preg_replace ("/'/",'`',$row[29]);
    $row[30] = preg_replace ("/'/",'`',$row[30]);
    $row[31] = preg_replace ("/'/",'`',$row[31]);                           
    $row[32] = preg_replace ("/'/",'`',$row[32]);
    $row[33] = preg_replace ("/'/",'`',$row[33]);
    $row[36] = preg_replace ("/'/",'`',$row[36]);
    $row[37] = preg_replace ("/'/",'`',$row[37]);
     
    $row[9] = gmdate("Y-m-d H:i:s", ($row[9] - 25569) * 86400);
    $row[10] = gmdate("Y-m-d H:i:s", ($row[10] - 25569) * 86400);
    if (!$row[9] || $row[9] == '1899-12-30 00:00:00') $row[9] = '0000-00-00 00:00:00';
    if (!$row[10] || $row[10] == '1899-12-30 00:00:00') $row[10] = '0000-00-00 00:00:00';

    $sql_insert .= "($row[0],$id_lang,$id_shop,$row[1],'$row[2]','$row[3]',$row[4],$row[5],$row[6],$row[7],$row[8],'$row[9]','$row[10]','$row[11]','$row[12]','$row[13]','$row[14]','$row[15]',$row[16],$row[17],$row[18],$row[19],$row[20],'$row[21]','$row[22]','$row[23]',$row[24],'$row[25]',$row[26],$row[27],'$row[28]','$row[29]','$row[30]','$row[31]','$row[32]','$row[33]',$row[34],$row[35],'$row[38]','$row[36]','$row[37]'),";
  //  dump($sql_insert);
  //  die();
    }
  $sql_insert = substr($sql_insert, 0, -1);
  
  unset ($excelfile);
  unset ($row);
  Db::getInstance()->execute($sql_insert);
  unset ($sql_insert);
    
  $offset++;
    
if ($iteration == 1) $iteration=$kolline;
if ($cols < $kolline) 
  { 
    //updatepriceqfozzysales
  if ($import_option == 'updatepriceqfozzysales')
  {  
    $sql1 = "UPDATE `ps_exceltable` e, `ps_product` p SET p.`price` = e.`price`, p.`wholesale_price` = e.`wholesale_price`, p.`available_for_order` = 1, p.`show_price` = 1, p.`visibility` = 'both' WHERE p.`id_product` = e.`id_product` AND (e.`on_sale` = 0) AND (e.`price` > 0)";
    $sql1_1 = "UPDATE `ps_exceltable` e, `ps_product` p SET p.`price` = e.`price`, p.`wholesale_price` = e.`wholesale_price`, p.`available_for_order` = 0, p.`show_price` = 0, p.`visibility` = 'catalog' WHERE p.`id_product` = e.`id_product` AND (e.`on_sale` = 0) AND (e.`price` = 0)";
    $sql2 = "UPDATE `ps_exceltable` e, `ps_product_shop` p SET p.`price` = e.`price`, p.`wholesale_price` = e.`wholesale_price`, p.`available_for_order` = 1, p.`show_price` = 1, p.`visibility` = 'both' WHERE p.`id_product` = e.`id_product` AND (e.`on_sale` = 0) AND (e.`price` > 0)";
    $sql2_1 = "UPDATE `ps_exceltable` e, `ps_product_shop` p SET p.`price` = e.`price`, p.`wholesale_price` = e.`wholesale_price`, p.`available_for_order` = 0, p.`show_price` = 0, p.`visibility` = 'catalog' WHERE p.`id_product` = e.`id_product` AND (e.`on_sale` = 0) AND (e.`price` = 0)";
    $sql3 = "UPDATE `ps_exceltable` e, `ps_stock_available` s SET s.`quantity` = 10000 WHERE s.`id_product` = e.`id_product` AND (e.`quantity` > 0)";
    $sql4 = "UPDATE `ps_exceltable` e, `ps_stock_available` s SET s.`quantity` = 0 WHERE s.`id_product` = e.`id_product` AND (e.`quantity` = 0)";
    $sql5 = "DELETE FROM `ps_exceltable` WHERE `on_sale` = 0";
    $sql6 = "ALTER TABLE `ps_exceltable` MODIFY `id_excel` INT(11)"; 
    $sql7 = "ALTER TABLE `ps_exceltable` DROP PRIMARY KEY";
    $sql8 = "UPDATE `ps_exceltable` SET `id_excel`='0'"; 
    $sql9 = "ALTER TABLE `ps_exceltable` AUTO_INCREMENT=1"; 
    $sql10 = "ALTER TABLE `ps_exceltable` MODIFY `id_excel` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY";
    $sql11 = "UPDATE `ps_product` SET `on_sale` = 0";
    $sql12 = "UPDATE `ps_product_shop` SET `on_sale` = 0";
    
    $excel_percent = "ALTER TABLE `ps_exceltable` ADD `percent` FLOAT NOT NULL DEFAULT '1' AFTER `offset`";
    $excel_percent_rand = "UPDATE `ps_exceltable` SET `percent` = ROUND((RAND() * 40)+10)/100 + 1";
    
    $sql_sp_p1 = "UPDATE `ps_exceltable` e, `ps_product` p SET p.`price` = e.`price`*e.`percent`, p.`wholesale_price` = e.`wholesale_price`, p.`on_sale` = 1, p.`available_for_order` = 1, p.`show_price` = 1, p.`visibility` = 'both' WHERE p.`id_product` = e.`id_product` AND (e.`on_sale` = 1) AND (e.`price` > 0)";
    $sql_sp_p2 = "UPDATE `ps_exceltable` e, `ps_product` p SET p.`price` = e.`price`*e.`percent`, p.`wholesale_price` = e.`wholesale_price`, p.`on_sale` = 1, p.`available_for_order` = 0, p.`show_price` = 0, p.`visibility` = 'catalog' WHERE p.`id_product` = e.`id_product` AND (e.`on_sale` = 1) AND (e.`price` = 0)";
    $sql_sp_p3 = "UPDATE `ps_exceltable` e, `ps_product_shop` p SET p.`price` = e.`price`*e.`percent`, p.`wholesale_price` = e.`wholesale_price`, p.`on_sale` = 1, p.`available_for_order` = 1, p.`show_price` = 1, p.`visibility` = 'both' WHERE p.`id_product` = e.`id_product` AND (e.`on_sale` = 1) AND (e.`price` > 0)";
    $sql_sp_p4 = "UPDATE `ps_exceltable` e, `ps_product_shop` p SET p.`price` = e.`price`*e.`percent`, p.`wholesale_price` = e.`wholesale_price`, p.`on_sale` = 1, p.`available_for_order` = 0, p.`show_price` = 0, p.`visibility` = 'catalog' WHERE p.`id_product` = e.`id_product` AND (e.`on_sale` = 1) AND (e.`price` = 0)";

    
    $sql_sp_clear = "TRUNCATE TABLE `ps_specific_price`";
    $sql_sp_clear2 = "TRUNCATE TABLE `ps_specific_price_priority`";
    $sql_sp_add = "INSERT INTO `ps_specific_price` (`id_specific_price_rule`, `id_cart`, `id_product`, `id_shop`, `id_shop_group`, `id_currency`, `id_country`, `id_group`, `id_customer`, `id_product_attribute`, `price`, `from_quantity`, `reduction`, `reduction_tax`, `reduction_type`, `from`, `to`) 
    SELECT 0, 0, `id_product`, 1, 0, 0, 0, 0, 0, 0, -1, 1, `price`*`percent` - `price`, 1, 'amount', '0000-00-00 00:00:00', `reduction_to`
    FROM `ps_exceltable`
    WHERE `on_sale` = 1
    ";
    $sql_sp_add2 = "INSERT INTO `ps_specific_price_priority`(`id_product`, `priority`)
    SELECT `id_product`, 'id_shop;id_currency;id_country;id_group'
    FROM `ps_exceltable`
    WHERE `on_sale` = 1    
    ";
    
    $sql_sp_clear_e = "TRUNCATE TABLE `ps_exceltable`";
    
    Db::getInstance()->execute($sql1);
    Db::getInstance()->execute($sql1_1);
    Db::getInstance()->execute($sql2);
    Db::getInstance()->execute($sql2_1);
    Db::getInstance()->execute($sql3);
    Db::getInstance()->execute($sql4);
    Db::getInstance()->execute($sql5);
    Db::getInstance()->execute($sql6);
    Db::getInstance()->execute($sql7);
    Db::getInstance()->execute($sql8);
    Db::getInstance()->execute($sql9);
    Db::getInstance()->execute($sql10);
    Db::getInstance()->execute($sql11);
    Db::getInstance()->execute($sql12); 
    
    Db::getInstance()->execute($excel_percent);
    Db::getInstance()->execute($excel_percent_rand);
    Db::getInstance()->execute($sql_sp_p1);
    Db::getInstance()->execute($sql_sp_p2);
    Db::getInstance()->execute($sql_sp_p3);
    Db::getInstance()->execute($sql_sp_p4);
    Db::getInstance()->execute($sql_sp_clear);
    Db::getInstance()->execute($sql_sp_clear2);
    Db::getInstance()->execute($sql_sp_add);
    Db::getInstance()->execute($sql_sp_add2);
    Db::getInstance()->execute($sql_sp_clear_e);
    
    } //updatepriceqfozzysales
    $sucsess = 1;
  } 
else
  { 
    $sucsess = round($iteration/$allrows, 2);
  }
// И возвращаем клиенту данные (номер итерации и сообщение об окончании работы скрипта)
$output = Array('offset' => $offset, 'sucsess' => $sucsess);

echo json_encode($output);
