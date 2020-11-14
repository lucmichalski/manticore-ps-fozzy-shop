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

{if $status == 'ok'}

<div class="box" style="max-width:400px;margin:0 auto;">
  <h1 class="page-heading" style="    text-transform: none;">{l s='Thank you for your order' mod='ecm_checkout'}</h1>
	<p>
		{if !isset($order->reference)}
			<br />{l s='Your order number' mod='ecm_checkout'}: {$order->id}
		{else}
			<br />{l s='Your order reference' mod='ecm_checkout'}: {$order->reference}
		{/if}
		<br />{l s='Payment' mod='ecm_checkout'}: <strong>{$order->payment}</strong>
		<br />{l s='Shipping' mod='ecm_checkout'}: <strong>{$carrier->name}</strong>
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
{/block}
