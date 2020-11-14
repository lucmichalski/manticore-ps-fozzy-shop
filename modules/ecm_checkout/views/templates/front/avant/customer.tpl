{**
 * We offer the best and most useful modules PrestaShop and modifications for your on-line store.
 *
 * We are experts and professionals in PrestaShop
 *
 * @author    Elcommerce support@elcommece.com.ua
 * @copyright 2010-2018 Elcommerce
 * @license   Comercial
 * @category  PrestaShop
 * @category  Module
*}

{*dump($sc_customer)*}
<div class="card-block"><h5 class="h5"> {l s='About customer' mod='ecm_checkout'}</h5>
<hr class="separator">
</div>

{if !Configuration::Get('PS_GUEST_CHECKOUT_ENABLED') && $type_auth == 'guest'}{$type_auth = 'registration'}{/if}

<section class="card-block sc_customer">

{if !$ecm_customer->id}
<div class="form-row options-row auth">
{if Configuration::Get('PS_GUEST_CHECKOUT_ENABLED')}
{$value_auth = 'guest'}
	<div class="login_radio btn visible-lg-inline-block visible-md-inline-block visible-sm-inline-block d-inline-block"
		title="{l s='Guest checkout' mod='ecm_checkout'}" data-auth="{$value_auth}">
		<span class="custom-radio">
		<input type="radio" name="type_auth" class="type_auth" id="type_auth_{$value_auth}" value="{$value_auth}"
		{if $type_auth == {$value_auth}}checked{/if}/>
		<span></span>
		</span>
		<label for="type_auth_{$value_auth}">{l s='Guets' mod='ecm_checkout'}</label>
	</div>
{/if}
{$value_auth = 'registration'}
	<div class="login_radio btn visible-lg-inline-block visible-md-inline-block visible-sm-inline-block d-inline-block"
		title="{l s='New user registration' mod='ecm_checkout'}" data-auth="{$value_auth}">
		<span class="custom-radio">
		<input type="radio" name="type_auth" class="type_auth" id="type_auth_{$value_auth}" value="{$value_auth}"
		{if $type_auth == $value_auth}checked{/if}/>
		<span></span>
		</span>
		<label for="type_auth_{$value_auth}">{l s='Registration' mod='ecm_checkout'}</label>
	</div>
{$value_auth = 'login'}
	<div class="login_radio btn visible-lg-inline-block visible-md-inline-block visible-sm-inline-block d-inline-block"
		title="{l s='Login for registred user' mod='ecm_checkout'}" data-auth="{$value_auth}">
		<span class="custom-radio">
		<input type="radio" name="type_auth" class="type_auth" id="type_auth_{$value_auth}" value="{$value_auth}"
		{if $type_auth == $value_auth}checked{/if}/>
		<span></span>
		</span>
		<label for="type_auth_{$value_auth}">{l s='Login' mod='ecm_checkout'}</label>
	</div>
</div>

{/if}

{if $authMethod == 2 && !$ecm_customer->id}
	<div class="form-row phone_email-block {if !$ecm_customer->email}frl-50{else}frr-100{/if}">
	{if $authMethod == 2}{$orphone=" {l s='or phone' mod='ecm_checkout'}"}{else}{$orphone=''}{/if}
	{if $ecm_customer->email}{$value=$ecm_customer->email}{else}{$value=$ecm_customer->phone}{/if}
		<input class="form-control phone_email" required="required" title="{l s='E-mail' mod='ecm_checkout'}{$orphone}" id="phone_email" value="{$value}" type="text" placeholder="{l s='E-mail' mod='ecm_checkout'}{$orphone}" autocomplete="off"/>
		<span class="for_email warning unvisible"></span>
		<span class="for_email msg unvisible"></span>
	</div>
{/if}

<div class="required form-row email-block">
	{if $authMethod == 2}{$orphone=" {l s='or phone' mod='ecm_checkout'}"}{else}{$orphone=''}{/if}
	{if $ecm_customer->email}{$value=$ecm_customer->email}{else}{$value=$ecm_customer->phone}{/if}
	<input class="form-control login_ontype email" title="{l s='E-mail' mod='ecm_checkout'}{$orphone}" id="email" value="{$ecm_customer->email}" type="email" act="save_login" placeholder="{l s='E-mail' mod='ecm_checkout'}{$orphone}"
	{if $authMethod != 1}required="required"{/if} {if $authMethod == 2}autocomplete="off"{/if} {if $authMethod != 0 && !$ecm_customer->id} hidden{/if} {if $ecm_customer->id} disabled {/if} />
	<span class="for_email warning unvisible"></span>
	<span class="for_email msg unvisible"></span>
