<?php
//НП
class npmail extends exec_ {	
	
	//После создания ЕН-ки
	public static
	function sendtracking($id_order, $en=''){
		//return; //полное отключение
		if ($en=='') { // выполняется при удалении ЕН
			self::TrackingNumberUpdate($id_order);
			return;
		}
		$order = new Order ($id_order);
		$customer = new Customer((int)$order->id_customer);
		$carrier = new Carrier((int)$order->id_carrier, $order->id_lang);
		if (!Validate::isLoadedObject($customer)) {
			throw new PrestaShopException('Can\'t load Customer object');
		}
		if (!Validate::isLoadedObject($carrier)) {
			throw new PrestaShopException('Can\'t load Carrier object');
		}
		$templateVars = array(
			'{followup}' => str_replace('@', $en, $carrier->url),
			'{firstname}' => $customer->firstname,
			'{lastname}' => $customer->lastname,
			'{id_order}' => $order->id,
			'{shipping_number}' => $en,
			'{order_name}' => $order->getUniqReference()
		);
		
		self::TrackingNumberUpdate($id_order);
		if(Configuration::get('ecm_np_SendNPmail')) {
			npmail::sendnpmail($id_order);
			@Mail::Send((int)$order->id_lang, 'in_transit', Mail::l('Package in transit', (int)$order->id_lang), $templateVars,	$customer->email, $customer->firstname.' '.$customer->lastname, null, null, null, null,	_PS_MAIL_DIR_, true, (int)$order->id_shop);
		}
	}
	
	public static
	function TrackingNumberUpdate($id_order){
		$order = new Order ($id_order);
		$customer = new Customer((int)$order->id_customer);
		$carrier = new Carrier((int)$order->id_carrier, $order->id_lang);
		$arr = array('order' => $order, 'customer' => $customer, 'carrier' => $carrier);
		Hook::exec('actionAdminOrdersTrackingNumberUpdate', $arr, null, false, true, false, $order->id_shop);
	}

	public static
	function sendnpmail($id_order){
		$order = new Order($id_order);
		$customer = new Customer($order->id_customer);
		$orderdetail = exec::GetOrderDetails($id_order);
		$mess = self::addToMess("","Ваше замовлення ".$order->reference.", № ".$order->id." буде відправлено Новою Поштою");
		$mess .= self::addToMess("Прізвище",$orderdetail['another_recipient']?$orderdetail['another_lastname']:$orderdetail['lastname']);
		$mess .= self::addToMess("Ім'я",$orderdetail['another_recipient']?$orderdetail['another_firstname']:$orderdetail['firstname']);
		$mess .= self::addToMess("Телефон",$orderdetail['another_recipient']?$orderdetail['another_phone']:$orderdetail['phone']);
		$mess .= self::addToMess("Область",exec::getareaname($orderdetail['area']));
		$mess .= self::addToMess("Місто",exec::getcityname($orderdetail['city']));
		
		$address = ($orderdetail['ware'] == 1) ? 
			"вул. ".$orderdetail['StreetName'].", буд. ".$orderdetail['BuildingNumber'].", кв. ".$orderdetail['Flat'] : 
			exec::getwarename($orderdetail['ware']);
		$mess .= ($orderdetail['ware'] == 1) ? self::addToMess("Адреса",$address) : self::addToMess("Відділення",$address);
		
		if($orderdetail['en'] != ""){
			$url ="http://novaposhta.ua/tracking/?cargo_number=".$orderdetail['en'];
			$url = "<a href='{$url}' target='_blank'>".$orderdetail['en']."</a>";
			$mess .= self::addToMess("Слідкувати на сайті Нової Пошти ",$url);
		}
		return Mail::Send(
			Configuration::get('PS_LANG_DEFAULT'),
			'newsletter',
			Mail::l("Ваше замовлення: ".$order->reference.", № ".$order->id." буде відправлено Новою Поштою"),
			array(
				'{firstname}' => $customer->firstname,
				'{lastname}' => $customer->lastname,
				'{message}'    => $mess),
			$customer->email,
			$customer->firstname.' '.$customer->lastname
		);
	}

