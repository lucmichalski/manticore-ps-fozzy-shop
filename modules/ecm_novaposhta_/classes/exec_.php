<?php
/* НоваПошта */
if (!defined('_NP_TIMEOUT_')) {define ('_NP_TIMEOUT_', 4);}
if (!defined('_VW_')) {define ('_VW_', 4000);}
if (!defined('__END__')) {define ('__END__', '__END__');}

class exec_ 
{
	
	 public static
	 function price($to_city, $weight=0.1, $pub_price=1, $servicetype="WarehouseWarehouse", $nal=0) {
        $cost = exec::price2($to_city, $weight, $pub_price, $servicetype, $nal);
        // if ($to_city == '8d5a980d-391c-11dd-90d9-001a92567626'){ //Киев
		//	$cost['Cost'] = $cost['CostOrg'] = 100;  // Своя доставка по Киеву 
		//	$cost['CostRedelivery'] = $pub_price * 0.02;  // Своя доставка по Киеву 
		//}
        
        return $cost;
	}

    public static function select($id_lang){
        $lang = Language::getIsoById($id_lang);
        switch ($lang) {
            case 'ru':
                $select = array(
                    'area' => 'Выберите вашу область',
                    'noarea' => 'Область не выбрана',
                    'city' => 'Выберите ваш город',
                    'nocity' => 'Город не выбран',
                    'district' => 'Выберите ваш район',
                    'nodistrict' => 'Район не выбран',
                    'address' => 'Адресная доставка',
                    'ware' => 'Выберите ваше отделение',
                    'noware' => 'Отделение не выбрано',
                    'prefix' => 'Ru',
                ); break;
            case 'uk':
                $select = array(
                    'area' => 'Виберіть вашу область',
                    'noarea' => 'Область не вибрана',
                    'city' => 'Виберіть ваше місто',
                    'nocity' => 'Місто не вибрано',
                    'district' => 'Виберіть ваш район',
                    'nodistrict' => 'Район не выбран',
                    'address' => 'Адресна доставка',
                    'ware' => 'Виберіть ваше відділення',
                    'noware' => 'Відділення не вибрано',
                    'prefix' => '',
                ); break;
            default:
                $select = array(
                    'area' => 'Select your area',
                    'noarea' => 'Area not select',
                    'city' => 'Select your city',
                    'nocity' => 'City not select',
                    'district' => 'Select your district',
                    'nodistrict' => 'District not select',
                    'address' => 'Address delivery',
                    'ware' => 'Select your warehouse',
                    'noware' => 'Warehouse not select',
                    'prefix' => '',
                ); break;
            }

        return $select;
    }

    public static function checkpackage()
    {
        //setlocale(LC_TIME, "uk_UA.UTF8");
        $deklaracia = Tools::getValue('deklaracia');
        $id_order = Tools::getValue('order');
        $delivery_date = date('d.m.Y', strtotime(Db::getInstance()->GetValue("SELECT `delivery_date` FROM `"._DB_PREFIX_."orders` WHERE `id_order` = '{$id_order}'")));
        $order = Db::getInstance()->GetRow("SELECT * FROM `"._DB_PREFIX_."ecm_newpost_orders` WHERE `id_order` = '{$id_order}'");
        $est_date = np::getDocumentDeliveryDate($delivery_date, $order['city']);
            //p($est_date);
            $tracking = np::tracking($deklaracia, $order['another_recipient']?$order['another_phone']:$order['phone']	);
            //p($tracking);
            $html = 'Невідома помилка';
        if (isset($tracking)) {
            $html = '';
            if (!empty($tracking->CitySender)) {
                $html .= ' з міста - '.(string) $tracking->CitySender;
            }
            if (!empty($tracking->CityRecipient)) {
                $html .= ', до міста - '.(string) $tracking->CityRecipient.'<br>';
            }
            if (!empty($tracking->AddressUA)) {
                $html .= ' Отримати в '.(string) $tracking->AddressUA.'<br>';
            }
            if (!empty($tracking->ReceiptDateTime)) {
                $html .= ' Дата отримання вантажу - '.strftime('%d %B %Y', strtotime((string) $tracking->ReceiptDateTime)).'<br>';
            }
            if (!empty($tracking->ScheduledDeliveryDate)) {
                $html .= ' Очикувана дата прибуття - '.strftime('%d %B %Y', strtotime((string) $tracking->ScheduledDeliveryDate)).'<br>';
            } elseif (isset($tracking)) {
                if (!empty($est_date->DeliveryDate->date)) {
                    $html .= ' Очикувана дата прибуття - '.strftime('%d %B %Y', strtotime((string) $est_date->DeliveryDate->date)).'<br>';
                }
            }
            if (!empty($tracking->RecipientFullName)) {
                $html .= ' Отримувач - '.(string) $tracking->RecipientFullName.'<br>';
            }
            if (!empty($tracking->DocumentCost)) {
                $html .= ' Вартість замовлення - '.(string) $tracking->DocumentCost.'<br>';
            }
            if (!empty($tracking->Sum)) {
                $html .= ' Вартість доставки - '.(string) $tracking->Sum.'<br>';
            }
            if (!empty($tracking->UndeliveryReasonsSubtypeDescription)) {
                $html .= ' Причина відмови - '.(string) $tracking->UndeliveryReasonsSubtypeDescription.'<br>';
            }
            if (!empty($tracking->Status)) {
                $html .= ' Стан відправлення - '.(string) $tracking->Status.'<br>';
            }
        }

        return $html;
    }

