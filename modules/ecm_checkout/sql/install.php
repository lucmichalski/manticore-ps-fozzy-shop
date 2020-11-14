<?php
/**
* 2007-2018 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web', please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2018 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

$sql=$fieds = $keys = $change = array();

$table = $this->name;
$fieds[$table] = array (
    'id_cart' => 'int(10) UNSIGNED NOT NULL',
    'id_order' => 'int(10) UNSIGNED NOT NULL',
    'id_country' => 'int(10) UNSIGNED NOT NULL',
    'id_customer' => 'int(10) UNSIGNED NOT NULL',
    'id_address' => 'int(10) UNSIGNED NOT NULL',
    'id_address_temp' => 'int(10) UNSIGNED NOT NULL',
    'id_carrier' => 'int(10) UNSIGNED NOT NULL',
    'id_state' => 'int(10) UNSIGNED NOT NULL',
    'alias' => 'varchar(32) DEFAULT NULL',
    'email' => 'varchar(128) DEFAULT NULL',
    'company' => 'varchar(255) DEFAULT NULL',
    'lastname' => 'varchar(32) DEFAULT NULL',
    'firstname' => 'varchar(32) DEFAULT NULL',
    'middlename' => 'varchar(32) DEFAULT NULL',
    'address1' => 'varchar(128) DEFAULT NULL',
    'address2' => 'varchar(128) DEFAULT NULL',
    'postcode' => 'varchar(12) DEFAULT NULL',
    'city' => 'varchar(64) DEFAULT NULL',
    'other' => 'text',
    'phone' => 'varchar(32) DEFAULT NULL',
    'phone_mobile' => 'varchar(32) DEFAULT NULL',
    'vat_number' => 'varchar(32) DEFAULT NULL',
    'dni' => 'varchar(16) DEFAULT NULL',
    'callme' => ' tinyint(1) DEFAULT NULL',
    'newsletter' => 'tinyint(1) UNSIGNED NOT NULL DEFAULT 0',
    'optin' => 'tinyint(1) UNSIGNED NOT NULL DEFAULT 0',
    'payment' => 'varchar(32) DEFAULT NULL',
    'password' => 'varchar(32) DEFAULT NULL',
    'password2' => 'varchar(32) DEFAULT NULL',
    'street'  => 'text',
    'house'  => 'varchar(10) DEFAULT NULL',
    'apartment' =>  'varchar(10) DEFAULT NULL',
    'level'  => 'int(5) DEFAULT NULL',
    'door'  => 'int(5) DEFAULT NULL',
    'intercom'  => 'varchar(10) DEFAULT NULL',
    'elevator'  => 'tinyint(1) DEFAULT NULL',
    'concierge'  => 'tinyint(1) DEFAULT NULL',
    'zone'  => 'varchar(20) DEFAULT NULL',
    'zone_name'  => 'varchar(20) DEFAULT NULL',
    'lat'  => 'decimal(13,10) DEFAULT NULL',
    'lng'  => 'decimal(13,10) DEFAULT NULL',
    'valid_adr'  => 'tinyint(1) DEFAULT NULL',
    'is_dm'  => 'tinyint(1) DEFAULT NULL'
);
    
$keys[$table][] = array (
	'type' => 'UNIQUE',
	'name' => 'id_cart',
	'colnums' => '`id_cart`, `id_country`, `id_address`',
	);
$change[$table] = array (
	);
    

$table = $this->name.'_custom';
$fieds[$table] = array (
    'id_lang' => 'int(10) UNSIGNED NOT NULL',
    'ref' => 'VARCHAR(32) DEFAULT NULL',
    'type' => 'VARCHAR(32) DEFAULT NULL',
    'name' => 'VARCHAR(64) DEFAULT NULL',
    'icon' => 'VARCHAR(32) DEFAULT NULL',
    'description' => 'TEXT',
    'skip' => 'TINYINT(1) UNSIGNED NOT NULL DEFAULT 0',
    'sc_end' => 'TINYINT(1) UNSIGNED NOT NULL DEFAULT 0',
    'info' => 'TEXT',
    'from' => 'DECIMAL(20,6) NOT NULL  DEFAULT 0',
    'to' => 'DECIMAL(20,6) NOT NULL  DEFAULT 9999999',
);

$keys[$table][] = array (
	'type' => 'UNIQUE',
	'name' => 'id_cust',
	'colnums' => '`id_lang`, `ref`, `type`',
	);
$change[$table] = array (
	);
    

$sql[] ='ALTER TABLE `'._DB_PREFIX_.'customer` ADD `phone` VARCHAR( 32 )';
$sql[] ='ALTER TABLE `'._DB_PREFIX_.'customer` ADD `middlename` VARCHAR( 32 )';
$sql[] ='ALTER TABLE `'._DB_PREFIX_.'customer` ADD `pwd` VARCHAR( 32 )';
$sql[] ='ALTER TABLE `'._DB_PREFIX_.'address` ADD `middlename` VARCHAR( 32 )';
$sql[] ='ALTER TABLE `'._DB_PREFIX_.'address` ADD `sc_address` tinyint(1) UNSIGNED NOT NULL DEFAULT 0';


insert_row ($fieds);
//change_row ($fieds);
insert_key ($keys);
change_row ($change);

foreach ($sql as $s) {
    try {Db::getInstance()->Execute($s);} 
    catch (Exception $e) {}
}

$meta = new Meta();
$meta->page = "module-{$this->name}-{$this->controller_name}";
$meta->configurable = 1;
$meta->url_rewrite = 'simplecheckout';
try {$meta->add();} 
catch (Exception $e) {}
        
$meta = new Meta();
$meta->page = "module-{$this->name}-{$this->controller_name}_end";
$meta->configurable = 1;
$meta->url_rewrite = 'order-confirm';
try {$meta->add();} 
catch (Exception $e) {}

if ($cs = json_decode(Configuration::get($this->name.'_cs'))){
    foreach ($cs as $id_ref=>$c){
        Configuration::updateValue($this->name.'_cs_'.$id_ref, json_encode($c));
    }
    Configuration::deleteByName($this->name.'_cs');
}

       
function insert_row ($fieds){
	foreach ($fieds as $table=>$colnums){
		$first = true;
		foreach ($colnums as $name=>$def){
			if ($first){
				try {Db::getInstance()->Execute("CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."{$table}` (
					`{$name}` {$def}) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ");} 
				catch(Exception $a) {}
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

function insert_key ($keys) {
	foreach ($keys as $table=>$key){
		foreach ($key as $k){
			if ($k['type'] == 'PRIMARY'){
				try {Db::getInstance()->Execute("ALTER TABLE `"._DB_PREFIX_."{$table}` DROP PRIMARY KEY");} 
				catch(Exception $a) {}
				try {Db::getInstance()->Execute("ALTER TABLE `"._DB_PREFIX_."{$table}` ADD PRIMARY KEY ({$k['colnums']})");}
				catch(Exception $c) {}
			} else {
				try {Db::getInstance()->Execute("ALTER TABLE `"._DB_PREFIX_."{$table}` DROP INDEX `{$k['name']}`");} 
				catch(Exception $a) {}
				try {Db::getInstance()->Execute("ALTER TABLE `"._DB_PREFIX_."{$table}` ADD {$k['type']} `{$k['name']}` ({$k['colnums']})");} 
				catch(Exception $c) {}
			}
		}
	}
}	

function change_row ($fieds){
	foreach ($fieds as $table=>$colnums){
		foreach ($colnums as $name=>$def){
			try {Db::getInstance()->Execute("ALTER TABLE `"._DB_PREFIX_."{$table}` CHANGE `{$name}` `{$name}` {$def}");} 
			catch(Exception $c) {}
//			catch(Exception $c){dump($c->getMessage());}
			
		}
	}
}