</div>
{if $authMethod > 0 && !$ecm_customer->id}
{$item = 'phone'}
<div class="required form-row {$item}-block frr-50">
		<input class="form-control login_ontype {$item} {$item}_login {if Configuration::Get('ecm_checkout_password2')}check-50{/if}" id="{$item}" act="save_login" placeholder="{l s='Login phone' mod='ecm_checkout'}"autocomplete="off" title = "{l s='Login phone' mod='ecm_checkout'}" value="{$ecm_customer->phone}" type="tel"required="required" />
		<span class="for_phone warning unvisible"></span>
		<span class="for_phone msg unvisible"></span>

		{if Configuration::Get('ecm_checkout_password2') }
		<input class="form-control {$item}2 login_ontype {$item}2_login {if Configuration::Get('ecm_checkout_password2')}check-50{/if}" id="{$item}2" act="save_login" placeholder="{l s='Retry phone' mod='ecm_checkout'}" autocomplete="off" title = "{l s='Retry phone' mod='ecm_checkout'}" value="{$ecm_customer->phone2}" type="tel" required="required" />
		{/if}
		
	</div>
{/if}

{if !$ecm_customer->id}
	{$item = 'password'}
	<div class="form-row {$item} {$item}-block frr-100" {if $sc_customer == "temp"}hidden{/if}>
	<input class="form-control pass_ontype {$item} {$item}_login  font-password {if Configuration::Get('ecm_checkout_password2')}check-50{/if}" {if $type_auth != "guest"}required="required"{/if}  autocomplete="off" title = "{l s='Password' mod='ecm_checkout'}" value="{$ecm_customer->password}" id="{$item}" type="text" act="save_login" placeholder="{l s='Password' mod='ecm_checkout'}"/>

	<span class="for_password warning unvisible"></span>
	<span class="for_password msg unvisible"></span>
	{if Configuration::Get('ecm_checkout_password2')}
	<input class="form-control pass_ontype {$item}2 {$item}2_login font-password login_ontype {if Configuration::Get('ecm_checkout_password2')}check-50{/if}" {if $type_auth != "guest"}required="required"{/if}  autocomplete="off" title = "{l s='Retry password' mod='ecm_checkout'}" value="{$ecm_customer->password2}" id="{$item}2" type="text" placeholder="{l s='Retry password' mod='ecm_checkout'}"/>
	{/if}
	</div>

	<div class="form-row button-block frr-100"  hidden>
		<button class="btn btn-primary" onclick="login()">
			<span>{l s='Sign in' mod='ecm_checkout'}</span>
		</button>
		<a class="btn btn-link" href="password-recovery" 
			title="{l s='Recover your forgotten password' mod='ecm_checkout'}" rel="nofollow">
			<span>{l s='Forgot the password?' mod='ecm_checkout'}</span>
		</a>
	</div>
	<div class="form-row alogin">&nbsp;</div>
{/if}
<div id="login_errors" class="form-row alert alert-warning login_errors" hidden></div>

{if !$ecm_customer->id}<hr class="separator">{/if}



{$item = 'id_country'}
<div class="form-row {$as[$item]['unvisible']}" {if count($country_list)<2}hidden{/if}>
        <select id="{$item}" name="{$item}" class="form-control form-control-select" title="{l s='Country' mod='ecm_checkout'}"
            onchange="action('save_country', $(this).val(), '{$item}')">
            {html_options options=$country_list selected=$sc_country}
        </select>
</div>




{$item = 'address_delivery'}
<div class="form-row" {if count($address_list)<2}hidden{/if}>
        <select id="{$item}" name="{$item}" class="form-control form-control-select" title="{l s='Address' mod='ecm_checkout'}" onchange="action('change_address', $(this).val(), '{$item}')"
		{if count($address_list)==1} disabled {/if}>
            {html_options options=$address_list selected=$sc_address_delivery}
        </select>
</div>

{if $authMethod > 0 && !$ecm_customer->id}
{else}
{$item = 'phone'}
<div class="form-row" {$as[$item]['unvisible']}>
        <input class="form-control customer_ontype phone"  {$as[$item]['required_input']}
		placeholder="{l s='Home phone' mod='ecm_checkout'}"
		title="{l s='Home phone' mod='ecm_checkout'}" {$as[$item]['required']}
        id="{$item}" name="{$item}" value="{$address->$item}" type="tel" act="save_address" placeholder="{l s='Home phone' mod='ecm_checkout'}">
</div>

{$item = 'phone_mobile'}
<div class="form-row" {$as[$item]['unvisible']} >
        <input class="form-control  customer_ontype phone" {$as[$item]['required_input']}
		placeholder="{l s='Mobile phone' mod='ecm_checkout'}"
        title = "{l s='Mobile phone' mod='ecm_checkout'}" {$as[$item]['required']}
        id="{$item}" name="{$item}" value="{$address->$item}" type="tel" act="save_address" placeholder="{l s='Mobile phone' mod='ecm_checkout'}"
        >
