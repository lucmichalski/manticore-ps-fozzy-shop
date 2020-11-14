<?php
/**
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * We are experts and professionals in PrestaShop
 *
 * @author    Elcommerce <support@elcommece.com.ua>
 * @copyright 2010-2018 Elcommerce
 * @license   Closed
 * @category  PrestaShop
 * @category  Module
 */


$sql = $fieds = $keys = $change = array();



$table = 'ecm_newpost_last';
$fieds[$table] = array (
    'id_customer' => 'int(11) NOT NULL',
    'id_cart' => 'int(11) NOT NULL',
    'nal' => 'tinyint(1) NOT NULL',
    'customsize' => 'tinyint(1) NOT NULL',
    'area' => 'varchar(36) NOT NULL',
    'city' => 'varchar(36) NOT NULL',
    'ref' => 'varchar(36) NOT NULL',
    'counterparty' => 'varchar(36) NOT NULL',
    'contact' => 'varchar(36) NOT NULL',
    'firstname' => 'varchar(36) NOT NULL',
    'lastname' => 'varchar(36) NOT NULL',
    'middlename' => 'varchar(36) NOT NULL',
    'phone' => 'varchar(36) NOT NULL',
    'StreetRef' => 'varchar(36) NOT NULL',
    'StreetName' => 'varchar(36) NOT NULL',
    'StreetType' => 'varchar(36) NOT NULL',
    'BuildingNumber' => 'varchar(36) NOT NULL',
    'Flat' => 'varchar(36) NOT NULL',
    'AddressRef' => 'varchar(36) NOT NULL',
    'another_middlename' => 'varchar(36) NOT NULL',
    'edrpou' => 'VARCHAR(12) NOT NULL',
);

$keys[$table][] = array (
	'type' => 'PRIMARY',
	'name' => 'id_customer',
	'colnums' => '`id_customer`',
	);
$change[$table] = array (
	);
    

$table = 'ecm_newpost_orders';
$fieds[$table] = array (
    'id_order' => 'int(10) NOT NULL',
    'id_customer' => 'int(11) NOT NULL',
    'senderpay' => 'tinyint(1) NOT NULL DEFAULT 0',
    'nal' => 'tinyint(1) NOT NULL DEFAULT 0',
    'senderpaynal' => 'tinyint(4) NOT NULL',
    'customsize' => 'tinyint(1) NOT NULL DEFAULT 0',
    'area' => 'varchar(36) NOT NULL',
    'city' => 'varchar(36) NOT NULL',
    'ware' => 'varchar(36) NOT NULL',
    'ref' => 'varchar(36) DEFAULT NULL',
    'en' => 'varchar(36) DEFAULT NULL',
    'counterparty' => 'varchar(36) NOT NULL',
    'contact' => 'varchar(36) NOT NULL',
    'width' => 'decimal(10,4) NOT NULL',
    'height' => 'decimal(10,4) NOT NULL',
    'depth' => 'decimal(10,4) NOT NULL',
    'weight' => 'decimal(10,4) NOT NULL',
    'vweight' => 'decimal(10,4) NOT NULL',
    'cost' => 'decimal(10,4) NOT NULL',
    'cost_pr' => 'decimal(10,4) NOT NULL',
    'costredelivery' => 'decimal(10,4) NOT NULL',
    'costpack' => 'decimal(10,4) NOT NULL',
    'insurance' => 'decimal(10,4) NOT NULL',
    'cod_value' => 'decimal(10,4) NOT NULL',
    'description' => 'varchar(100) NOT NULL',
    'pack' => 'varchar(100) NOT NULL',
    'seats_amount' => 'int(11) NOT NULL',
    'PackingNumber' => 'bigint(20) NOT NULL',
    'msg' => 'text NOT NULL',
    'firstname' => 'varchar(36) NOT NULL',
    'lastname' => 'varchar(36) NOT NULL',
    'middlename' => 'varchar(36) NOT NULL',
    'phone' => 'varchar(36) NOT NULL',
    'x' => 'double(16,10) NOT NULL',
    'y' => 'double(16,10) NOT NULL',
    'AddressRef' => 'varchar(36) NOT NULL',
    'StreetRef' => 'varchar(36) NOT NULL',
    'StreetName' => 'varchar(36) NOT NULL',
    'BuildingNumber' => 'varchar(36) NOT NULL',
    'Flat' => 'varchar(36) NOT NULL',
    'another_recipient' => 'TINYINT NULL DEFAULT NULL',
    'another_firstname' => 'VARCHAR( 36 ) NOT NULL',
    'another_lastname' => 'VARCHAR( 36 ) NOT NULL',
    'another_middlename' => 'VARCHAR( 36 ) NOT NULL',
    'another_phone' => 'VARCHAR( 36 ) NOT NULL',
    'RedBoxBarcode' => 'VARCHAR( 36 ) NOT NULL',
    'InfoRegClientBarcodes' => 'VARCHAR( 36 ) NOT NULL',
    'edrpou' => 'VARCHAR(12) NOT NULL',
);

