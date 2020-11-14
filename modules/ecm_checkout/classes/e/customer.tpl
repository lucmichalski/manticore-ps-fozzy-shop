{**
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * We are experts and professionals in PrestaShop
 *
 * @author    Elcommerce <support@elcommece.com.ua>
 * @copyright 2010-2018 Elcommerce
 * @license   Comercial
 * @category  PrestaShop
 * @category  Module
*}

{$renrer_address=''}
{if Configuration::get('ecm_simcheck_strict_auth')}{$renrer_address=',customer'}{/if}

{if !$is_logged}{include file="./login.tpl"}{/if}


<label class="sc-label"><i class="icon icon-user"></i> {l s='About customer' mod='ecm_simcheck'}
	{if !$is_logged}
	<a class="button btn btn-default button-small pull-right" onclick = "to_login()">
	<span>{l s='Are you already with us?' mod='ecm_simcheck'}</span>
	</a>
	{/if}
</label>
<div class="table_block table-responsive">
<table class="table"><tr><td>

{$item = 'id_country'}
<div class="form-group {$as[$item]['unvisible']} {if count($country_list) < 2}unvisible{/if}">
    <div class="form-row">
        <label class="{$as[$item]['required']}-after">{l s='Country' mod='ecm_simcheck'}</label>
        <select id="{$item}" name="{$item}" class="form-control" onchange="action('save_country,{$renderSeq}', $(this).val(), '{$item}')">
            {html_options options=$country_list selected=$address->$item}
        </select>
    </div>
</div>
    
{$item = 'address_delivery'}
<div class="form-group {if count($address_collection)<2}unvisible{/if}">
    <div class="form-row">
        <label class="">{l s='Address' mod='ecm_simcheck'}</label>
        <select id="{$item}" name="{$item}" class="form-control" onchange="action('change_address,{$renderSeq}', $(this).val(), '{$item}')"
		{if count($address_collection)==1} disabled {/if}>
            {html_options options=$address_list selected=$cart->id_address_delivery}
        </select>
    </div>
</div>
{if $authMethod != 1}
<div class="form-group {if $authMethod == 1}unvisible{/if}">
    <div class="form-row ">
        <label for="email" class="">{l s='E-mail' mod='ecm_simcheck'}</label>
        {assign var=email_pattern value='^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,11}$'}
		<input class="form-control login_ontype email"  {if $authMethod != 2}{/if}
		{*	pattern="{$email_pattern}" *}
		title="{l s='E-mail' mod='ecm_simcheck'}"
        name="email" id="email" value="{$customer->email}" type="email" act="save_login{$renrer_address}"
		{if $cart->id_customer} disabled {/if} />
		<span class="warning unvisible"></span>
		<span class="unvisible"></span>
    </div>
</div>
{/if}

{if $authMethod > 0 && !$cart->id_customer}
{$item = 'phone'}
<div class="required form-group">
    <div class="form-row ">
        <label id="authMethod2" class="warning unvisible">{l s='Please enter phone and E-mail (optionaly)' mod='ecm_simcheck'}</label>
        <label for="phone" class="required-after">{l s='Login phone' mod='ecm_simcheck'}</label>
        <input class="form-control login_ontype phone" 
		title="{l s='Login phone' mod='ecm_simcheck'}"
        id="{$item}" name="{$item}" value="{$customer->$item}" type="tel" act="save_login{$renrer_address}"
        {if !$cookie->exist_customer}required{/if}
        />
   		<span class="warning unvisible"></span>
    </div>
</div>

{else}
{$item = 'phone'}
<div class="form-group {$as[$item]['unvisible']}">
    <div class="form-row ">
        <label for="phone" class="{$as[$item]['required']}-after">{l s='Home phone' mod='ecm_simcheck'}</label>
        <input class="form-control customer_ontype phone"  {$as[$item]['required_input']}
		title="{l s='Home phone' mod='ecm_simcheck'}"
        id="{$item}" name="{$item}" value="{$address->$item}" type="tel" act="save_address"/>
    </div>
</div>