</div>
{/if}



{$item = 'firstname'}
<div class="form-row" {$as[$item]['unvisible']} >
        <input class="form-control customer_ontype" {$as[$item]['required_input']} {$as[$item]['unvisible']}
        title = "{l s='First name' mod='ecm_checkout'}" {$as[$item]['required']}
        id="{$item}" name="{$item}" value="{$address->$item}" type="text" act="save_address" 
		placeholder="{l s='First name' mod='ecm_checkout'}">
</div>

{$item = 'lastname'}
<div class="form-row" {$as[$item]['unvisible']} >
        <input class="form-control customer_ontype" {$as[$item]['required_input']} {$as[$item]['unvisible']}
        title = "{l s='Last name' mod='ecm_checkout'}" {$as[$item]['required']}
        id="{$item}" name="{$item}" value="{$address->$item}" type="text" act="save_address" 
		placeholder="{l s='Last name' mod='ecm_checkout'}">
</div>

{if Configuration::get('ecm_checkout_middlename')}
{$item = 'middlename'}
<div class="form-row" {$as[$item]['unvisible']} >
        <input class="form-control customer_ontype" {$as[$item]['required_input']} {$as[$item]['unvisible']}
        title = "{l s='Middle name' mod='ecm_checkout'}" {$as[$item]['required']}
        id="{$item}" name="{$item}" value="{$address->$item}" type="text" act="save_address" 
		placeholder="{l s='Middle name' mod='ecm_checkout'}">
</div>
{$item = 'secondname'}{if isset($address->$item) and isset($as[$item])}
<div class="form-row" {$as[$item]['unvisible']} >
        <input class="form-control customer_ontype" {$as[$item]['required_input']} {$as[$item]['unvisible']}
        title = "{l s='Middle name' mod='ecm_checkout'}" {$as[$item]['required']}
        id="{$item}" name="{$item}" value="{$address->$item}" type="text" act="save_address" 
		placeholder="{l s='Middle name' mod='ecm_checkout'}">
</div>{/if}
{/if}



{$item = 'id_state'}
<div class="form-row" {$as[$item]['unvisible']} {if !$country.contains_states}hidden{/if}>
        <select id="{$item}" name="{$item}" class="form-control form-control-select customer_ontype" act="save_address">
            <option value="">-</option>
            {html_options options=$state_list selected=$address->$item}
        </select>
</div>

{$item = 'city'}
<div class="form-row" {$as[$item]['unvisible']} >
        <input class="form-control customer_ontype" {$as[$item]['required_input']} {$as[$item]['unvisible']} maxlength="64" title="{l s='City' mod='ecm_checkout'}" {$as[$item]['required']} placeholder="{l s='City' mod='ecm_checkout'}"
        id="{$item}" name="{$item}" value="{$address->$item}" type="text" act="save_address">
</div>


{$item = 'postcode'}
<div class="form-row" {$as[$item]['unvisible']} >
		{assign var=zip_len value=strlen($country.zip_code_format)}
		{assign var=zip_pattern value='[0-9]{'|cat:{$zip_len}|cat:'}'}
        <input class="form-control text customer_ontype" {$as[$item]['required_input']} {$as[$item]['unvisible']}
		title="{l s='Zip/Postal Cod' mod='ecm_checkout'}" {$as[$item]['required']}
		placeholder="{l s='Zip/Postal Cod' mod='ecm_checkout'}"
        pattern="{$zip_pattern}" maxlength="{$zip_len}"
        id="{$item}" name="{$item}" value="{$address->$item}" type="text" act="save_address">
</div>

{$item = 'address1'}
<div class="form-row" {$as[$item]['unvisible']} >
        <input class="form-control customer_ontype" {$as[$item]['required_input']}  {$as[$item]['unvisible']}
		title="{l s='Address' mod='ecm_checkout'}" {$as[$item]['required']} placeholder="{l s='Address' mod='ecm_checkout'}"
        id="{$item}" name="{$item}" value="{$address->$item}" type="text" act="save_address">
</div>

{$item = 'address2'}
<div class="form-row" {$as[$item]['unvisible']} >
        <input class="form-control customer_ontype" {$as[$item]['required_input']} {$as[$item]['unvisible']}
		title="{l s='Address2' mod='ecm_checkout'}" {$as[$item]['required']} placeholder="{l s='Address2' mod='ecm_checkout'}"
        id="{$item}" name="{$item}" value="{$address->$item}" type="text" act="save_address">
</div>

{$item = 'other'}
<div class="form-row" {$as[$item]['unvisible']} >
        <textarea class="form-control customer_ontype"  {$as[$item]['required_input']}  {$as[$item]['unvisible']}
        cols="120" rows="2" id="{$item}" name="{$item}" act = "save_address">{strip}{if isset($address->$item)}{$address->$item|escape:'html':'UTF-8'}{/if}{/strip}</textarea>