$keys[$table][] = array (
	'type' => 'PRIMARY',
	'name' => 'id_order',
	'colnums' => '`id_order`',
	);
$change[$table] = array (
    'PackingNumber' => 'bigint(20) NOT NULL',
	);
    


$table = 'ecm_newpost_cart';
$fieds[$table] = array (
    'id_cart' => 'int(11) NOT NULL',
    'id_customer' => 'int(11) NOT NULL',
    'area' => 'varchar(36) NOT NULL',
    'city' => 'varchar(36) NOT NULL',
    'ref' => 'varchar(36) NOT NULL',
    'customsize' => 'tinyint(1) NOT NULL DEFAULT 0',
    'nal' => 'tinyint(1) NOT NULL DEFAULT 0',
    'cost' => 'decimal(10,4) NOT NULL DEFAULT 25',
    'cost_pr' => 'decimal(10,4) NOT NULL DEFAULT 25',
    'costredelivery' => 'decimal(10,4) NOT NULL',
    'costpack' => 'decimal(10,4) NOT NULL',
    'width' => 'decimal(10,4) NOT NULL',
    'height' => 'decimal(10,4) NOT NULL',
    'depth' => 'decimal(10,4) NOT NULL',
    'weight' => 'decimal(10,4) NOT NULL',
    'vweight' => 'decimal(10,4) NOT NULL',
    'full_address' => 'varchar(256) NOT NULL',
    'counterparty' => 'varchar(36) NOT NULL',
    'firstname' => 'varchar(36) NOT NULL',
    'lastname' => 'varchar(36) NOT NULL',
    'middlename' => 'varchar(36) NOT NULL',
    'phone' => 'varchar(36) NOT NULL',
    'total_wt' => 'double(10,4) NOT NULL',
    'AddressRef' => 'varchar(36) NOT NULL',
    'StreetRef' => 'varchar(36) NOT NULL',
    'StreetName' => 'varchar(36) NOT NULL',
    'BuildingNumber' => 'varchar(36) NOT NULL',
    'Flat' => 'varchar(36) NOT NULL',
    'another_recipient' => 'TINYINT NULL DEFAULT NULL',
    'another_firstname' => 'VARCHAR( 36 ) NOT NULL',
    'another_lastname' => 'VARCHAR( 36 ) NOT NULL',
    'another_middlename' => 'VARCHAR( 36 ) NOT NULL',
    'another_phone' => 'VARCHAR( 36 ) NOT NULL',
    'edrpou' => 'VARCHAR(12) NOT NULL',
    'counterparty' => 'varchar(36) NOT NULL',
    'contact' => 'varchar(36) NOT NULL',
);

$keys[$table][] = array (
	'type' => 'PRIMARY',
	'name' => 'id_cart',
	'colnums' => '`id_cart`',
	);
$change[$table] = array (
	);
    
