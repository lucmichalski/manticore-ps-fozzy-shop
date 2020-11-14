{**
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * We are experts and professionals in PrestaShop
 *
 * @author    Elcommerce support@elcommece.com.ua
 * @copyright 2010-2018 Elcommerce
 * @license   Comercial
 * @category  PrestaShop
 * @category  Module
*}

{*$sc_customer*}
<div class="card-block"><h5 class="h5"><i class="fa fa-user"></i> 
{l s='About customer' mod='ecm_checkout'}</h5></div>
<hr class="separator">

<section class="card-block sc_customer">

{if !Configuration::Get('PS_GUEST_CHECKOUT_ENABLED') && $type_auth == 'guest'}{$type_auth = 'registration'}{/if}

{if !$customer->id}
<div class="form-row">
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

<div class="form-row alert alert-warning login_errors" hidden></div>

{*if $authMethod == 2 && !$customer->id}
<label id="authMethod2" class="warning unvisible">{l s='Please enter phone or E-mail (optionaly)' mod='ecm_checkout'}</label>
{/if*}

{if $authMethod > 0 && !$customer->id}
{$item = 'phone'}
<div class="required form-row">
        <label id="authMethod2" class="required-after">{l s='Mobile phone' mod='ecm_checkout'}</label>
        <input class="form-control login_ontype phone"
        id="{$item}" name="{$item}" value="{$customer->$item}" type="tel" act="save_login"
		title="{l s='Login phone' mod='ecm_checkout'}"
        />
		<span class="warning unvisible"></span>
		<span class="unvisible"></span>
</div>
{/if}


<div class="required form-row"  >
        <label for="email" class="login_ontype email{if $authMethod == 0} required-after{/if}">{l s='E-mail' mod='ecm_checkout'}</label>
       <input class="form-control login_ontype email"  {if $authMethod == 0}required{/if}
		title="{l s='E-mail' mod='ecm_checkout'}"
        name="email" id="email" value="{$customer->email}" type="email" act="save_login"
		{if $customer->id} disabled {/if} >
		<span class="warning unvisible"></span>
		<span class="unvisible"></span>
</div>


{if !$customer->id}
{$item = 'password'}
<div class="form-row {$item}" {if $type_auth == "guest"}hidden{/if} {if $sc_customer == "temp"}hidden{/if}>
		<label class="">{l s='Password' mod='ecm_checkout'}</label>
		<input class="form-control login_ontype"
        title = "{l s='Password' mod='ecm_checkout'}"
        id="{$item}" name="{$item}" type="password" act="save_address" 
        />
</div>

<div class="form-row login" {if $type_auth != "login"}hidden{/if} >
		<button class="btn btn-info button-small mt-1" onclick="login()">
			<span>{l s='Sign in' mod='ecm_checkout'}</span>
		</button>
		<a href="password-recovery" title="{l s='Recover your forgotten password' mod='ecm_checkout'}" rel="nofollow"
		style="vertical-align: bottom;">
			{l s='Forgot the password?' mod='ecm_checkout'}
		</a>
</div>

<hr>
{/if}

{if $authMethod == 0 or $customer->id}
{$item = 'phone'}
<div class="form-row" {$as[$item]['unvisible']}>
        <label for="phone" class="{$as[$item]['required']}-after">{l s='Home phone' mod='ecm_checkout'}</label>
        <input class="form-control customer_ontype"  {$as[$item]['required_input']}
		title="{l s='Home phone' mod='ecm_checkout'}"
        id="{$item}" name="{$item}" value="{$address->$item}" type="tel" act="save_address">
</div>

{$item = 'phone_mobile'}
<div class="form-row" {$as[$item]['unvisible']} >
        <label class="{$as[$item]['required']}-after">{l s='Mobile phone' mod='ecm_checkout'}</label>
        <input class="form-control  customer_ontype" {$as[$item]['required_input']}
        title = "{l s='Mobile phone' mod='ecm_checkout'}"
        id="{$item}" name="{$item}" value="{$address->$item}" type="tel" act="save_address"
        >
</div>
{/if}
    


{$item = 'id_country'}
<div class="form-row {$as[$item]['unvisible']}" {if count($country_list)<2}hidden{/if}>
        <label class="{$as[$item]['required']}-after">{l s='Country' mod='ecm_checkout'}</label>
        <select id="{$item}" name="{$item}" class="form-control form-control-select" 
            onchange="action('save_country,{$renderSeq}', $(this).val(), '{$item}')">
            {html_options options=$country_list selected=$sc_country}
        </select>
</div>
    
