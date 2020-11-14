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

{if Configuration::Get('ecm_gre_active')}
<script>
var gre_products=[]
var gre_products_references='';
var gre_products_prices='';
</script>
{/if}

{hook h="displayBeforeShoppingCartBlock"} 
<div id="order-detail-content" class="table_block table-responsive">
	<table id="cart_summary" class="table _table-bordered">
		<thead>
			<tr>
				<th></th>
				<th class="cart_product first_item" colspan="1">{l s='Product' mod='ecm_simcheck'}</th>
				<th class="cart_unit text-right">{l s='Unit price' mod='ecm_simcheck'}</th>
				<th class="cart_quantity text-center">{l s='Qty' mod='ecm_simcheck'}</th>
				<th colspan="2" class="cart_total text-right">{l s='Total' mod='ecm_simcheck'}</th>
			</tr>
		</thead>
		<tfoot>
			{if $use_taxes}
				{if $priceDisplay}
					<tr class="cart_total_price">
						<td colspan="2" rowspan="2" id="cart_voucher" class="align-bottom"></td>
						<td colspan="3" class="text-right">{if $display_tax_label}{l s='Total products (tax excl.)' mod='ecm_simcheck'}{else}{l s='Total products' mod='ecm_simcheck'}{/if}</td>
						<td colspan="1" class="price text-right" id="total_product"><span{if $msg} class="warning"{/if}>{displayPrice price=$total_products}</span></td>
					</tr>
					<tr>
						<td colspan="4">{if $msg}<div class="text-right">{$msg}</div>{/if}</td>
					</tr>
				{else}
					<tr class="cart_total_price">
						<td colspan="2" rowspan="2" id="cart_voucher" class="align-bottom"></td>
						<td colspan="3" class="text-right">{l s='Total products' mod='ecm_simcheck'}</td>
						<td colspan="1" class="price text-right" id="total_product"> <span{if $msg} class="warning"{/if}>{displayPrice price=$total_products_wt}</span></td>
					</tr>
					{if $msg}
					<tr>
						<td colspan="4"><div class="text-right">{$msg}</div></td>
					</tr>
					{/if}
				{/if}
			{else}
				<tr class="cart_total_price">
					<td colspan="2"  rowspan="2" id="cart_voucher" class="align-bottom"></td>
					<td colspan="3" class="text-right">{l s='Total products' mod='ecm_simcheck'}</td>
					<td colspan="1" class="price text-right" id="total_product"><span{if $msg} class="warning"{/if}>{displayPrice price=$total_products}</span></td>
				</tr>
				<tr>
					<td colspan="4">{if $msg}<div class="text-right">{$msg}</div>{/if}</td>
				</tr>
			{/if}
			

				
		</tfoot>
		<tbody>
			{assign var='odd' value=0}
			{assign var='have_non_virtual_products' value=false}
			{$skipped = 0}
			{foreach $products as $product}
				{$skip = false}
				{if $skipped != $product.id_product}
					{foreach $gift_products as $g_prod}
						{if $g_prod.gift && $product.id_product ==  $g_prod.id_product}
							{$skip = true} {$skipped = $product.id_product} {break}
						{/if}
					{/foreach}
				{/if}
				
				{if $product.is_virtual == 0}
					{assign var='have_non_virtual_products' value=true}
				{/if}
				{assign var='productId' value=$product.id_product}
				{assign var='productAttributeId' value=$product.id_product_attribute}
				{assign var='quantityDisplayed' value=0}
				{assign var='odd' value=($odd+1)%2}
				{assign var='ignoreProductLast' value=isset($customizedDatas.$productId.$productAttributeId) || count($gift_products)}
				{* Display the product line *}
				{if !$skip }
					{include file="./product-line.tpl" productLast=$product@last productFirst=$product@first}
				{/if}
				{* Then the customized datas ones*}
				{if isset($customizedDatas.$productId.$productAttributeId[$product.id_address_delivery])}
					{foreach $customizedDatas.$productId.$productAttributeId[$product.id_address_delivery] as $id_customization=>$customization}
						<tr
							id="product_{$product.id_product}_{$product.id_product_attribute}_{$id_customization}_{$product.id_address_delivery|intval}"
							class="product_customization_for_{$product.id_product}_{$product.id_product_attribute}_{$product.id_address_delivery|intval}{if $odd} odd{else} even{/if} customization alternate_item {if $product@last && $customization@last && !count($gift_products)}last_item{/if}">
							<td></td>
							<td colspan="3">
								{foreach $customization.datas as $type => $custom_data}
									{if $type == $CUSTOMIZE_FILE}
										<div class="customizationUploaded">
											<ul class="customizationUploaded">
												{foreach $custom_data as $picture}
													<li><img src="{$pic_dir}{$picture.value}_small" alt="" class="customizationUploaded" /></li>
												{/foreach}
											</ul>
										</div>
									{elseif $type == $CUSTOMIZE_TEXTFIELD}
										<ul class="typedText">
											{foreach $custom_data as $textField}
												<li>
													{if $textField.name}
														{$textField.name}
													{else}
														{l s='Text #' mod='ecm_simcheck'}{$textField@index+1}
													{/if}
													: {$textField.value}
												</li>
											{/foreach}
										</ul>
									{/if}
								{/foreach}
							</td>
							<td class="cart_quantity" colspan="1">
								{if isset($cannotModify) AND $cannotModify == 1}
									<span>{if $quantityDisplayed == 0 AND isset($customizedDatas.$productId.$productAttributeId)}{$customizedDatas.$productId.$productAttributeId|@count}{else}{$product.cart_quantity-$quantityDisplayed}{/if}</span>
								{else}
									<input type="hidden" value="{$customization.quantity}" name="quantity_{$product.id_product}_{$product.id_product_attribute}_{$id_customization}_{$product.id_address_delivery|intval}_hidden"/>
									<input type="text" value="{$customization.quantity}" class="cart_quantity_input form-control grey" name="quantity_{$product.id_product}_{$product.id_product_attribute}_{$id_customization}_{$product.id_address_delivery|intval}"/>
									<div class="cart_quantity_button clearfix">
										{if $product.minimal_quantity < ($customization.quantity -$quantityDisplayed) OR $product.minimal_quantity <= 1}
											<a
												id="cart_quantity_down_{$product.id_product}_{$product.id_product_attribute}_{$id_customization}_{$product.id_address_delivery|intval}"
												class="cart_quantity_down btn btn-default button-minus"
												href="{$link->getPageLink('cart', true, NULL, "add=1&amp;id_product={$product.id_product|intval}&amp;ipa={$product.id_product_attribute|intval}&amp;id_address_delivery={$product.id_address_delivery}&amp;id_customization={$id_customization}&amp;op=down&amp;token={$token_cart}")|escape:'html':'UTF-8'}"
												rel="nofollow"
												title="{l s='Subtract' mod='ecm_simcheck'}">
												<span><i class="icon-minus"></i></span>
											</a>
										{else}
											<a
												id="cart_quantity_down_{$product.id_product}_{$product.id_product_attribute}_{$id_customization}"
												class="cart_quantity_down btn btn-default button-minus disabled"
												href="#"
												title="{l s='Subtract' mod='ecm_simcheck'}">
												<span><i class="icon-minus"></i></span>
											</a>
										{/if}
										<a
											id="cart_quantity_up_{$product.id_product}_{$product.id_product_attribute}_{$id_customization}_{$product.id_address_delivery|intval}"
											class="cart_quantity_up btn btn-default button-plus"
											href="{$link->getPageLink('cart', true, NULL, "add=1&amp;id_product={$product.id_product|intval}&amp;ipa={$product.id_product_attribute|intval}&amp;id_address_delivery={$product.id_address_delivery}&amp;id_customization={$id_customization}&amp;token={$token_cart}")|escape:'html':'UTF-8'}"
											rel="nofollow"
											title="{l s='Add' mod='ecm_simcheck'}">
											<span><i class="icon-plus"></i></span>
										</a>
									</div>
								{/if}
							</td>
							<td class="cart_delete text-center">
								{if isset($cannotModify) AND $cannotModify == 1}
								{else}
									<a
										id="{$product.id_product}_{$product.id_product_attribute}_{$id_customization}_{$product.id_address_delivery|intval}"
										class="cart_quantity_delete"
										href="{$link->getPageLink('cart', true, NULL, "delete=1&amp;id_product={$product.id_product|intval}&amp;ipa={$product.id_product_attribute|intval}&amp;id_customization={$id_customization}&amp;id_address_delivery={$product.id_address_delivery}&amp;token={$token_cart}")|escape:'html':'UTF-8'}"
										rel="nofollow"
										title="{l s='Delete'} mod='ecm_simcheck'">
										<i class="icon-trash"></i>
									</a>
								{/if}
							</td>
							<td>
							</td>
						</tr>
						{assign var='quantityDisplayed' value=$quantityDisplayed+$customization.quantity}
					{/foreach}

					{* If it exists also some uncustomized products *}
					{if $product.quantity-$quantityDisplayed > 0}
						{include file="./product-line.tpl" productLast=$product@last productFirst=$product@first}
					{/if}
				{/if}
			{/foreach}
			{assign var='last_was_odd' value=$product@iteration%2}
			{foreach $gift_products as $product}
				{assign var='productId' value=$product.id_product}
				{assign var='productAttributeId' value=$product.id_product_attribute}
				{assign var='quantityDisplayed' value=0}
				{assign var='odd' value=($product@iteration+$last_was_odd)%2}
				{assign var='ignoreProductLast' value=isset($customizedDatas.$productId.$productAttributeId)}
				{assign var='cannotModify' value=1}
				{* Display the gift product line *}
				{include file="./product-line.tpl" productLast=$product@last productFirst=$product@first}
			{/foreach}
		</tbody>

	</table>
</div>

{*<div id="HOOK_SHOPPING_CART">{$HOOK_SHOPPING_CART}</div>
<div class="cart_navigation_extra">
	<div id="HOOK_SHOPPING_CART_EXTRA">{if isset($HOOK_SHOPPING_CART_EXTRA)}{$HOOK_SHOPPING_CART_EXTRA}{/if}</div>
</div>
*}

<script>

$(".cart_quantity_input").on("input change", function (){
	delay_quantity(this,2000,true);
})

$(".cart_quantity_input").on("blur", function (){
	delay_quantity(this,0,false);
})


var cart_qties = {$cart_qties};
var cart = {$cart|@json_encode};


</script>

{if Configuration::Get('ecm_gre_active')}
	<script>
	dataLayer.push({
	  'event': 'rem',
	  'dynx_itemid': '['+gre_products_references.slice(0, -1)+']',
	  'dynx_pagetype': 'conversionintent',
	  'dynx_totalvalue': '['+gre_products_prices.slice(0, -1)+']',
	});
	</script>
{/if}
