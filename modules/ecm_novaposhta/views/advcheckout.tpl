<fieldset>
<div class="panel">
	<div class="panel-heading">
		<i class="icon-eye-open"></i> {l s='Advanced checkout' mod='ecm_novaposhta'}
	</div>
	
	<label>{l s='Advanced checkout compatibility' mod='ecm_novaposhta'}</label>
	<div class="margin-form">
		<input type="checkbox" name="ac" value="1" {$ac} />
		<p class="clear">Если Вы используете модули 
		<a href="https://addons.prestashop.com/ru/checkout/11167--.html" target="_blank">
		<b>Покупка на одной странице + самовывоз</b></a> или 
		<a href="https://addons.prestashop.com/ru/express-checkout-process/18016-knowband-one-page-checkout-social-login-mailchimp.html" target="_blank">
		<b>Модуль Knowband - One Page Checkout, Social Login & Mailchimp</b></a> или 
		<a href="https://addons.prestashop.com/ru/express-checkout-process/6841-one-page-checkout-for-prestashop.html" target="_blank">
		<b>One Page Checkout PrestaShop</b></a></p><hr>
	</div>
		
		<label>{l s='Fill address fields'  mod='ecm_novaposhta'}</label>
		<div class="margin-form">
		<input type="checkbox" name="fill" value="1" {$fill} />
			<p class="clear">{l s='If checked, the address fields are filled in choosing the delivery address' mod='ecm_novaposhta'}</p>
		</div>
	
		<center><hr><input class="button" type="submit" name="submitUPDATE" value="{l s='Save' mod='ecm_novaposhta'}" /></center>
</div>
</fieldset>