{$item = 'phone_mobile'}
<div class="form-group {$as[$item]['unvisible']}">
    <div class="form-row ">
        <label class="{$as[$item]['required']}-after">{l s='Mobile phone' mod='ecm_simcheck'}</label>
        <input class="form-control  customer_ontype phone" {$as[$item]['required_input']}
		title="{l s='Mobile phone' mod='ecm_simcheck'}"
        id="{$item}" name="{$item}" value="{$address->$item}" type="tel" act="save_address"
        {*pattern="\+?[38]?8?0\d\d ?-?\d\d\d ?-?\d\d ?-?\d\d"*}  
        />
    </div>
</div>
{/if}
    
	
	
{$item = 'company'}
<div class="form-group {$as[$item]['unvisible']}">
    <div class="form-row ">
        <label class="{$as[$item]['required']}-after">{l s='Company' mod='ecm_simcheck'}</label>
        <input class="form-control customer_ontype" {$as[$item]['required_input']}
		title="{l s='Company' mod='ecm_simcheck'}"
        id="{$item}" name="{$item}" value="{$address->$item}" type="text" act="save_address"/>
    </div>
</div>
    
{$item = 'firstname'}
<div class="form-group {$as[$item]['unvisible']}">
    <div class="form-row ">
        <label class="{$as[$item]['required']}-after">{l s='First name' mod='ecm_simcheck'}</label>
        <input class="form-control customer_ontype" {$as[$item]['required_input']}
		title="{l s='First name' mod='ecm_simcheck'}"
        id="{$item}" name="{$item}" value="{$address->$item}" type="text" act="save_address"/>
    </div>
</div>
    
{$item = 'lastname'}
<div class="form-group {$as[$item]['unvisible']}">
    <div class="form-row">
        <label class="{$as[$item]['required']}-after">{l s='Last name' mod='ecm_simcheck'}</label>
        <input class="form-control customer_ontype" {$as[$item]['required_input']} {$as[$item]['unvisible']}
		title="{l s='Last name' mod='ecm_simcheck'}"
        id="{$item}" name="{$item}" value="{$address->$item}" type="text" act="save_address"/>
	</div>
</div>
    
{if Configuration::get('ecm_simcheck_middlename')}
{$item = 'middlename'}
<div class="form-group {$as[$item]['unvisible']}">
    <div class="form-row">
        <label class="{$as[$item]['required']}-after">{l s='Middle name' mod='ecm_simcheck'}</label>
        <input class="form-control customer_ontype" {$as[$item]['required_input']}
		title="{l s='Middle name' mod='ecm_simcheck'}"
        id="{$item}" name="{$item}" value="{$address->$item}" type="text" act="save_address"/>
	</div>
</div>
{/if}
    
{$item = 'id_state'}
<div class="form-group {$as[$item]['unvisible']} {if !$country.contains_states}unvisible{/if}">
    <div class="form-row">
        <label class="{$as[$item]['required']}-after">{l s='State' mod='ecm_simcheck'}</label>
        <select id="{$item}" name="{$item}" class="form-control customer_ontype" act="save_address">
            <option value="">-</option>
            {html_options options=$state_list selected=$address->id_state}
        </select>
    </div>
</div>
    
{$item = 'city'}
<div class="form-group {$as[$item]['unvisible']}">
    <div class="form-row">
        <label class="{$as[$item]['required']}-after">{l s='City' mod='ecm_simcheck'}</label>
        <input class="form-control customer_ontype" {$as[$item]['required_input']} maxlength="64"
		title="{l s='City' mod='ecm_simcheck'}"
        id="{$item}" name="{$item}" value="{$address->$item}" type="text" act="save_address"/>
    </div>
</div>
    
    
{$item = 'postcode'}
<div class="form-group {$as[$item]['unvisible']}">
    <div class="form-row">
        <label class="{$as[$item]['required']}-after">{l s='Zip/Postal Code' mod='ecm_simcheck'}</label>
		{assign var=zip_len value=strlen($country.zip_code_format)}
		{assign var=zip_pattern value='[0-9]{'|cat:{$zip_len}|cat:'}'}
        <input class="form-control text customer_ontype" {$as[$item]['required_input']} 
		title="{l s='Zip/Postal Code' mod='ecm_simcheck'}"
        pattern="{$zip_pattern}" maxlength="{$zip_len}"
        id="{$item}" name="{$item}" value="{$address->$item}" type="text" act="save_address"/>
    </div>
</div>
    