$table = 'ecm_newpost_warehouse';
$fieds[$table] = array (
    'area_ref' => 'varchar(36) NOT NULL',
    'area' => 'varchar(256) COLLATE utf8_unicode_ci NOT NULL',
    'areaRu' => 'varchar(256) COLLATE utf8_unicode_ci NOT NULL',
    'area_id' => 'int(11) NOT NULL',
    'city_ref' => 'varchar(36) NOT NULL',
    'city_id' => 'int(11) NOT NULL',
    'city' => 'varchar(255) COLLATE utf8_unicode_ci NOT NULL',
    'cityRu' => 'varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL',
    'is_capital' => 'TINYINT NOT NULL DEFAULT 0',
    'ref' => 'varchar(255) NOT NULL',
    'address' => 'varchar(255) NOT NULL',
    'addressRu' => 'varchar(255) DEFAULT NULL',
    'number' => 'int(10) unsigned NOT NULL',
    'wareId' => 'int(10) unsigned NOT NULL',
    'phone' => 'varchar(255) DEFAULT NULL',
    'weekday_work_hours' => 'varchar(255) DEFAULT NULL',
    'weekday_reseiving_hours' => 'varchar(255) DEFAULT NULL',
    'weekday_delivery_hours' => 'varchar(255) DEFAULT NULL',
    'saturday_work_hours' => 'varchar(255) DEFAULT NULL',
    'saturday_reseiving_hours' => 'varchar(255) DEFAULT NULL',
    'saturday_delivery_hours' => 'varchar(255) DEFAULT NULL',
    'working_monday' => 'varchar(255) DEFAULT NULL',
    'working_tuesday' => 'varchar(255) DEFAULT NULL',
    'working_wednesday' => 'varchar(255) DEFAULT NULL',
    'working_thursday' => 'varchar(255) DEFAULT NULL',
    'working_friday' => 'varchar(255) DEFAULT NULL',
    'working_saturday' => 'varchar(255) DEFAULT NULL',
    'working_sunday' => 'varchar(255) DEFAULT NULL',
    'departure_monday' => 'varchar(255) DEFAULT NULL',
    'departure_tuesday' => 'varchar(255) DEFAULT NULL',
    'departure_wednesday' => 'varchar(255) DEFAULT NULL',
    'departure_thursday' => 'varchar(255) DEFAULT NULL',
    'departure_friday' => 'varchar(255) DEFAULT NULL',
    'departure_saturday' => 'varchar(255) DEFAULT NULL',
    'departure_sunday' => 'varchar(255) DEFAULT NULL',
    'receipt_monday' => 'varchar(255) DEFAULT NULL',
    'receipt_tuesday' => 'varchar(255) DEFAULT NULL',
    'receipt_wednesday' => 'varchar(255) DEFAULT NULL',
    'receipt_thursday' => 'varchar(255) DEFAULT NULL',
    'receipt_friday' => 'varchar(255) DEFAULT NULL',
    'receipt_saturday' => 'varchar(255) DEFAULT NULL',
    'receipt_sunday' => 'varchar(255) DEFAULT NULL',
    'max_weight_allowed' => 'int(11) DEFAULT NULL',
    'place_weight_allowed' => 'int(11) DEFAULT NULL',
    'x' => 'double(16,10) DEFAULT NULL',
    'y' => 'double(16,10) DEFAULT NULL',
    'TypeOfWarehouse' => 'varchar(36) NOT NULL',
);

$keys[$table][] = array (
	'type' => 'PRIMARY',
	'name' => '',
	'colnums' => '`ref`',
	);
$keys[$table][] = array (
	'type' => 'UNIQUE',
	'name' => 'area_ref',
	'colnums' => '`area_ref`,`city_ref`,`ref`',
	);
$change[$table] = array (
    'area' => 'VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL',
    'areaRU' => 'VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL',
    'city' => 'VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL',
    'cityRU' => 'VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL',
	);
    


insert_row ($fieds);
//change_row ($fieds);
insert_key ($keys);
change_row ($change);

$sql[] ='ALTER TABLE `'._DB_PREFIX_.'customer` ADD `phone` VARCHAR( 32 )';
$sql[] ='ALTER TABLE `'._DB_PREFIX_.'customer` ADD `middlename` VARCHAR( 32 )';
$sql[] ='ALTER TABLE `'._DB_PREFIX_.'customer` ADD `pwd` VARCHAR( 32 )';
$sql[] ='ALTER TABLE `'._DB_PREFIX_.'address` ADD `middlename` VARCHAR( 32 )';
$sql[] ='ALTER TABLE `'._DB_PREFIX_.'address` ADD `sc_address` tinyint(1) UNSIGNED NOT NULL DEFAULT 0';
$sql[] ='ALTER TABLE `'._DB_PREFIX_.'cart_product` ADD `special` tinyint(1) UNSIGNED NOT NULL DEFAULT 0';
$sql[] ='ALTER TABLE `'._DB_PREFIX_.'order_detail` ADD `special` tinyint(1) UNSIGNED NOT NULL DEFAULT 0';

