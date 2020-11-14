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

if (!defined('_PS_VERSION_'))
    exit;
class AdminLiqpayConfirmationController extends AdminControllerCore {
	const CURRENCY_EUR = 'EUR';
    const CURRENCY_USD = 'USD';
    const CURRENCY_UAH = 'UAH';
    const CURRENCY_RUB = 'RUB';
    const CURRENCY_RUR = 'RUR';
    private $_api_url = 'https://www.liqpay.ua/api/';
    private $_checkout_url = 'https://www.liqpay.ua/api/3/checkout';
    protected $_supportedCurrencies = array(
        self::CURRENCY_EUR,
        self::CURRENCY_USD,
        self::CURRENCY_UAH,
        self::CURRENCY_RUB,
        self::CURRENCY_RUR,
    );
    private $_public_key;
    private $_private_key;
    private $_server_response_code = null;
    
    public function __construct()
	{
		
		$this->context = Context::getContext();
		$config = Configuration::getMultiple(array('liqpay_id', 'liqpay_pass'));
		if (isset($config['liqpay_pass']))
            $this->_private_key = $config['liqpay_pass'];
        if (isset($config['liqpay_id']))
            $this->_public_key = $config['liqpay_id'];
		if (empty($this->_public_key)) {
            throw new InvalidArgumentException('public_key is empty');
        }
        if (empty($this->_private_key)) {
            throw new InvalidArgumentException('private_key is empty');
        }
		parent::__construct();
	}
	
	public function api($path, $params = array(), $timeout = 5)
    {
        if (!isset($params['version'])) {
            throw new InvalidArgumentException('version is null');
        }
        $url         = $this->_api_url . $path;
        $public_key  = $this->_public_key;
        $private_key = $this->_private_key;
        $data        = $this->encode_params(array_merge(compact('public_key'), $params));
        $signature   = $this->str_to_sign($private_key.$data.$private_key);
        $postfields  = http_build_query(array(
           'data'  => $data,
           'signature' => $signature
        ));
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // Avoid MITM vulnerability http://phpsecurity.readthedocs.io/en/latest/Input-Validation.html#validation-of-input-sources
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);    // Check the existence of a common name and also verify that it matches the hostname provided
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,$timeout);   // The number of seconds to wait while trying to connect
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);          // The maximum number of seconds to allow cURL functions to execute
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        
        $this->_server_response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $resp = json_decode($server_output);
        if($resp->result == 'error'){
        	$this->context->cookie->__set('redirect_errors', $this->l('err_code: ').$resp->err_code.' '.$resp->err_description);
 
		}
		if($resp->result == 'ok' && $resp->action == 'hold'){
        	$this->context->cookie->__set('redirect_success', $this->l('payment in the amount of ').$resp->amount.$this->l(' UAH  was successfully confirmed'));
 
		}
		if($resp->action == 'refund' && $resp->status == 'reversed'){
        	$this->context->cookie->__set('redirect_success', $this->l('payment was successfully returned'));
 
		}
        return json_decode($server_output);
    }

	private function encode_params($params)
    {
        return base64_encode(json_encode($params));
    }
    
    public function str_to_sign($str)
    {
        $signature = base64_encode(sha1($str, 1));
        return $signature;
    }
	private function params($id_order,$action,$amount)
    {
		return array(
			'action'        => $action,
			'version'       => '3',
			'order_id'      => $id_order,
			'amount'        => $amount,
			//'sandbox'       => 1,
			);
	}
    public function postProcess()
    {
		$id_order = Tools::getValue('ecmLiqpayhold_id_order');
    $id_order_r = $id_order;
   // $id_order = 415660;
    $postvalidate = Configuration::get('liqpay_postvalidate');
    if ($postvalidate == 1) {
      $id_order = Db::getInstance()->getValue("SELECT `id_cart` FROM `"._DB_PREFIX_."orders` WHERE `id_order` = ".$id_order);
    }
		$amount = Tools::getValue('ecmLiqpayhold_paid');
    $id_employee = $this->context->employee->id;
    $token = Tools::getAdminToken('AdminOrders' . (int) Tab::getIdFromClassName('AdminOrders') . (int) $id_employee);
		$link = $this->context->link->getAdminLink('AdminOrders').'&id_order='.$id_order_r.'&vieworder&token='.$token;
    
	   if (((bool)Tools::isSubmit('submitLiqpayRefund')) == true && Tools::getValue('token')== Tools::getAdminTokenLite('AdminLiqpayConfirmation')) {
			$this->api("request", $this->params($id_order,'refund',$amount));
            return Tools::redirectAdmin($link);
        }
       if (((bool)Tools::isSubmit('submitLiqpayHoldCompletion')) == true && Tools::getValue('token')== Tools::getAdminTokenLite('AdminLiqpayConfirmation')) {
            $this->api("request", $this->params($id_order,'hold_completion',$amount));
            return Tools::redirectAdmin($link);
        }
        parent::postProcess();

	}
	
}

