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

class Ecm_checkoutCheckout_smsModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
		$this->context->smarty->assign(array(
            'authMethod' => Configuration::get($this->module->name.'_auth'),
            'enable_button' => $this->context->cookie->sms_attempts_left-- ? true : false,
            'sms_pass_done' => $this->context->cookie->sms_pass_done,
        ));

		$this->setTemplate("module:{$this->module->name}/views/templates/front/".Configuration::get($this->module->name.'_simple_layout')."/checkout_sms.tpl");
    }

    public function init()
    {
        parent::init();
        $this->display_column_left = false;
        $this->display_hide_column_right = false;
    }

    public function displayAjaxSms()
    {
        $result['success'] = false;
        $error = '';
        $customer = new Customer();
		$phone = $this->module->phoneClear(trim(Tools::getValue('phone')));
        if (trim(Tools::getValue('phone')) && $id = Db::getInstance()->GetValue("SELECT `id_customer` FROM `"._DB_PREFIX_."customer` WHERE `phone`='{$phone}'")){
			$customer = new Customer($id);
		}
        if (!Validate::isLoadedObject($customer)) {
	        $error = $phone.' - '.$this->module->sms_not_found;
        } elseif (!$customer->active) {
            $error = $this->module->sms_inactive_customer;
        } elseif ((strtotime($customer->last_passwd_gen.'+'.($min_time = (int)Configuration::get('PS_PASSWD_TIME_FRONT')).' minutes') - time()) > 0) {
            $error = sprintf(Tools::displayError($this->module->sms_every_minute), $min_time);
        } else {
	        if ((Module::isInstalled('ecm_smssender'))){
				include(_PS_MODULE_DIR_ . 'ecm_smssender/classes/turbosms.php');
				include(_PS_MODULE_DIR_ . 'ecm_smssender/classes/message.php');
				$login = Configuration::get('ECM_SMSSENDER_ACCOUNT');
				$pwd = Configuration::get('ECM_SMSSENDER_ACCOUNT_PASSWORD');
				$sender = Configuration::get('ECM_SMSSENDER_ACCOUNT_ALFA');
				$smssender = new Client($login,$pwd,$sender);
	 	        $code = $this->generateCode(4);
				$message = $code;
				$smssender->send($phone,$message);
				//$smssender->sms_id = 1;
			}
	        if ($smssender->sms_id != null){
	            $result['success'] = true;
	            $this->context->cookie->__set('sms_attempts_left', 3);
	            $this->context->cookie->__set('sms_phone', $phone);
	            $this->context->cookie->__set('sms_pass_done', false);
	            $customer->last_passwd_gen = date('Y-m-d H:i:s', time());
	            $customer->update();
				Db::getInstance()->update('customer', array('pwd' => $code), 'id_customer =' .(int)$customer->id);
	        } else {
	            $error = $smssender->result_text;
	        }
        }
        $result['error_msg'] = '<div class="alert alert-danger"><p>'.$error.'</p></div>';
        die (Tools::jsonEncode($result));
    }
    
    public function postProcess()
    {
        parent::postProcess();
        if (((bool)Tools::isSubmit('VerifyCode')) == true) {
            $customer = Db::getInstance()->GetRow("SELECT * FROM `"._DB_PREFIX_."customer` WHERE `phone`='{$this->context->cookie->sms_phone}'");
			if (trim(Tools::getValue('code')) == $customer['pwd']){
				$this->context->cookie->sms_attempts_left == 1;
				Db::getInstance()->update('customer', array('pwd' => ''), 'id_customer =' .(int)$customer['id_customer']);
				$customer = new Customer((int)$customer['id_customer']);
	            $customer->passwd = Tools::encrypt($password = Tools::passwdGen(MIN_PASSWD_LENGTH, 'RANDOM'));
	            $customer->last_passwd_gen = date('Y-m-d H:i:s', time());
                if ($customer->update()) {
                    Hook::exec('actionPasswordRenew', array('customer' => $customer, 'password' => $password));
			        if ((Module::isInstalled('ecm_smssender'))){
						include(_PS_MODULE_DIR_ . 'ecm_smssender/classes/turbosms.php');
						include(_PS_MODULE_DIR_ . 'ecm_smssender/classes/message.php');
						$login = Configuration::get('ECM_SMSSENDER_ACCOUNT');
						$pwd = Configuration::get('ECM_SMSSENDER_ACCOUNT_PASSWORD');
						$sender = Configuration::get('ECM_SMSSENDER_ACCOUNT_ALFA');
						$smssender = new Client($login,$pwd,$sender);
			 	        $message = $password;
						$smssender->send($this->context->cookie->sms_phone,$message);
					}
					if ($smssender->sms_id == null){
			            $this->errors[] = $smssender->result_text;
			        } else {
			            $this->context->cookie->__set('sms_pass_done', true);
			            $this->context->cookie->__set('sms_phone', '');
	                }
                } else {
                        $this->errors[] = Tools::displayError('An error occurred with your account, which prevents us from sending you a new password. Please report this issue using the contact form.');
                }
			} else {
		        $this->errors[] = Tools::displayError($this->module->sms_invalid_code);
		        $this->errors[] = sprintf(Tools::displayError($this->module->sms_attempts_left), $this->context->cookie->sms_attempts_left);
				if ($this->context->cookie->sms_attempts_left == 1){
					Db::getInstance()->update('customer', array('pwd' => ''), 'id_customer =' .(int)$customer['id_customer']);
				}
			}
		}
	}
    
    public function generateCode($number)
	{
		$arr = array('0','1','2','3','4','5','6','7','8','9');
		$pass = '';
        for($i = 0; $i < $number; $i++){
			$pass .= $arr[rand(0, count($arr) - 1)];
        }
        return $pass;
	}

    public function setMedia()
    {
        parent::setMedia();
    }



}