    public static function GetOrderDetails2($id_order)
    {
        $sql = '
			SELECT	o.`total_products_wt` insurance_od,
					o.`total_products_wt` cod_value_od,
					o.`total_paid_real`,
					o.`module`,
					o.`total_discounts_tax_incl` discounts_od
			FROM `'._DB_PREFIX_.'orders` o,
				`'._DB_PREFIX_.'order_carrier` oc,
				`'._DB_PREFIX_.'order_detail` od,
				`'._DB_PREFIX_."ecm_newpost_orders` onp
			WHERE o.`id_order` = '$id_order' AND oc.`id_order` = '$id_order'
			AND onp.`id_order` = '$id_order' AND od.`id_order` = '$id_order'
			";
        $order = Db::getInstance()->getRow($sql);
            //p($result);
            if (!empty($order)) {
                $sql = "
				SELECT
					COUNT(*) item_quantity,
					group_concat(DISTINCT concat(od.product_name,', Артикул: ', p.reference,' - ',od.product_quantity,' шт')) msg_od,
					round(sum(p.width * p.height * p.depth / 4000 * od.product_quantity),4) vweight_od,
					round(sum(p.weight * od.product_quantity),4) weight_od,
					round(max(p.width),4) width_od,
					round(max(p.depth),4) depth_od,
					round(sum(p.height * od.product_quantity),4) height_od
				FROM `"._DB_PREFIX_.'order_detail` od
				LEFT JOIN `'._DB_PREFIX_."product` p ON p.`id_product` = od.`product_id`
				WHERE od.`id_order` = '$id_order'
				";
                $order = array_merge($order, Db::getInstance()->getRow($sql));
                $order['msg_od'] = trim($order['msg_od'], " \t\n\r\0\x0B");
                //$order['weight_od'] = $order['weight_od'] == 0 ? Configuration::get('ecm_np_weght') : $order['weight_od'];
                $ord = new Order($id_order);
                $order['weight_od'] = $ord->getTotalWeight() == 0 ? Configuration::get('ecm_np_weght') : $ord->getTotalWeight();
                $order['vweight_od'] = $order['vweight_od'] == 0 ? Configuration::get('ecm_np_vweght') : $order['vweight_od'];
                //$order['insurance_od'] = Tools::ps_round((float)$order['insurance_od']);
                $order['cod_value_od'] = (float) $order['cod_value_od'] - (float) self::RealDiscount($id_order);
                if (!($order['module'] == 'ecm_cashnovaposta' or $order['module'] == 'ecm_cashnovaposhta')) {
                    $order['cod_value_od'] = $order['cod_value_od'] - $order['total_paid_real'];
                }

                return $order;
            } else {
                return false;
            }
    }