if (Module::isInstalled('ecm_csync')){
    $sql[]='ALTER TABLE `'._DB_PREFIX_.'orders`  ADD `other_customer` TINYINT NULL DEFAULT 0 ';
    $sql[]='ALTER TABLE `'._DB_PREFIX_.'orders`  ADD `other_customer_name` VARCHAR( 36 ) NULL  ';
    $sql[]='ALTER TABLE `'._DB_PREFIX_.'orders`  ADD `other_customer_phone` VARCHAR( 36 ) NULL  ';
}

foreach ($sql as $s) {
    try {Db::getInstance()->Execute($s);} 
    catch (Exception $c) {}
}

$status_map_0 = array();
$status_map_0['0'] = '0';
$status_map = json_decode(Configuration::get('ecm_np_status_map'), true);
if (is_array($status_map)){
	$status_map['0'] = '0';
	Configuration::updateValue('ecm_np_status_map', json_encode($status_map));
} else{
	Configuration::updateValue('ecm_np_status_map', json_encode($status_map_0));
}

$confs =[
	'np_TrimMsg' => 100,
	'np_comiso' =>  20,
	'np_percentage' => 2,
	'np_insurance' => 0,
	'np_CargoType' => 'Cargo',
	'np_time' => '20:00',
	'np_weght' => 0.1,
	'np_vweght' => 0.1,
	'np_FreeLimit' => 99999,
	'np_FreeLimitAddr' => 99999,
	'np_FreeLimitMaxWeight' => 1000,
	'np_FreeLimitMaxWeightAddr' => 1000,
	'np_description' =>'Товар',
	'np_TotalMaxWeightAllowed' => 1000000,
	'np_PlaceMaxWeightAllowed' => 1000,
	'np_staff_users', json_encode(array(0,1)),
	'np_privileged_group', json_encode(array()),
	'np_privileged_ware', json_encode(array()),
	'np_another_alias' => 'np_another',
	'np_fixcost' => 50,
	'np_fixcost_address' => 50,
];

foreach ($confs as $name=>$value){
	if(!Configuration::hasKey(self::PREFIX . $name)) {
		Configuration::updateValue(self::PREFIX . $name, $value);
	}
}
		
       
function insert_row ($fieds)
{
	foreach ($fieds as $table=>$colnums){
		$first = true;
		foreach ($colnums as $name=>$def){
			if ($first){
				try {Db::getInstance()->Execute("CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."{$table}` (
					`{$name}` {$def}) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");} 
				catch(Exception $c) {}
				$first = false;
			}
			else{
				try {Db::getInstance()->Execute("ALTER TABLE `"._DB_PREFIX_."{$table}` ADD `{$name}` {$def}");
				} catch(Exception $b) {
					try {Db::getInstance()->Execute("ALTER TABLE `"._DB_PREFIX_."{$table}` CHANGE `{$name}` `{$name}` {$def}");} 
					catch(Exception $c) {}
				}
			}
		}
	}
}

function insert_key ($keys)
{
	foreach ($keys as $table=>$key){
		foreach ($key as $k){
			if ($k['type'] == 'PRIMARY'){
				try {Db::getInstance()->Execute("ALTER TABLE `"._DB_PREFIX_."{$table}` DROP PRIMARY KEY");} 
				catch(Exception $c) {}
				try {Db::getInstance()->Execute("ALTER TABLE `"._DB_PREFIX_."{$table}` ADD PRIMARY KEY ({$k['colnums']})");}
				catch(Exception $c) {}
			} else {
				try {Db::getInstance()->Execute("ALTER TABLE `"._DB_PREFIX_."{$table}` DROP INDEX `{$k['name']}`");} 
				catch(Exception $c) {}
				try {Db::getInstance()->Execute("ALTER TABLE `"._DB_PREFIX_."{$table}` ADD {$k['type']} `{$k['name']}` ({$k['colnums']})");} 
				catch(Exception $c) {}
			}
		}
	}
}	

function change_row ($fieds)
{
	foreach ($fieds as $table=>$colnums){
		foreach ($colnums as $name=>$def){
			try {Db::getInstance()->Execute("ALTER TABLE `"._DB_PREFIX_."{$table}` CHANGE `{$name}` `{$name}` {$def}");} 
			catch(Exception $c) { /* dump($c->getMessage()); */ }
		}
	}
}

