<?php
/**
* 2007-2019 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2019 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class Fozzy_print extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'fozzy_print';
        $this->tab = 'others';
        $this->version = '1.0.0';
        $this->author = 'Fozzy';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('FozzyPrint Stickers');
        $this->description = $this->l('Print stickers for Fozzy');

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        return parent::install();
    }

    public function uninstall()
    {
        return parent::uninstall();
    }

    public function getContent()
    {
        if (((bool)Tools::isSubmit('submitFozzy_printModule')) == true) {
            $this->postProcess();
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        return $this->renderForm();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitFozzy_printModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-envelope"></i>',
                        'desc' => $this->l('Enter a valid email address'),
                        'name' => 'FOZZY_PRINT_ACCOUNT_EMAIL',
                        'label' => $this->l('Email'),
                    ),
                    array(
                        'type' => 'password',
                        'name' => 'FOZZY_PRINT_ACCOUNT_PASSWORD',
                        'label' => $this->l('Password'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    protected function getConfigFormValues()
    {
        return array(
            'FOZZY_PRINT_ACCOUNT_EMAIL' => Configuration::get('FOZZY_PRINT_ACCOUNT_EMAIL', 'contact@prestashop.com'),
            'FOZZY_PRINT_ACCOUNT_PASSWORD' => Configuration::get('FOZZY_PRINT_ACCOUNT_PASSWORD', null),
        );
    }

    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
    }
    
    public function printPDF ($id_order, $show = 0, $filial = 1614, $np = false)
    {
      $order = new Order($id_order);

      if ($filial == 0) $filial = 1614; 

      if ($order->id)
        {
          $id_cart = (int)$order->id_cart;
          $id_address_delivery = (int)$order->id_address_delivery;
          $id_customer = (int)$order->id_customer;
          $id_carrier = (int)$order->id_carrier;
          $current_state = (int)$order->current_state;
          
          $address = new Address($id_address_delivery);
          $carrier = new Carrier($id_carrier);
          
          $sql_order_params = 'SELECT `norm`, `ice`, `fresh`, `hot`, `mest`, `id_sborshik`, `id_vodila`, `rro_num`, DATE_FORMAT(`dateofdelivery`, "%d-%m-%Y") as dd, `period` FROM `'._DB_PREFIX_.'orders` WHERE `id_order` = '.$id_order.' LIMIT 1';
          $order_params = Db::getInstance()->executeS($sql_order_params);
          
          $logistika = "";
          if ($order_params[0]['norm'] == 1) $logistika .= 'С | ';
          if ($order_params[0]['ice'] == 1) $logistika .= 'З | ';
          if ($order_params[0]['fresh'] == 1) $logistika .= 'О | ';
          if ($order_params[0]['hot'] == 1) $logistika .= 'Г | ';
          $logistika = substr($logistika,0,-3);
          $mest = (int)$order_params[0]['mest'];
          $dateofdelivery = $order_params[0]['dd'];
          $id_period = (int)$order_params[0]['period'];
          $rro_num = $order_params[0]['rro_num'];
          
          $sql_period = 'SELECT DATE_FORMAT(`timefrom`, "%H:%i") as tf, DATE_FORMAT(`timeto`, "%H:%i") as tt FROM `'._DB_PREFIX_.'nove_dateofdelivery` WHERE `id_period` = '.$id_period.' LIMIT 1';
          $period_params = Db::getInstance()->executeS($sql_period);
          $period = "с ".$period_params[0]['tf']." до ".$period_params[0]['tt'];
          
          if ($mest == 0) $mest = 1;
          $sql_sb = "SELECT `fio` FROM `"._DB_PREFIX_."fozzy_logistic_sborshik` WHERE `id_sborshik` = ".$order_params[0]['id_sborshik'];
          $sborshik = Db::getInstance()->getValue($sql_sb);
          $sql_v = "SELECT `fio` FROM `"._DB_PREFIX_."fozzy_logistic_vodila` WHERE `id_vodila` = ".$order_params[0]['id_vodila'];
          $vodila = Db::getInstance()->getValue($sql_v);
          
          $sql_carrier_name = "SELECT `external_module_name` FROM `"._DB_PREFIX_."carrier` WHERE `id_carrier` = ".$id_carrier;
          $carrier_name = Db::getInstance()->getValue($sql_carrier_name);
         // if ($carrier_name == 'ecm_novaposhta') 
         if ($np) 
            {
             $sql_carrier_ref = "SELECT `ref` FROM `"._DB_PREFIX_."ecm_newpost_orders` WHERE `id_order` = ".$id_order;
             $np_ref = Db::getInstance()->getValue($sql_carrier_ref);
             $pdf_original = file_get_contents('https://my.novaposhta.ua/orders/printMarking100x100/orders/'.$np_ref.'/type/PDF/apiKey/'.Configuration::get('ecm_np_API_KEY_1').'/zebra/zebra');
             if ($show) {
              header('Content-type:application/pdf');
              print $pdf_original;
              die();
             }
            }
          else 
            {
              // create new PDF document
              $width = 101;
              $height = 101; 
              $pageLayout = array($width, $height);
              $pdf = new TCPDF('L', 'mm', $pageLayout, true, 'UTF-8', false);
            //  $pdf = new TCPDF('L', 'mm', 'A7', true, 'UTF-8', false);
              // set document information
              $pdf->SetCreator(PDF_CREATOR);
              $pdf->SetAuthor('Fozzyshop');
              $pdf->SetTitle('MARKER');
              
              
              // remove default header/footer
              $pdf->setPrintHeader(false);
              $pdf->setPrintFooter(false);
              
              // set default monospaced font
              $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
              
              // set margins
              $pdf->SetMargins(2, 2, 2);
              
              // set auto page breaks
              $pdf->SetAutoPageBreak(false, 0);
              
              // set image scale factor
              $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
              
              // set some language-dependent strings (optional)
              if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
                  require_once(dirname(__FILE__).'/lang/eng.php');
                  $pdf->setLanguageArray($l);
              }
              
              // ---------------------------------------------------------
              //$mest = 3;
              // set font
              $pdf->SetFont('freeserif', 'B', 11);
              for ($i = 1; $i <= $mest; $i++) 
              {
              // add a page
            //  $pdf->AddPage('L', ['format' => 'A7','Rotate' => 90]);
               $pdf->AddPage();
              // set some text to print
              $txt_100 = '
                <table cellspacing="5" cellpadding="1">
                    <tr>
                        <td colspan="2" rowspan="2" style="font-size: 30pt;">№ '.$id_order.'</td>
                        <td align="rigth" rowspan="2">Мест</td>
                        <td style="font-size: 16pt;" border="2" align="center">'.$i.'/'.$mest.'</td>
                    </tr>
                    <tr>
                        <td style="font-size: 16pt;" border="2" align="center">'.$logistika.'</td>
                    </tr>
                    <tr>
                        <td colspan="3" style="font-size: 14pt;">Доставить: '.$dateofdelivery.'</td>
                        <td rowspan="4" border="0" align="center"><img src="https://fozzyshop.com.ua/modules/nove_customplugins/phpqrcode/genqr_order.php?id_order='.$id_order.'" /></td>
                    </tr>
                    <tr>
                        <td colspan="3" style="font-size: 14pt;">Окно: '.$period.'</td>
                    </tr>
                    <tr>
                        <td colspan="3" style="font-size: 14pt;">'.$carrier->name.'</td>
                    </tr>
                    <tr>
                        <td colspan="3" style="font-size: 14pt;">'.$order->payment.'</td>
                    </tr>
                    <tr>
                        <td colspan="4">Адрес: '.$address->firstname.' '.$address->lastname.'
                            '.$address->city.', '.$address->address1.'
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">+'.$address->phone_mobile.'</td>
                    </tr>
                    <tr>
                        <td colspan="4">+'.$address->phone.'</td>
                    </tr>
                </table>
                ';
              $txt_70 = '
                <table cellspacing="0" cellpadding="0">
                    <tr>
                        <td colspan="2" rowspan="2" style="font-size: 30pt;">№ '.$id_order.'</td>
                        <td align="rigth" rowspan="2">Мест</td>
                        <td style="font-size: 16pt;" border="2" align="center">'.$i.'/'.$mest.'</td>
                    </tr>
                    <tr>
                        <td style="font-size: 16pt;" border="2" align="center">'.$logistika.'</td>
                    </tr>
                    <tr>
                        <td colspan="4" style="font-size: 13pt;">Доставить: '.$dateofdelivery.'</td>
                    </tr>
                    <tr>
                        <td colspan="4" style="font-size: 13pt;">Окно: '.$period.'</td>
                    </tr>
                    <tr>
                        <td colspan="4" style="font-size: 13pt;">'.$carrier->name.'</td>
                    </tr>
                    <tr>
                        <td colspan="4" style="font-size: 13pt;">'.$order->payment.'</td>
                    </tr>
                    <tr>
                        <td colspan="4">Адрес: '.$address->firstname.' '.$address->lastname.', '.$address->city.', '.$address->address1.'
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">+'.$address->phone_mobile.'</td>
                    </tr>
                    <tr>
                        <td colspan="4">+'.$address->phone.'</td>
                    </tr>
                </table>
                ';
              
              $txt = '<p> <span style="font-size:30pt;">Заказ №'.$id_order.'</span>
                          <br/>Мест: <span style="font-size:20pt;">'.$i.'/'.$mest.'</span>
                          <br/>Оплата: '.$order->payment.'
                          <br/>Перевозчик: '.$carrier->name.'
                          <br/>Логистика: <span style="font-size:20pt;">'.$logistika.'</span>
                          <br/>Водитель: <span style="font-size:20pt;">'.$vodila.'</span>
                          <br/>Клиент: '.$address->firstname.' '.$address->lastname.'
                          <br/>Адрес: '.$address->city.', '.$address->address1.'
                          <br/>Телефон: <span style="font-size:15pt;">+'.$address->phone_mobile.'</span>
                      </p>';
        
             $pdf->writeHTML($txt_100, true, false, true, false, ''); 
              // ---------------------------------------------------------
              }
             
              //Close and output PDF document
              $pdf_original = $pdf->Output('order_'.$id_order.'.pdf', 'S');
              if ($show) {
                header('Content-type:application/pdf');
                print $pdf_original;
                die(); 
              }
            }
        }
      
      $pdf_b64 = base64_encode($pdf_original);
      $send_data = array();

         $send_data['dataType'] = 0;
         $send_data['debug'] = 1;
         $send_data['paperHeight'] = 101;
         $send_data['paperName'] = '';
         $send_data['paperWidth'] = 101;
         $send_data['pdfValue'] = $pdf_b64;
         $send_data['printerDPI'] = 203;
         
         switch ($filial) {
            case 1614:
                $send_data['printerName'] = 'p-kv-zabo37-024.businesskiev.fozzy.lan';
                break;
            case 322:
            //    $send_data['printerName'] = 'p-od-ser83a-022.businessukraine.fozzy.lan';
                $send_data['printerName'] = 'p-od-ser83a-021.businessukraine.fozzy.lan';
                break;
            case 1674:
                $send_data['printerName'] = 'p-dp-malin2-022.businessukraine.fozzy.lan';
                break;
            case 510:
                $send_data['printerName'] = 'p-kh-gertr9-022.businessukraine.fozzy.lan';
                break;
            case 154:
                $send_data['printerName'] = 'p-kv-brov2a-022.businesskiev.fozzy.lan';
                break;      
            case 1839:
                //$send_data['printerName'] = 'p-kv-band23-021.businesskiev.fozzy.lan';
                $send_data['printerName'] = 'p-kv-band23-020.businesskiev.fozzy.lan';
                break;
            case 382:
                $send_data['printerName'] = 'p-rv-kur9-020.businessukraine.fozzy.lan'; 
                break;
            case 1292:
                $send_data['printerName'] = 'p-kr-kiev66g-020.businessukraine.fozzy.lan';
                break;
            default:
                return true;
                break;
        }

      $print_service_link = 'https://bankserv.fozzy.ua:1449/FozzyShopOrderService.svc/PrintPDF';
        
      
      
      $send_data_json = json_encode($send_data);
      
      $request = $this->PostJSON($print_service_link,$send_data_json);
      
      $test = 1;
      if ($test) {
        return true;
      }
             
      if ($request != '<ConfirmResponse xmlns:i="http://www.w3.org/2001/XMLSchema-instance"><errorCode>0</errorCode><errorMessage>Ok</errorMessage></ConfirmResponse>')
        {
          return false;
        }
      else 
        {
          return true;
        } 
      
    }

    private function PostJSON($link, $JSONRequest = NULL)
    {
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $link);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
      if ($JSONRequest) curl_setopt($ch, CURLOPT_POSTFIELDS,$JSONRequest);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_TIMEOUT, 25); 
      $responce = curl_exec($ch);
      curl_close($ch); 
    return $responce; 
    }
      
    public function object2array($object) {
      return json_decode(json_encode($object), TRUE); 
    }
    
}