    public static 
	function area_Ru(){
        $area_ru = array(
                '71508128-9b87-11de-822f-000c2965ae0e' => array('id' => '337', 'name' => 'Автономная Республика Крым'),
                '71508129-9b87-11de-822f-000c2965ae0e' => array('id' => '313', 'name' => 'Винницкая'),
                '7150812a-9b87-11de-822f-000c2965ae0e' => array('id' => '314', 'name' => 'Волынская'),
                '7150812b-9b87-11de-822f-000c2965ae0e' => array('id' => '315', 'name' => 'Днепропетровская'),
                '7150812c-9b87-11de-822f-000c2965ae0e' => array('id' => '316', 'name' => 'Донецкая'),
                '7150812d-9b87-11de-822f-000c2965ae0e' => array('id' => '317', 'name' => 'Житомирская'),
                '7150812e-9b87-11de-822f-000c2965ae0e' => array('id' => '318', 'name' => 'Закарпатская'),
                '7150812f-9b87-11de-822f-000c2965ae0e' => array('id' => '319', 'name' => 'Запорожская'),
                '71508130-9b87-11de-822f-000c2965ae0e' => array('id' => '320', 'name' => 'Ивано-Франковская'),
                '71508131-9b87-11de-822f-000c2965ae0e' => array('id' => '321', 'name' => 'Киевская'),
                '71508132-9b87-11de-822f-000c2965ae0e' => array('id' => '322', 'name' => 'Кировоградская'),
                '71508133-9b87-11de-822f-000c2965ae0e' => array('id' => '323', 'name' => 'Луганская'),
                '71508134-9b87-11de-822f-000c2965ae0e' => array('id' => '324', 'name' => 'Львовская'),
                '71508135-9b87-11de-822f-000c2965ae0e' => array('id' => '325', 'name' => 'Николаевская'),
                '71508136-9b87-11de-822f-000c2965ae0e' => array('id' => '326', 'name' => 'Одесская'),
                '71508137-9b87-11de-822f-000c2965ae0e' => array('id' => '327', 'name' => 'Полтавская'),
                '71508138-9b87-11de-822f-000c2965ae0e' => array('id' => '328', 'name' => 'Ровненская'),
                '71508139-9b87-11de-822f-000c2965ae0e' => array('id' => '329', 'name' => 'Сумская'),
                '7150813a-9b87-11de-822f-000c2965ae0e' => array('id' => '330', 'name' => 'Тернопольская'),
                '7150813b-9b87-11de-822f-000c2965ae0e' => array('id' => '331', 'name' => 'Харьковская'),
                '7150813c-9b87-11de-822f-000c2965ae0e' => array('id' => '332', 'name' => 'Херсонская'),
                '7150813d-9b87-11de-822f-000c2965ae0e' => array('id' => '333', 'name' => 'Хмельницкая'),
                '7150813e-9b87-11de-822f-000c2965ae0e' => array('id' => '334', 'name' => 'Черкасская'),
                '7150813f-9b87-11de-822f-000c2965ae0e' => array('id' => '336', 'name' => 'Черновицкая'),
                '71508140-9b87-11de-822f-000c2965ae0e' => array('id' => '335', 'name' => 'Черниговская'),
            );
		if (_PS_VERSION_ >= '1.7'){
			$area_ru['71508128-9b87-11de-822f-000c2965ae0e']['id'] = '349';
			$area_ru['71508129-9b87-11de-822f-000c2965ae0e']['id'] = '325';
			$area_ru['7150812a-9b87-11de-822f-000c2965ae0e']['id'] = '326';
			$area_ru['7150812b-9b87-11de-822f-000c2965ae0e']['id'] = '327';
			$area_ru['7150812c-9b87-11de-822f-000c2965ae0e']['id'] = '328';
			$area_ru['7150812d-9b87-11de-822f-000c2965ae0e']['id'] = '329';
			$area_ru['7150812e-9b87-11de-822f-000c2965ae0e']['id'] = '330';
			$area_ru['7150812f-9b87-11de-822f-000c2965ae0e']['id'] = '331';
			$area_ru['71508130-9b87-11de-822f-000c2965ae0e']['id'] = '332';
			$area_ru['71508131-9b87-11de-822f-000c2965ae0e']['id'] = '333';
			$area_ru['71508132-9b87-11de-822f-000c2965ae0e']['id'] = '334';
			$area_ru['71508133-9b87-11de-822f-000c2965ae0e']['id'] = '335';
			$area_ru['71508134-9b87-11de-822f-000c2965ae0e']['id'] = '336';
			$area_ru['71508135-9b87-11de-822f-000c2965ae0e']['id'] = '337';
			$area_ru['71508136-9b87-11de-822f-000c2965ae0e']['id'] = '338';
			$area_ru['71508137-9b87-11de-822f-000c2965ae0e']['id'] = '339';
			$area_ru['71508138-9b87-11de-822f-000c2965ae0e']['id'] = '340';
			$area_ru['71508139-9b87-11de-822f-000c2965ae0e']['id'] = '341';
			$area_ru['7150813a-9b87-11de-822f-000c2965ae0e']['id'] = '342';
			$area_ru['7150813b-9b87-11de-822f-000c2965ae0e']['id'] = '343';
			$area_ru['7150813c-9b87-11de-822f-000c2965ae0e']['id'] = '344';
			$area_ru['7150813d-9b87-11de-822f-000c2965ae0e']['id'] = '345';
			$area_ru['7150813e-9b87-11de-822f-000c2965ae0e']['id'] = '346';
			$area_ru['7150813f-9b87-11de-822f-000c2965ae0e']['id'] = '348';
			$area_ru['71508140-9b87-11de-822f-000c2965ae0e']['id'] = '347';
		}		
        return $area_ru;
    }
	
