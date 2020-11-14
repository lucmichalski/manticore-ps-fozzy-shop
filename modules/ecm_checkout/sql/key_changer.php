<?php
set_time_limit(0);
$SELF = 'ecm_checkout';
include(dirname(__FILE__).'/../../../config/config.inc.php');
require_once(dirname(__FILE__).'/../'.$SELF.'.php');
if (Tools::getValue('secure_key')){
	if (md5(_COOKIE_KEY_.Configuration::get('PS_SHOP_NAME')) === Tools::getValue('secure_key')){

		$module = new $SELF();

        if (Tools::isSubmit('submitLIC')) {
			$pattern = '/^[0-9A-Fa-f]{5}\-[0-9A-Fa-f]{5}\-[0-9A-Fa-f]{5}\-[0-9A-Fa-f]{5}\-[0-9A-Fa-f]{5}$/';
			//$lic_key = Configuration::get('ecm_np_LIC_KEY');
			//if (empty($lic_key))
			$lic_key = Tools::getValue('LIC_KEY');
			preg_match($pattern, $lic_key, $matches);
			if (sizeof($matches)){
				Configuration::updateValue($module->name.'_lic_key', $lic_key,false,0,0);
				@unlink(dirname(__FILE__).'/../key.lic');
				echo( '
                <div class="bootstrap">
                <div class="alert alert-success">
                Успех!!!
                </div>
                </div>
                ');}
			else {
				echo( '
                <div class="bootstrap">
                <div class="alert alert-danger">
                <btn btn-default button type="btn btn-default button" class="close" data-dismiss="alert">×</btn btn-default button>
                Укажите валидный лицензионный ключ!!!
                </div>
                </div>
                ');
			}
		}

		$html= '<fieldset class="space">
			<form action="' . $_SERVER['REQUEST_URI'] . '" method="post">
            <legend>' . $module->l('Новый') . '</legend>
            <div class="col-xs-12">
            <label>' . $module->l('Licension key') . '</label>
            <div class="margin-form">
            <input type="text" style="width: 270px" name="LIC_KEY" placeholder="' . $module->l('Licension key') . '" required
            value="'.Configuration::get($module->name.'_lic_key').'"/>
            <p class="clear">' . $module->l('Enter your Licension key ') . '</p>
            </div>
            <center><hr>
            <input class="button" type="submit" name="submitLIC" value="' . $module->l('Save') . '" />
            </center>
            ';

		 echo($html);
	}
	else{
		echo 'is not a match';
	}
}

else{
	echo 'no secure key';
}

?>