{$item = 'address_delivery'}
<div class="form-row" {if count($address_list)<2}hidden{/if}>
        <label class="">{l s='Address' mod='ecm_checkout'}</label>
        <select id="{$item}" name="{$item}" class="form-control" onchange="action('change_address,{$renderSeq}', $(this).val(), '{$item}')"
		{if count($address_list)==1} disabled {/if}>
            {html_options options=$address_list selected=$sc_address_delivery}
        </select>
</div>








{$item = 'firstname'}
<div class="form-row" {$as[$item]['unvisible']} >
        <label class="{$as[$item]['required']}-after">{l s='First name' mod='ecm_checkout'}</label>
        <input class="form-control customer_ontype" {$as[$item]['required_input']}
        title = "{l s='First name' mod='ecm_checkout'}"
        id="{$item}" name="{$item}" value="{$address->$item}" type="text" act="save_address">
</div>

{$item = 'lastname'}
<div class="form-row" {$as[$item]['unvisible']} >
        <label class="{$as[$item]['required']}-after">{l s='Last name' mod='ecm_checkout'}</label>
        <input class="form-control customer_ontype" {$as[$item]['required_input']} {$as[$item]['unvisible']}
        title = "{l s='Last name' mod='ecm_checkout'}"
        id="{$item}" name="{$item}" value="{$address->$item}" type="text" act="save_address">
</div>
    
{if Configuration::get('ecm_checkout_middlename')}
{$item = 'middlename'}
<div class="form-row" {$as[$item]['unvisible']} >
        <label class="{$as[$item]['required']}-after">{l s='Middle name' mod='ecm_checkout'}</label>
        <input class="form-control customer_ontype" {$as[$item]['required_input']}
        title = "{l s='Middle name' mod='ecm_checkout'}"
        id="{$item}" name="{$item}" value="{$address->$item}" type="text" act="save_address">
</div>
{/if}
    
{$item = 'id_state'}
<div class="form-row" {$as[$item]['unvisible']} {if !$country.contains_states}hidden{/if}>
        <label class="{$as[$item]['required']}-after">{l s='State' mod='ecm_checkout'}</label>
        <select id="{$item}" name="{$item}" class="form-control form-control-select customer_ontype" act="save_address">
            <option value="">-</option>
            {html_options options=$state_list selected=$address->id_state}
        </select>
</div>
    
{$item = 'city'}
<div class="form-row" {$as[$item]['unvisible']} >
        <label class="{$as[$item]['required']}-after">{l s='City' mod='ecm_checkout'}</label>
        <input class="form-control customer_ontype" {$as[$item]['required_input']} maxlength="64"
		title="{l s='City' mod='ecm_checkout'}"
        id="{$item}" name="{$item}" value="{$address->$item}" type="text" act="save_address">
</div>
    
    
{$item = 'postcode'}
<div class="form-row" {$as[$item]['unvisible']} >
        <label class="{$as[$item]['required']}-after">{l s='Zip/Postal Code' mod='ecm_checkout'}</label>
		{assign var=zip_len value=strlen($country.zip_code_format)}
		{assign var=zip_pattern value='[0-9]{'|cat:{$zip_len}|cat:'}'}
        <input class="form-control text customer_ontype" {$as[$item]['required_input']} 
		title="{l s='Zip/Postal Code' mod='ecm_checkout'}"
        pattern="{$zip_pattern}" maxlength="{$zip_len}"
        id="{$item}" name="{$item}" value="{$address->$item}" type="text" act="save_address">
</div>
    
{$item = 'address1'}
<div class="form-row" {$as[$item]['unvisible']} >
        <label class="{$as[$item]['required']}-after">{l s='Address' mod='ecm_checkout'}</label>
        <input class="form-control customer_ontype" {$as[$item]['required_input']}  {$as[$item]['unvisible']}
		title="{l s='Address' mod='ecm_checkout'}"
        id="{$item}" name="{$item}" value="{$address->$item}" type="text" act="save_address">
</div>

{$item = 'address2'}
<div class="form-row" {$as[$item]['unvisible']} >
        <label class="{$as[$item]['required']}-after">{l s='Address2' mod='ecm_checkout'}</label>
        <input class="form-control customer_ontype" {$as[$item]['required_input']}
		title="{l s='Address2' mod='ecm_checkout'}"
        id="{$item}" name="{$item}" value="{$address->$item}" type="text" act="save_address">
</div>