	public static 
	function captal_list(){
		$captal_list = array(
			'', // Крым
			'db5c88de-391c-11dd-90d9-001a92567626', // Винницкая
			'db5c893b-391c-11dd-90d9-001a92567626', // Волынская
			'db5c88f0-391c-11dd-90d9-001a92567626', // Днепропетровская
			'', // Донецкая
			'db5c88c4-391c-11dd-90d9-001a92567626', // Житомирская
			'e221d627-391c-11dd-90d9-001a92567626', // Закарпатская
			'db5c88c6-391c-11dd-90d9-001a92567626', // Запорожская
			'db5c8904-391c-11dd-90d9-001a92567626', // Ивано-Франковская
			'8d5a980d-391c-11dd-90d9-001a92567626', // Киевская
			'db5c891b-391c-11dd-90d9-001a92567626', // Кировоградская
			'', // Луганская
			'db5c88f5-391c-11dd-90d9-001a92567626', // Львовская
			'db5c888c-391c-11dd-90d9-001a92567626', // Николаевская
			'db5c88d0-391c-11dd-90d9-001a92567626', // Одесская
			'db5c8892-391c-11dd-90d9-001a92567626', // Полтавская
			'db5c896a-391c-11dd-90d9-001a92567626', // Ровненская
			'db5c88e5-391c-11dd-90d9-001a92567626', // Сумская
			'db5c8900-391c-11dd-90d9-001a92567626', // Тернопольская
			'db5c88e0-391c-11dd-90d9-001a92567626', // Харьковская
			'db5c88cc-391c-11dd-90d9-001a92567626', // Херсонская
			'db5c88ac-391c-11dd-90d9-001a92567626', // Хмельницкая
			'db5c8902-391c-11dd-90d9-001a92567626', // Черкасская
			'e221d642-391c-11dd-90d9-001a92567626', // Черновицкая
			'db5c897c-391c-11dd-90d9-001a92567626', // Черниговская
		);
		return $captal_list;
	}

	public static 
	function is_capital($ref){
		$captal_list = self::captal_list();
		return in_array($ref, $captal_list)?(int)Configuration::get('ecm_np_capital_top'):0;
	}
	
	public static
	function set_capital(){
		$sql = "UPDATE  `"._DB_PREFIX_."ecm_newpost_warehouse` 
			SET `is_capital` = '".(int)Configuration::get('ecm_np_capital_top')."' 
			WHERE `city_ref` IN ( '".implode( "','" , self::captal_list() )."')";
		return Db::getInstance()->Execute($sql);
	}
}