{$item = 'address1'}
<div class="form-group {$as[$item]['unvisible']}">
    <div class="form-row">
        <label class="{$as[$item]['required']}-after">{l s='Address' mod='ecm_simcheck'}</label>
        <input class="form-control customer_ontype" {$as[$item]['required_input']}  {$as[$item]['unvisible']}
		title="{l s='Address' mod='ecm_simcheck'}"
        id="{$item}" name="{$item}" value="{$address->$item}" type="text" act="save_address"/>
    </div>
</div>

{$item = 'address2'}
<div class="form-group {$as[$item]['unvisible']}">
    <div class="form-row">
        <label class="{$as[$item]['required']}-after">{l s='Address2' mod='ecm_simcheck'}</label>
        <input class="form-control customer_ontype" {$as[$item]['required_input']}
		title="{l s='Address2' mod='ecm_simcheck'}"
        id="{$item}" name="{$item}" value="{$address->$item}" type="text" act="save_address"/>
    </div>
</div>

{$item = 'other'}
<div class="form-group {$as[$item]['unvisible']}">
    <div class="form-row">
        <label class="{$as[$item]['required']}-after">{l s='Other' mod='ecm_simcheck'}</label>
        <textarea class="form-control customer_ontype"  {$as[$item]['required_input']}
        cols="120" rows="2" id="{$item}" name="{$item}" act = "save_address">{strip}{if isset($address->$item)}{$address->$item|escape:'html':'UTF-8'}{/if}{/strip}</textarea>
    </div>
</div>

{$item = 'vat_number'}
<div class="form-group {$as[$item]['unvisible']}">
    <div class="form-row">
        <label class="{$as[$item]['required']}-after">{l s='Vat number' mod='ecm_simcheck'}</label>
        <input class="form-control customer_ontype" {$as[$item]['required_input']}
		title="{l s='Vat number' mod='ecm_simcheck'}"
        id="{$item}" name="{$item}" value="{$address->$item}" type="text" act="save_address"/>
    </div>
</div>

{$item = 'dni'}
<div class="form-group {$as[$item]['unvisible']}">
    <div class="form-row">
        <label class="{$as[$item]['required']}-after">{l s='DNI' mod='ecm_simcheck'}</label>
        <input class="form-control customer_ontype" {$as[$item]['required_input']}
		title="{l s='DNI' mod='ecm_simcheck'}"
        id="{$item}" name="{$item}" value="{$address->$item}" type="text" act="save_address"/>
    </div>
</div>

{if !$cart->id_customer}
{$item = 'optin'}
<div class="form-group {$as[$item]['unvisible']}">
    <div class="form-row checkbox">
        <input class="oncheck" id="{$item}" name="{$item}" 
		type="checkbox" act="save_customer" value="1"
        {if $customer->$item == 1} checked="checked" {/if} />
        <label>{l s='Subscribe to optin' mod='ecm_simcheck'}</label>
    </div>
</div>

{$item = 'newsletter'}
<div class="form-group {$as[$item]['unvisible']}">
    <div class="form-row checkbox">
        <input class="oncheck" id="{$item}" name="{$item}" 
		type="checkbox" act="save_customer" value="1"
       {if $customer->$item == 1} checked="checked" {/if} />
        <label>{l s='Subscribe to newsletter' mod='ecm_simcheck'}</label>
    </div>
</div>
{/if}

</td></tr></table>    
</div>

<script>

var cart_qties = {$cart_qties};
var cart = {$cart|@json_encode};
var timeoutId;
var authMethod = {$authMethod};

$('.phone').mask('+38(999)-999-99-99');

$(".oncheck").on("click", function (){
	action($(this).attr('act'), 'check_'+$(this).is(':checked'), $(this).attr('id'), false);
})

$.each($('input:required'), function( index, val ) {
	if ($(val)[0].checkValidity()) {
		$(val).removeClass('sc-error').addClass('sc-ok');
	} else {
		$(val).removeClass('sc-ok').addClass('sc-error');
	}
})

$(".login_ontype").on("blur", function (){
	delay(this,0,false);
})

$(".login_ontype").on("input change", function (){
	delay(this,1000,true);
})

$(".customer_ontype").on("blur", function (){
	delay(this,0,false);
})

</script>
