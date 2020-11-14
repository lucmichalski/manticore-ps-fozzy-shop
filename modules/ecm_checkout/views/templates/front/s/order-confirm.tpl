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

{extends file=$layout}

{block name='content'}
<div class="card">
<div class="card-block">
<h1 class="page-heading">{l s='Thank you, we finished' mod='ecm_checkout'} !!!</h1>
</div>
<hr class="separator">
<div class="card-block">
{if $status == 'ok'}
<div class="box">
	<h5>{l s='Your order is complete.' mod='ecm_checkout'}</h5>
	<p>
		{if !isset($order->reference)}
			{l s='Your order number' mod='ecm_checkout'}: <strong>{$order->id}</strong>
		{else}
			{l s='Your order reference' mod='ecm_checkout'}: <strong>{$order->reference}</strong>
		{/if}
		<br />{l s='Payment' mod='ecm_checkout'}: <strong>{$order->payment}</strong>
		<br />{l s='Shipping' mod='ecm_checkout'}: <strong>{$carrier->name}</strong>
		<br />{l s='An email has been sent to you with this information.' mod='ecm_checkout'}
		<br /><strong>{l s='Your order will be sent as soon as we receive your payment.' mod='ecm_checkout'}</strong>
		<br />{l s='For any questions or for further information, please contact our' mod='ecm_checkout'} <a href="{$link->getPageLink('contact', true)|escape:'html'}">{l s='customer service department.' mod='ecm_checkout'}</a>.
	</p>
</div>
{else}
<div class="box">
	<p class="warning">
		{l s='We have noticed that there is a problem with your order. If you think this is an error, you can contact our' mod='ecm_checkout'} 
		<a href="{$link->getPageLink('contact', true)|escape:'html'}">
		{l s='customer service department.' mod='ecm_checkout'}</a>.
	</p>
</div>
{/if}
</div>
</div>
{/block}