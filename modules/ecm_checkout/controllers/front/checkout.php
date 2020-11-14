<?php
/**
* We offer the best and most useful modules PrestaShop and modifications for your online store.
*
* We are experts and professionals in PrestaShop
*
* @author    Elcommerce <support@elcommece.com.ua>
* @copyright 2010-2018 Elcommerce
* @license   Comercial
* @category  PrestaShop
* @category  Module
*/

use PrestaShop\PrestaShop\Core\Checkout\TermsAndConditions;

class Ecm_checkoutCheckoutModuleFrontController extends ModuleFrontController
{
    public $sc;
	public function displayAjaxRefresh()
    {
        if(Tools::GetValue('command') == 'wake'){die(Tools::jsonEncode(true));}
        $commands = explode(',', Tools::GetValue('command'));
        $this->sc->fixCart();
		CartRule::autoRemoveFromCart();
        $result = [];
        foreach ($commands as $command){
            if (!$this->context->cart->nbProducts()){
                $url_string = parse_url(__PS_BASE_URI__ . $_SERVER['REQUEST_URI']);
                $url_params = array();
                if (isset($url_string['query'])) {
                    parse_str($url_string['query'], $url_params);
                    if (isset($url_params['isPaymentStep'])) {
                        unset($url_params['isPaymentStep']);
                    }
                }
                $result['first_href'] = $result['href'] = $this->context->link->getPageLink('cart').'?action=show';
                $result['empty_cart'] = true;
                die (Tools::jsonEncode($result));
            }
            switch (trim($command)) {
	            case 'to_login':
	                $result = $this->sc->login();
	                break;
	            case 'to_checkout':
	                $this->sc->makeOrder($result);
	                break;
	            case 'delete_Discount':
	                $this->sc->deleteDiscount($result);
	                break;
	            case 'add_Discount':
	                $this->sc->addDiscount($result);
	                break;
	            case 'set_TOS':
	                $this->sc->setTOS();
	                break;
	            case 'init_cart':
	                $this->sc->initCart($result);
	                break;
	            case 'save_country':
	                $result['address'] = $this->sc->saveCountry();
	                break;
	            case 'save_address':
	                $this->sc->saveAddress($result);
	                break;
	            case 'change_address':
	                $result[] = $this->sc->changeAddress();
	                break;
	            case 'save_auth':
	                $this->context->cookie->__set('type_auth', Tools::GetValue('name'));
                    Media::addJsDef(array('type_auth'   => $this->context->cookie->type_auth,));
	                break;
	            case 'save_callme':
               	    Db::getInstance()->update($this->module->name, ['callme' => Tools::GetValue('name')], "`id_cart` = '{$this->context->cart->id}' AND `id_country` = '{$this->context->cookie->sc_country}' AND `id_address` = '{$this->context->cookie->sc_address_delivery}'");
                    break;
	            case 'save_login':
	                $this->sc->saveLogin($result);
	                break;
	            case 'save_pass':
	                $this->sc->saveLogin($result);
	                break;
	            case 'save_cart':
	                $this->sc->saveCart();
	                break;
	            case 'save_message':
	                $this->sc->saveMessage();
	                break;
	            case 'set_quantity':
	                $this->sc->setQuantity($result);
	                break;
	            case 'quantity_up':
	                $this->sc->quantityUp($result);
	                break;
	            case 'quantity_down':
	                $this->sc->quantityDown($result);
	                break;
	            case 'quantity_delete':
	                $this->sc->setDelete($result);
	                break;
	            case 'cart':
	                $this->sc->renderCart($result);
	                break;
	            case 'set_carrier':
	                $result[] = $this->sc->setCarrier();
	                break;
	            case 'carrier':
	                $this->sc->renderCarrier($result);
	                break;
	            case 'checkout':
	                $this->sc->renderCheckout($result);
	                break;
	            case 'set_payment':
	                $this->sc->setPayment($result);
                    break;
	            case 'payment':
	                $this->sc->renderPayment($result);
	                break;
	            case 'save_customer':
	                $this->sc->saveCustomer($result);
	                break;
	            case 'customer':
	                $this->sc->renderCustomer($result);
	                break;
	            case 'fix_carrier':
	                $result[] = $this->sc->fixCarrier();
	                break;
	            case 'make_order':
	                $this->sc->makeOrder($result);
	                break;
	            default:
	                $result = false;
	        }
			if(isset($result['discount_errors'])){
				die (Tools::jsonEncode($result));
			}
        }

 		//if($command == 'save_login' or $command == 'save_address') $this->module->CorrectPhone($command);
        die (Tools::jsonEncode($result));
    }

