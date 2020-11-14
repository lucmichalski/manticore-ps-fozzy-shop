{*
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
*  @author    Yuri Denisov <contact@splashmart.ru>
*  @copyright 2014-2017 Yuri Denisov
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{if isset($ooc_order->ooc_cart->products) && $ooc_order->ooc_cart->products != ''}
    {foreach from=$ooc_order->ooc_cart->products item=product}
	<tr>
	    <td style="border: 1px solid #D6D4D4; font-family: Arial; color: #333; font-size: 11px; padding: 10px; text-align: center;">
		{if $product->combination}
		    {$product->combination->reference|escape:'html':'UTF-8'}
		{else}
		    {$product->product_object->reference|escape:'html':'UTF-8'}
		{/if}
	    </td>
	    <td style="border: 1px solid #D6D4D4; font-family: Arial; color: #333; font-size: 11px; padding: 10px;">
		<p style="font-size: 12px">
		    <strong>{$product->product_object->name.$id_lang|escape:'html':'UTF-8'}</strong>
		</p>
		{if ($product->combination)}
		    <p>
			<div><strong>{l s='Combination:' mod='orderinoneclick'}</strong></div>
			{assign var=attributes value=$product->combination->getAttributesName($id_lang)}
			{foreach from=$attributes key=key item=attribute}
			    <div>{$attribute.name|escape:'html':'UTF-8'}</div>
			{/foreach}
		    </p>
		{/if}
		{if ($product->customization)}
		    <p>
			{l s='Was used customization' mod='orderinoneclick'}
		    <p>
		{/if}
	    </td>
	    <td style="border: 1px solid #D6D4D4; font-family: Arial; color: #333; font-size: 11px; padding: 10px; text-align: center;">
                {Tools::displayPrice($product->price)}
	    </td>
	    <td style="border: 1px solid #D6D4D4; font-family: Arial; color: #333; font-size: 11px; padding: 10px; text-align: center;">{$product->quantity|escape:'html':'UTF-8'}</td>
	    <td style="border: 1px solid #D6D4D4; font-family: Arial; color: #333; font-size: 11px; padding: 10px; text-align: center;">
                {Tools::displayPrice($product->price*$product->quantity)}
	    </td>
	</tr>
    {/foreach}
    {if $ooc_order->ooc_cart->total_discount > 0}
	<tr style="background-color: #fbfbfb; border: 1px solid #D6D4D4; font-family: Arial; color: #333; font-size: 11px; padding: 10px; text-align: center;">
	    <td colspan="3" rowspan="3"></td>
	    <td style="border: 1px solid #D6D4D4; font-family: Arial; color: #333; font-size: 11px; padding: 10px; text-align: center;">{l s='Without discount' mod='orderinoneclick'}</td>
	    <td style="border: 1px solid #D6D4D4; font-family: Arial; color: #333; font-size: 11px; padding: 10px; text-align: center;">
                {Tools::displayPrice($ooc_order->ooc_cart->order_price + $ooc_order->ooc_cart->total_discount)}
	    </td>
	</tr>
	<tr style="background-color: #fbfbfb; border: 1px solid #D6D4D4; font-family: Arial; color: #333; font-size: 11px; padding: 10px; text-align: center;">
	    <td style="border: 1px solid #D6D4D4; font-family: Arial; color: #333; font-size: 11px; padding: 10px; text-align: center;">
		{l s='Discount' mod='orderinoneclick'}
	    </td>
	    <td style="border: 1px solid #D6D4D4; font-family: Arial; color: #333; font-size: 11px; padding: 10px; text-align: center;">
                {Tools::displayPrice($ooc_order->ooc_cart->total_discount)}
	    </td>
	</tr>
    {/if}
    <tr style="background-color: #fbfbfb; border: 1px solid #D6D4D4; font-family: Arial; color: #333; font-size: 11px; padding: 10px; text-align: center;">
	{if $ooc_order->ooc_cart->total_discount == 0}
	    <td colspan="3"></td>
	{/if}
	<td style="font-weight: bold; border: 1px solid #D6D4D4; font-family: Arial; color: #333; font-size: 13px; padding: 10px; text-align: center;">{l s='TOTAL' mod='orderinoneclick'}</td>
	<td style="font-weight: bold; border: 1px solid #D6D4D4; font-family: Arial; color: #333; font-size: 13px; padding: 10px; text-align: center;">
            {Tools::displayPrice($ooc_order->ooc_cart->order_price)}
	</td>
    </tr>
{/if}