</div>

{$item = 'vat_number'}
<div class="form-row" {$as[$item]['unvisible']} >
        <input class="form-control customer_ontype" {$as[$item]['required_input']} {$as[$item]['unvisible']}
		title="{l s='Vat number' mod='ecm_checkout'}"
		placeholder="{l s='Vat number' mod='ecm_checkout'}"
        id="{$item}" name="{$item}" value="{$address->$item}" type="text" act="save_address">
</div>

{$item = 'dni'}
<div class="form-row" {$as[$item]['unvisible']} >
        <input class="form-control customer_ontype" {$as[$item]['required_input']} {$as[$item]['unvisible']}
		title="{l s='DNI' mod='ecm_checkout'}"
		placeholder="{l s='DNI' mod='ecm_checkout'}"
        id="{$item}" name="{$item}" value="{$address->$item}" type="text" act="save_address">
</div>

{$item = 'company'}
<div class="form-row" {$as[$item]['unvisible']} >
        <input class="form-control customer_ontype" {$as[$item]['required_input']}  {$as[$item]['unvisible']}
		title="{l s='Company' mod='ecm_checkout'}"
		placeholder="{l s='Company' mod='ecm_checkout'}"
        id="{$item}" name="{$item}" value="{$address->$item}" type="text" act="save_address">
</div>
    

{$item = 'optin'}
<div class="form-row" {$as[$item]['unvisible']} >
	<span class="custom-checkbox">    
        <input class="customer_oncheck" {$as[$item]['unvisible']} id="{$item}" name="{$item}" type="checkbox" act="save_customer"
        {if $ecm_customer->$item == 1} checked="checked" {/if}>
		<span><i class="material-icons rtl-no-flip checkbox-checked">&#xE5CA;</i></span>
		<label class="{$as[$item]['required']}-after" for="{$item}">{l s='Subscribe to optin' mod='ecm_checkout'}</label>
	</span>
</div>

{$item = 'newsletter'}
<div class="form-row" {$as[$item]['unvisible']} >
     <span class="custom-checkbox">    
        <input class="customer_oncheck" {$as[$item]['unvisible']} id="{$item}" name="{$item}" type="checkbox" act="save_customer"
       {if $ecm_customer->$item == 1} checked="checked" {/if}>
   		<span><i class="material-icons rtl-no-flip checkbox-checked">&#xE5CA;</i></span>
		<label class="{$as[$item]['required']}-after" for="{$item}">{l s='Subscribe to newsletter' mod='ecm_checkout'}</label>
	</span>

</div>


</section>


<script>
document.addEventListener('DOMContentLoaded', function(){
	add_customer_event()
})    

if (typeof($)=='function'){
	$(document).ajaxComplete(function( event, xhr, settings) {
		add_customer_event()
	})
}

function add_customer_event(){

var type_auth = $('[name=type_auth]:checked').val();
var timeoutId;
var authMethod = {$authMethod|intval};
var sc_customer = '{$sc_customer}';

if(phone_mask) $('.phone').mask(phone_mask);												  
  
$("#ecm_checkout .customer_oncheck").off("change")
$("#ecm_checkout .customer_oncheck").on("change", function (){
	action($(this).attr('act'), 'check_'+$(this).is(':checked'), $(this).attr('id'), false);
})


$("#ecm_checkout .login_ontype").off("blur")
$("#ecm_checkout .login_ontype").on("blur", function (){
	if ($(this)[0].checkValidity()) {
		$(this).removeClass('sc-error').addClass('sc-ok');
	} else {
		$(this).removeClass('sc-ok').addClass('sc-error');
	}
	if (type_auth != 'login')
		delay(this,0,false);
})

$("#ecm_checkout .pass_ontype").off("blur")
$("#ecm_checkout .pass_ontype").on("blur", function (){
	if (type_auth != 'login') delay(this,0,false);
})

$("#ecm_checkout .login_ontype").off("input change")
$("#ecm_checkout .login_ontype").on("input change", function (){
	if ($(this)[0].checkValidity()) {
		$(this).removeClass('sc-error').addClass('sc-ok');
	} else {
		//$(this).removeClass('sc-ok').addClass('sc-error');
	}
	if (type_auth != 'login')
		delay(this,1000,true);
})

	$(".customer_ontype").off("blur")
	$(".customer_ontype").on("blur", function (){
	delay(this,0,false);
})

if (sc_customer == 'temp'){
	$.each($('.customer_ontype:required'), function(index, value) {
		$(value).removeAttr('required');
		$(value).removeClass('sc-error');
	});
	$('.login_ontype').removeAttr('required').removeClass('sc-error');
}

}
</script>