    public function getTemplateVarPage()
    {
        $page = parent::getTemplateVarPage();
        if (isset($page['meta'])) {
            $page['meta']['title'] = sprintf(
                '%s | Simplecheckout',
                Configuration::get('PS_SHOP_NAME')
            );
        }
        return $page;
    }

    public function setMedia()
    {
        parent::setMedia();

        $js = array(
			'modules/'.$this->module->name.'/views/js/front.js',
            'modules/'.$this->module->name.'/views/js/custom.js',
        );
		
		if(Configuration::get($this->module->name.'_phone_mask')) {
			//$js[] = 'modules/'.$this->module->name.'/views/js/jquery.inputmask.js'; //dodo
			$js[] = 'modules/'.$this->module->name.'/views/js/jquery.mask.js';
			//$js[] = 'modules/'.$this->module->name.'/views/js/z_custom_mask_UA.js';
		}
			
        
        $css = array(
			"modules/".$this->module->name."/views/templates/front/".Configuration::get($this->module->name.'_simple_layout')."/front.css",
			"modules/".$this->module->name."/views/css/custom.css",
        );
        
        foreach ($css as $uri) {
            if ($uri = $this->getAssetUriFromLegacyDeprecatedMethod($uri)) {
                $this->registerStylesheet(sha1($uri), $uri, array('media' => 'all', 'priority' => 9999));
            }
        }

        foreach ($js as $uri) {
            if ($uri = $this->getAssetUriFromLegacyDeprecatedMethod($uri)) {
                $this->registerJavascript(sha1($uri), $uri , array('priority' => 333));
            }
        }
		
        $this->addJqueryPlugin(array('growl'));
    }

    public function init()
    {
        parent::init();
        $this->sc = new Sc();
        //if (Tools::getvalue('action')=='Refresh') return;
		if (!$this->module->module_active) {
            if (Configuration::get('PS_ORDER_PROCESS_TYPE') == 1) {
                Tools::redirect('index.php?controller=order-opc');
            } else {
                Tools::redirect('index.php?controller=order');
            }
        }
		$this->sc->initCart();
        if (Configuration::get('PS_CONDITIONS') && !isset($this->context->cookie->checkedTOS)){
            $this->context->cookie->__set('checkedTOS', false);
        }
        if (Configuration::get($this->module->name.'_hide_column_left')) $this->display_column_left = false;
        if (Configuration::get($this->module->name.'_hide_column_right')) $this->display_hide_column_right = false;
		
		if (!$this->context->cookie->__isset('sc_country')){
			$this->context->cookie->__set('sc_country', Configuration::get('PS_COUNTRY_DEFAULT'));
			$country = new Country($this->context->cookie->sc_country);
			$this->context->country = $country;
		}
		
		//if (!isset($this->context->cookie->sc_address_delivery)){$this->context->cookie->__set('sc_address_delivery', $this->sc->getAddressByCountry($this->context->cookie->sc_country));}
 		
 		//if (!isset($this->context->cookie->sc_carrier)){ $this->sc->fixCarrier(); }
 		
 		if (!$this->context->cookie->__isset('current_payment')){ $this->context->cookie->__set('current_payment', Configuration::get($this->module->name.'_payment'));		}
        if (!$this->context->cookie->__isset('exist_customer')){ $this->context->cookie->__set('exist_customer', false); }
		if (!$this->context->cookie->__isset('need_logout')){ $this->context->cookie->__set('need_logout', false); }
		if (!$this->context->cookie->__isset('sc_customer')){ $this->context->cookie->__set('sc_customer', 'new'); }
        if ($this->context->customer->isLogged()){ $this->context->cookie->__set('sc_customer', 'old'); }
		if (!$this->context->cookie->__isset('type_auth')){ Configuration::Get('PS_GUEST_CHECKOUT_ENABLED')?$this->context->cookie->__set('type_auth', 'guest'):$this->context->cookie->__set('type_auth', 'registration'); }
		//dump($this->context->cookie->sc_customer);
		$this->sc->tryScLogOut();
    }

    
    public function initContent()
    {
		if (Tools::getvalue('action')=='Refresh') return;
        parent::initContent();
		$this->sc->initCart();
        $result = [];
        //$result['function'] = __FUNCTION__;
        $this->sc->renderCustomer($result);
		$this->sc->renderCart($result);
		$this->sc->renderCarrier($result);
		$this->sc->renderPayment($result);
		$this->sc->renderCheckout($result);
        //dump($result);die();
        $isFree = 0 == (float) $this->context->cart->getOrderTotal(true, Cart::BOTH);
		$this->context->smarty->assign([
			'sc_customer' => $this->context->cookie->__get('sc_customer'),
			'checkedTOS' => $this->context->cookie->__get('checkedTOS'),
			'renderSeq' => $this->module->renderSeq(),
			'renderCustomerSeq' => $this->module->renderCustomerSeq(),
			'module_name' => $this->module->name,
			'version' => $this->module->version,
			'hide_header' => Configuration::get($this->module->name.'_hide_header'),
			'hide_column_right' => Configuration::get($this->module->name.'_hide_column_right'),
			'HOOK_LEFT_COLUMN' => null,
			'HOOK_RIGHT_COLUMN' => null,
			'cart_qties' => $this->context->cart->nbProducts(),
			'id_cart' => $this->context->cart->id,
			'type_auth'   => $this->context->cookie->__get('type_auth'),
            'customer_place' => $result['customer'],
            'cart_place' => $result['cart'],
            'checkout_place' => $result['checkout'],
            'carrier_place' => $result['carrier'],
            'payment_place' => $result['payment'],
		]);
		$this->setTemplate("module:{$this->module->name}/views/templates/front/".Configuration::get($this->module->name.'_simple_layout')."/simcheck.tpl");
	}