	public static
	function sendnpadminmail($id_order){
		$order = new Order($id_order);
		$customer = new Customer($order->id_customer);
		$orderdetail = exec::GetOrderDetails($id_order);
		
		$mess = self::addToMess("","Зроблено замовлення ".$order->reference.", № ".$order->id." для відправлення Новою Поштою");
		$mess .= self::addToMess("Прізвище",$orderdetail['lastname']);
		$mess .= self::addToMess("Ім'я",$orderdetail['firstname']);
		$mess .= self::addToMess("Телефон",$orderdetail['phone']);
		$mess .= self::addToMess("Область",exec::getareaname($orderdetail['area']));
		$mess .= self::addToMess("Місто",exec::getcityname($orderdetail['city']));
		$address = ($orderdetail['ware'] == 1) ? "вул. ".$orderdetail['StreetName'].", буд. ".$orderdetail['BuildingNumber'].", кв. ".$orderdetail['Flat'] : exec::getwarename($orderdetail['ware']);
		$mess .= ($orderdetail['ware'] == 1) ? self::addToMess("Адреса",$address) : self::addToMess("Відділення",$address);
		
		$od = new OrderDetail();
		$products = $od->getList($id_order);
		
		$table = '<table width="100%">
			<thead>
				<tr>
					<th>№</th>
					<th>Код</th>
					<th>Артикул</th>
					<th>Товар</th>
					<th>Ціна</th>
					<th>К-ть</th>
					<th>Сума</th>
				</tr>
			</thead>
			<tbody>';
		$n = 1;
		foreach ($products as $product){
			$pr = new Product($product['product_id']);
			$table .= '<tr><td align="center">'.$n++.'</span></td>
				<td align="center">'.$product['product_id'].'</span></td>
				<td align="center">'.$pr->reference.'</span></td>
				<td>'.$product['product_name'].'</td>
				<td align="right">'.money_format('%.2n', round($product['unit_price_tax_incl'],2)).'</td>
				<td align="center">'.$product['product_quantity'].'</td>
				<td align="right">'.money_format('%.2n', round($product['total_price_tax_incl'],2)).'</td></tr>';
			}
		$table .= '</tbody>
					<tfoot>
						<tr>
							<td colspan="6" align="right">Всього:</td>
							<td align="right"><strong>'.money_format('%.2n', round($order->total_products_wt, 2)).'</strong></td>
						</tr>
					</tfoot>
				</table><br>';
		$mess .= $table;
		$employee = new Employee();
		$employee->getByEmail(Configuration::get('ecm_np_SendNPadminmail'));
		$tab = 'AdminOrders';
		$token = Tools::getAdminToken($tab.(int)Tab::getIdFromClassName($tab).(int)$employee->id);
		$a_href = _PS_BASE_URL_.__PS_BASE_URI__._NP_ADMIN_.'/index.php?controller=AdminOrders&id_order='.$order->id.'&vieworder&token='.$token;
		$url = "<a href='$a_href' target='_blank'>в Адмінку</a>";

		$mess .= self::addToMess("Деталі замовлення",$url);
		$subj = "Нове замовлення: ".$order->reference.", № ".$order->id." для відправлення Новою Поштою";
		//$subj = "New order".$order->reference", # ".$order->id." delivery by Nova Poshta";
		return Mail::Send(
			Configuration::get('PS_LANG_DEFAULT'),
			'newsletter',
			$subj,
			array(
				'{firstname}' => 'шановный',
				'{lastname}' => 'Администратор',
				'{message}'    => $mess),
			Configuration::get('ecm_np_SendNPadminmail'),
			'Адміністратору'
		);
	}

	public static
	function addToMess($c="", $o=""){
		if($c==""){
			$s = "<p><b>".$o."</b><br></p>";
		}
		else{
			$s = "<p>".$c.": <b>".$o."</b></p>";
		}
		return $s;
	}
	
	

}
