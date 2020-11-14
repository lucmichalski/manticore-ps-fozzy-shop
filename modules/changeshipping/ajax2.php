<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'../../../init.php');
include(dirname(__FILE__).'/changeshipping.php');

$changeshipping = new Changeshipping();
global $smarty, $cookie;
ini_set('display_errors', 'on');

if (Tools::isSubmit('submitChangepayment'))
{
	$errors = array();
    
    $order_id = (int)Tools::getValue('orderid');
    $order_p_id = (int)Tools::getValue('orderpid');
		$payment = Tools::getValue('payment');
    $payment_name = Tools::getValue('payment_name');
		$summ = Tools::getValue('price');
    $ddate = Tools::getValue('ddate');
		
		if (!Validate::isPrice($summ)) $errors[] = $changeshipping->l('Price  include tax must be a number with point as separator');

	if (sizeof($errors))
	{
		$return = array(
			'hasError' => !empty($errors), 
			'errors' => $errors
		);

		die(Tools::jsonEncode($return));
	}
	else
	{
    $order = new Order($order_id);
		$ddate = date( 'Y-m-d H:i:s', strtotime( $ddate ) );

    
    $sql = "UPDATE `"._DB_PREFIX_."orders` SET `module` = '".$payment."', `payment` = '".$payment_name."' WHERE `id_order` = ".$order_id;
    $sql_p = "UPDATE `"._DB_PREFIX_."order_payment` SET `date_add` = '".$ddate."', `amount` = ".$summ.", `payment_method` = '".$payment_name."' WHERE `id_order_payment` = ".$order_p_id;
		
		Db::getInstance()->execute ($sql);
		Db::getInstance()->execute ($sql_p);

    $return = true;
    die(Tools::jsonEncode($return));
	}
}
else
{
		$id_lang = $cookie->id_lang;
    
    $dirbase = Tools::getValue('dirbase');
    $orderid = Tools::getValue('orderid');
    
    $payments_list = PaymentModule::getInstalledPaymentModules();
    
    foreach ($payments_list as $key=>$pay)
      {
        $payment_namr = PaymentModule::getInstanceById($pay['id_module']);
        $payments_list[$key]['full_name'] = $payment_namr->displayName;
      }
    
    $order = new Order($orderid);
		$order_payments = OrderPayment::getByOrderReference($order->reference);
    
    if ($order_payments) 
      {
        $id_order_payment = (int)$order_payments[0]->id;
        $order_payment_summ = number_format($order_payments[0]->amount,2, '.', '');
        $ddate = date( 'd.m.Y', strtotime( $order_payments[0]->date_add ) );
      }
    else
      {
        $id_order_payment = 0;
        $order_payment_summ = number_format($order->total_paid,2, '.', '');
        $ddate = date( 'd.m.Y' );
      }

    $selector = '<select id="paymentslist">';
    foreach ($payments_list as $payment)
    {
      if ($payment['name'] == $order->module)
      $selector.='<option value="'.$payment['name'].'" selected="selected">'.$payment['full_name'].'</option>';
      else
      $selector.='<option value="'.$payment['name'].'">'.$payment['full_name'].'</option>';
    }
    $selector.='</select>';
    
    $smarty->assign(
        array(
            'payments_list' => $payments_list,
            'selector' => $selector,
            'order_payment_summ' => $order_payment_summ,
            'dirbase' => $dirbase,
            'orderid' => $orderid,
            'orderpid' => $id_order_payment,
            'ddate' => $ddate,
        )
    );
    
	if ($id_order_payment) $smarty->display(dirname(__FILE__).'/form2.tpl');
}