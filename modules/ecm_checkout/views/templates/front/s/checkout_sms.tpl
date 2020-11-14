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


<div class="box">
<h1 class="page-subheading">{l s='Recovery your password' mod='ecm_simcheck'}</h1>

{if isset($errors) && $errors}
	<div class="alert alert-danger">
		<ol>
		{foreach from=$errors key=k item=error}
			<li><p>{$error}</p></li>
		{/foreach}
		<ol>
	</div>
{/if}
{if $sms_pass_done}
<p>{l s='Your new password success send to your phone' mod='ecm_simcheck'}</p>
{else}
<p>{l s='Please enter the code from sms' mod='ecm_simcheck'}</p>
<form action="{$request_uri|escape:'html':'UTF-8'}" method="post" class="std" id="">
	<fieldset>
		<div class="form-group">
			<label for="code">{l s='Code' mod='ecm_simcheck'}</label>
			<input class="form-control" value="" id="code" name="code" type="tel" style="width: 263px;" autofocus autocomplete="off"/>
		</div>
		<p class="submit">
            <button {if $enable_button}type="submit"{else}disabled{/if} name="VerifyCode"
            class="btn btn-default button button-medium"><span>{l s='Verify code' mod='ecm_simcheck'}
            <i class="icon-chevron-right right"></i></span>
            </button>
		</p>
	</fieldset>
</form>
{/if}
</div>


<ul class="clearfix footer_links">
	<li>
	<a class="btn btn-default button button-small" href="{$link->getPageLink('authentication')|escape:'html':'UTF-8'}" 
	title="{l s='Back to Login'}" rel="nofollow">
	<span><i class="icon-chevron-left"></i>{l s='Back to Login' mod='ecm_simcheck'}</span></a></li>
</ul>