    public function getConditionsToApproveForTemplate()
    {
        return array_map(function (TermsAndConditions $condition) {
            return $condition->format();
        }, $this->getConditionsToApprove());
    }

	private function getConditionsToApprove()
    {
        $allConditions = array();
        $hookedConditions = Hook::exec('termsAndConditions', array(), null, true);
        if (!is_array($hookedConditions)) {
            $hookedConditions = array();
        }
        foreach ($hookedConditions as $hookedCondition) {
            if ($hookedCondition instanceof TermsAndConditions) {
                $allConditions[] = $hookedCondition;
            } elseif (is_array($hookedCondition)) {
                foreach ($hookedCondition as $hookedConditionObject) {
                    if ($hookedConditionObject instanceof TermsAndConditions) {
                        $allConditions[] = $hookedConditionObject;
                    }
                }
            }
        }

        if (Configuration::get('PS_CONDITIONS')) {
            array_unshift($allConditions, $this->getDefaultTermsAndConditions());
        }
		
		
        /*
         * If two TermsAndConditions objects have the same identifier,
         * the one at the end of the list overrides the first one.
         * This allows a module to override the default checkbox
         * in a consistent manner.
         */
        $reducedConditions = array();
        foreach ($allConditions as $condition) {
			if ($condition instanceof TermsAndConditions) {
                $reducedConditions[$condition->getIdentifier()] = $condition;
            }
        }

        return $reducedConditions;
    }

    private function getDefaultTermsAndConditions()
    {
        $cms = new CMS(Configuration::get('PS_CONDITIONS_CMS_ID'), $this->context->language->id);
        $link = $this->context->link->getCMSLink($cms, $cms->link_rewrite, (bool) Configuration::get('PS_SSL_ENABLED'));
        $termsAndConditions = new termsAndConditions();
        $termsAndConditions->setText($this->module->terms_text, $link);
		$termsAndConditions->setIdentifier('terms-and-conditions');
        return $termsAndConditions;
    }
    
	protected function l($string,$specific = false, $class = null, $addslashes = false, $htmlentities = true)
    {
        if (_PS_VERSION_ >= '1.7') {
            return Context::getContext()->getTranslator()->trans($string);
        } else {
            return parent::l($string, $class, $addslashes, $htmlentities);
        }
    }
	
    public function clearSCAddress()
    {
 		$sql = "SELECT `id_address`, `company`, `lastname`, `firstname`, `middlename`, `address1`, `address2`, 
				`postcode`, `city`, `other`, `phone`, `phone_mobile`, `vat_number` 
				FROM `"._DB_PREFIX_."address` WHERE `sc_address` = 1";
		$addresses = Db::getInstance()->ExecuteS($sql);
		foreach ($addresses as $address){
			$fields = array();
			foreach ($address as $name=>$value){
				if ($name == 'id_address'){continue;}
				if (!empty($value)){
					$fields[$name] = '';
				}
			}
			if (count($fields)){
				Db::getInstance()->update('address', $fields, "id_address = '{$address['id_address']}'");
			}
		}
    }


}
