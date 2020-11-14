<?php
/**
* We offer the best and most useful modules PrestаShop and modifications for your online store.
*
* We are experts and professionals in PrestaShop
*
* @author    Elcommerce <support@elcommece.com.ua>
* @copyright 2010-2019 Elcommerce TM
* @license   Comercial
* @category  PrestaShop
* @category  Module
*/

/*
* [result] => ok
[action] => hold
[payment_id] => 968131774
[status] => success
[version] => 3
[type] => hold
[paytype] => card
[public_key] => i7038476234
[acq_id] => 414963
[order_id] => 36
[liqpay_order_id] => 4LHZMGAE1551971970988743
[description] => Payment for order № 36
[sender_card_mask2] => 537541*74
[sender_card_bank] => OJSC "UNIVERSAL BANK"
[sender_card_type] => mc
[sender_card_country] => 804
[ip] => 46.149.83.225
[amount] => 0.2
[currency] => UAH
[sender_commission] => 0
[receiver_commission] => 0.01
[agent_commission] => 0
[amount_debit] => 0.2
[amount_credit] => 0.2
[commission_debit] => 0
[commission_credit] => 0.01
[currency_debit] => UAH
[currency_credit] => UAH
[sender_bonus] => 0
[amount_bonus] => 0
[authcode_debit] => 223204
[rrn_debit] => 001154712842
[mpi_eci] => 5
[is_3ds] => 1
[language] => ru
[create_date] => 1551971952314
[end_date] => 1551972019030
[completion_date] => 1551972018668
[transaction_id] => 968131774*/

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/ecm_liqpay.php');
$liqpay        = new ecm_liqpay();

//dump(Context::getContext());
//die();

$merchant_pass = $liqpay->liqpay_merchant_pass;
$merchant_id   = $liqpay->liqpay_merchant_id;

