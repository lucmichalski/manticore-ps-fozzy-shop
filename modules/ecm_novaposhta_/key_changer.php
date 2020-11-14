<?php
//нова пошта
//5C3D6-80F0A-18AA2-56DC7-D41CF
set_time_limit(0);
$SELF = 'ecm_novaposhta';
include(dirname(__FILE__).'/../../config/config.inc.php');
require_once(_PS_MODULE_DIR_.$SELF.'/'.$SELF.'.php');
if (Tools::getValue('secure_key')){
	if (md5(_COOKIE_KEY_.Configuration::get('PS_SHOP_NAME')) === Tools::getValue('secure_key')){

		$np = new ecm_novaposhta();

		 if (Tools::isSubmit('submitLIC')) {
			$pattern = '/^[0-9A-Fa-f]{5}\-[0-9A-Fa-f]{5}\-[0-9A-Fa-f]{5}\-[0-9A-Fa-f]{5}\-[0-9A-Fa-f]{5}$/';
			$lic_key = Tools::getValue('LIC_KEY');
			preg_match($pattern, $lic_key, $matches);
			if (sizeof($matches)){
				Configuration::updateValue('ecm_np_LIC_KEY', $lic_key);
				Db::getInstance()->Execute("UPDATE `"._DB_PREFIX_."configuration` SET `id_shop_group` = NULL, `id_shop` = NULL WHERE `name` = 'ecm_np_LIC_KEY'");
				@unlink(_PS_MODULE_DIR_.$SELF.'/key.lic');
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
            <legend>' . $np->l('Новый') . '</legend>
            <div class="col-xs-12">
            <label>' . $np->l('Licension key') . '</label>
            <div class="margin-form">
            <input type="text" style="width: 270px" name="LIC_KEY" placeholder="' . $np->l('Licension key') . '" required
            value="'.Configuration::get('ecm_np_LIC_KEY').'"/>
            <p class="clear">' . $np->l('Enter your Licension key ') . '</p>
            </div>
            <center><hr>
            <input class="button" type="submit" name="submitLIC" value="' . $np->l('Save') . '" />
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