{$item = 'other'}
<div class="form-row" {$as[$item]['unvisible']} >
        <label class="{$as[$item]['required']}-after">{l s='Other' mod='ecm_checkout'}</label>
        <textarea class="form-control customer_ontype"  {$as[$item]['required_input']}
        cols="120" rows="2" id="{$item}" name="{$item}" act = "save_address">{strip}{if isset($address->$item)}{$address->$item|escape:'html':'UTF-8'}{/if}{/strip}</textarea>
</div>

{$item = 'vat_number'}
<div class="form-row" {$as[$item]['unvisible']} >
        <label class="{$as[$item]['required']}-after">{l s='Vat number' mod='ecm_checkout'}</label>
        <input class="form-control customer_ontype" {$as[$item]['required_input']}
		title="{l s='Vat number' mod='ecm_checkout'}"
        id="{$item}" name="{$item}" value="{$address->$item}" type="text" act="save_address">
</div>

{$item = 'dni'}
<div class="form-row" {$as[$item]['unvisible']} >
        <label class="{$as[$item]['required']}-after">{l s='DNI' mod='ecm_checkout'}</label>
        <input class="form-control customer_ontype" {$as[$item]['required_input']}
		title="{l s='DNI' mod='ecm_checkout'}"
        id="{$item}" name="{$item}" value="{$address->$item}" type="text" act="save_address">
</div>

{$item = 'company'}
<div class="form-row" {$as[$item]['unvisible']} >
        <label class="{$as[$item]['required']}-after">{l s='Company' mod='ecm_checkout'}</label>
        <input class="form-control customer_ontype" {$as[$item]['required_input']}
		title="{l s='Company' mod='ecm_checkout'}"
        id="{$item}" name="{$item}" value="{$address->$item}" type="text" act="save_address">
</div>
    

{$item = 'optin'}
<div class="form-row" {$as[$item]['unvisible']} >
	<span class="custom-checkbox">    
        <input class="customer_oncheck" id="{$item}" name="{$item}" type="checkbox" act="save_customer"
        {if $customer->$item == 1} checked="checked" {/if}>
		<span>
			<i class="material-icons rtl-no-flip checkbox-checked">
				&#xE5CA;
			</i>
		</span>
		<label class="{$as[$item]['required']}-after" for="{$item}">{l s='Subscribe to optin' mod='ecm_checkout'}</label>
	</span>
</div>

{$item = 'newsletter'}
<div class="form-row" {$as[$item]['unvisible']} >
     <span class="custom-checkbox">    
        <input class="customer_oncheck" id="{$item}" name="{$item}" type="checkbox" act="save_customer"
       {if $customer->$item == 1} checked="checked" {/if}>
   		<span>
			<i class="material-icons rtl-no-flip checkbox-checked">
				&#xE5CA;
			</i>
		</span>
		<label class="{$as[$item]['required']}-after" for="{$item}">{l s='Subscribe to newsletter' mod='ecm_checkout'}</label>
	</span>

</div>



</section>


<script>
var type_auth = $('[name=type_auth]:checked').val();
var cart_qties = {$cart_qties};
var id_cart = {$id_cart|intval};
var timeoutId;
var authMethod = {$authMethod|intval};
var sc_customer = '{$sc_customer}';

if(phone_mask) $('.phone').mask(phone_mask);												  
  
$("#ecm_checkout .customer_oncheck").on("change", function (){
	action($(this).attr('act'), 'check_'+$(this).is(':checked'), $(this).attr('id'), false);
})


$("#ecm_checkout .login_ontype").on("blur", function (){
	if ($(this)[0].checkValidity()) {
		$(this).removeClass('sc-error').addClass('sc-ok');
	} else {
		$(this).removeClass('sc-ok').addClass('sc-error');
	}
	if (type_auth != 'login')
		delay(this,0,false);
})

$("#ecm_checkout .pass_ontype").on("blur", function (){
	if (type_auth != 'login') delay(this,0,false);
})

$("#ecm_checkout .login_ontype").on("input change", function (){
	if ($(this)[0].checkValidity()) {
		$(this).removeClass('sc-error').addClass('sc-ok');
	} else {
		//$(this).removeClass('sc-ok').addClass('sc-error');
	}
	if (type_auth != 'login')
		delay(this,1000,true);
})

	$(".customer_ontype").on("blur", function (){
	delay(this,0,false);
})

if (type_auth){
	check_auth();
	$('#email').trigger('input');
}

if (sc_customer == 'temp'){
	$.each($('.customer_ontype:required'), function(index, value) {
		$(value).removeAttr('required');
		$(value).removeClass('sc-error');
	});
	$('.login_ontype').removeAttr('required').removeClass('sc-error');
}
</script>