$response      = $_POST['data'];
$signature     = base64_encode(sha1($merchant_pass.$response.$merchant_pass, 1));
$output        = json_decode(base64_decode($response), true);
if ($_POST['signature']) {

    $output = json_decode(base64_decode($response), true);
    $errors       = '';
    $logger = new FileLogger(0); //0 == debug level, logDebug() won’t work without this.
	$logger->setFilename(_PS_MODULE_DIR_."ecm_liqpay/debug.log");
	$logger->logDebug($output);
    //$postvalidate = Configuration::get('liqpay_postvalidate');
    $postvalidate = 1;
    //Подтверждение
    if ($output['action'] == 'hold' && $output['status'] == 'success' && $postvalidate == 0) {
        $order         = new Order($output['order_id'])    ;
        $amount        = Db::getInstance()->getValue("
            SELECT amount
            FROM `"._DB_PREFIX_."order_payment`
            WHERE order_reference = '".$order->reference."'");
        $result_amount = $output['amount'] - $amount;
        addOrderPayment($result_amount, $output, $order);
        changeIdOrderState($output['order_id'],Configuration::get('EC_OS_HOLDCONFIRM'));

    }
    //Возврат
    elseif ($output['action'] == 'refund' && $output['status'] == 'reversed') {
     /*   $ref = Db::getInstance()->getValue("
            SELECT order_reference
            FROM `"._DB_PREFIX_."order_payment`
            WHERE transaction_id = '".(int)$output['payment_id']."'");
        $orders = Db::getInstance()->ExecuteS("
            SELECT id_order
            FROM `"._DB_PREFIX_."orders`
            WHERE reference = '".$ref."'");
        foreach ($orders as $order) {
            changeIdOrderState($order['id_order'],_PS_OS_REFUND_);
        }
       */
    }
    elseif (($output['action'] == 'hold' || $output['action'] == 'pay') && ($output['status'] == 'success' || $output['status'] == 'hold_wait' || $output['status'] == 'sandbox') ){
        $rest_amount = (float)$output['amount'];
         //$logger->logDebug($rest_amount);
         
        // $logger->logDebug($postvalidate);
        if ($postvalidate == 1) {
           // $id_cart_ = explode("-",$output['order_id']);
           // $cart     = new Cart((int)$id_cart_[0]);
            $id_cart_ = (int)$output['order_id'];
            $cart     = new Cart($id_cart_);
            $currency_order = new Currency($cart->id_currency);
            if (Configuration::get('liqpay_delivery'))
            $amount = $rest_amount;
            else
            {
            $amount = $rest_amount;
            }
  
           $customer = new Customer((int)$cart->id_customer);
           $email_cr = explode('@',$customer->email);
           $last_email = array_pop($email_cr);
         //  $shipping_summ = $cart->getTotalShippingCost(); // Не работает эта функция
         //  $cart_summ = $cart->getOrderTotal(true, Cart::ONLY_PRODUCTS);   // Не работает эта функция
           $courier_array = array();
           $courier_array = array(27,33,30,42,55,57);
           
            if ( ($last_email == 'fozzy.ua' || $last_email == 'temabit.com') && $amount > 620 && in_array((int)$cart->id_carrier, $courier_array) && $output['sender_card_type'] != 'mc' ) 
            {
              $rule_name = array();
              $rule_name[1] = 'Бесплатная доставка для сотрудников';
              $rule_name[2] = 'Безкоштовна доставка для працівників';
              $cart_rule = new CartRule();
          		if ($last_email == 'fozzy.ua') $cart_rule->code = 'Fozzy_'.$cart->id;
              if ($last_email == 'temabit.com') $cart_rule->code = 'Temabit_'.$cart->id;
          		$cart_rule->name = $rule_name;
          		$cart_rule->id_customer = (int)$cart->id_customer;
          		$cart_rule->free_shipping = true;
          		$cart_rule->quantity = 1;
          		$cart_rule->quantity_per_user = 1;
          		$cart_rule->minimum_amount_currency = (int)$cart->id_currency;
          		$cart_rule->reduction_currency = (int)$cart->id_currency;
          		$cart_rule->date_from = date('Y-m-d H:i:s', time());
          		$cart_rule->date_to = date('Y-m-d H:i:s', time() + 24 * 36000);
          		$cart_rule->active = 1;
          		$cart_rule->add();
              
              $cart->addCartRule((int)$cart_rule->id);
            }
             
      
            
        //   $courier_array = array();
        //   $courier_array = array(27,33,30,42,55,57);
       //    $ship = $cart->getTotalShippingCost(); // Не работает эта функция
           
            if ($output['sender_card_type'] == 'mc' && in_array($cart->id_carrier, $courier_array) ) 
            {
              $rule_name = array();
              $rule_name[1] = 'Безкоштовна доставка від MasterCard';
              $rule_name[2] = 'Безкоштовна доставка від MasterCard';
              $cart_rule = new CartRule();
          		$cart_rule->code = 'MC_'.$cart->id;
          		$cart_rule->name = $rule_name;
          		$cart_rule->id_customer = $cart->id_customer;
          		$cart_rule->free_shipping = true;
          		$cart_rule->quantity = 1;
          		$cart_rule->quantity_per_user = 1;
          		$cart_rule->minimum_amount_currency = $cart->id_currency;
          		$cart_rule->reduction_currency = $cart->id_currency;
          		$cart_rule->date_from = date('Y-m-d H:i:s', time());
          		$cart_rule->date_to = date('Y-m-d H:i:s', time() + 24 * 36000);
          		$cart_rule->active = 1;
          		$cart_rule->add();
              
              $cart->addCartRule($cart_rule->id);
            }
             
            
            if (Configuration::get('liqpay_wrapping')) {
                $amount += $order->total_wrapping;
            }
            $transaction_id = 'liqpay Transaction ID: '.$output['transaction_id'].' '.@$output['sender_phone'];
//$logger->logDebug($rest_amount);
           // $liqpay->validateOrder($id_cart_, 912, $amount, $liqpay->displayName, $transaction_id,array(),null,false,false,new shop((int)$cart->id_shop));
            $liqpay->validateOrder($id_cart_, 912, $amount, $liqpay->displayName, $transaction_id);
//$logger->logDebug($rest_amount);
            $ordernumber    = Order::getOrderByCartId($id_cart_);
//$logger->logDebug($rest_amount);
            $orderp          = new Order((int)$ordernumber);

//$logger->logDebug($rest_amount);
            updateOrderPayment($rest_amount, $output, $orderp);

            
            
        }
        else {
            $ordernumber = (int)$output['order_id'];
            
            $order = new Order((int)$ordernumber);
            //Проверка существования заказа
            if (!Validate::isLoadedObject($order)) {
                ecm_liqpay::validateAnsver($liqpay->l('Order does not exist'));
            }
            $currency_order = new Currency($order->id_currency);
            $total_to_pay   = $order->total_paid;
            $total_to_pay   = number_format($total_to_pay, 2, '.', '');
            //Проверка суммы заказа
               
            if (Configuration::get('liqpay_delivery'))
            $amount = $rest_amount;
            else
            $amount = $rest_amount + $order->total_shipping;
            if (Configuration::get('liqpay_wrapping')) {
                $amount += $order->total_wrapping;
            }
            //$logger->logDebug($amount." -- ".$total_to_pay);
            if ($amount != $total_to_pay) {
                ecm_liqpay::validateAnsver($liqpay->l('Incorrect payment summ'));
            }
			
            if ($output['status'] == 'success') {
                $new_os = _PS_OS_PAYMENT_;
            }elseif ($output['status'] == 'hold_wait') {
                $new_os = Configuration::get('EC_OS_HOLDPAYMENT');
            }elseif ($output['status'] == 'sandbox') {
                $new_os = (Configuration::get('EC_OS_HOLDPAYMENT'))?Configuration::get('EC_OS_HOLDPAYMENT'):_PS_OS_PAYMENT_;
            }
            //$logger->logDebug($new_os);
            //Записываем ид транзакции
            $ref = Db::getInstance()->getValue("
                SELECT order_reference
                FROM `"._DB_PREFIX_."order_payment`
                WHERE transaction_id = '".(int)$output['payment_id']."'");
            if (!$ref)
            addOrderPayment($rest_amount, $output, $order);
            //Меняем статус заказа
            if ($order->current_state != $new_os)
            $order->update();
            changeIdOrderState($ordernumber,$new_os);
        }
    }
    elseif ($output['status'] == 'failure') {
        $liqpay->validateOrder($id_cart, _PS_OS_ERROR_, 0, $liqpay->displayName, $errors.'<br />');
    }
 }
else {
    Tools::redirectLink(__PS_BASE_URI__.'order.php');
} 
function changeIdOrderState($id_order,$new_os){
	
	$history = new OrderHistory();
    $history->id_order = $id_order;
    $history->changeIdOrderState($new_os, $id_order);
    $history->addWithemail(true);
    return true;
}
function addOrderPayment($result_amount,$output,$order){
	if ($result_amount != 0) {
            $order_payment = new OrderPayment();
            $order_payment->order_reference = $order->reference;
            $order_payment->id_currency = Currency::getIdByIsoCode($output['currency']);
            $currency = new Currency($order_payment->id_currency);
            $order_payment->conversion_rate = $currency->conversion_rate;
            $order_payment->payment_method = $order->payment;
            $order_payment->transaction_id = $output['payment_id'];
            $order_payment->amount = $result_amount;
            $order_payment->card_number = $output['sender_card_mask2'];
    		$order_payment->card_brand = $output['sender_card_type'];
            $order_payment->card_expiration;
            $order_payment->card_holder = $output['sender_card_bank'];
            $order_payment->date_add = date('Y-m-d H:i:s');
            if ($order_payment->id_currency ==  $order->id_currency) {
                 $order->total_paid_real += $order_payment->amount;
            } else {
                 $order->total_paid_real += Tools::ps_round(Tools::convertPrice($order_payment->amount, $order_payment->id_currency, false), 2);
            }
            $order->valid = 1;
            $order->update();
            $res = $order_payment->add();
           
        }
	 if ($res) return TRUE;
}
function updateOrderPayment($result_amount,$output,$order){
	if ($result_amount != 0) {
            
        $sql = "UPDATE `ps_order_payment` SET `amount`=".$result_amount.",`transaction_id`='".$output['payment_id']."',`card_number`='".$output['sender_card_mask2']."',`card_brand`='".$output['authcode_debit']."',`card_holder`='".$output['rrn_debit']."' WHERE `order_reference`='".$order->reference."'";
  
           
        }
	 return Db::getInstance()->execute($sql);
}
