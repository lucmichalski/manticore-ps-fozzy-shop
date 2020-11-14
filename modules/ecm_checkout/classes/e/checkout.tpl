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


	{assign var='total_discounts_num' value="{if $total_discounts != 0}1{else}0{/if}"}
	{assign var='use_show_taxes' value="{if $use_taxes && $show_taxes}2{else}0{/if}"}
	{assign var='total_wrapping_taxes_num' value="{if $total_wrapping != 0}1{else}0{/if}"}
	{* eu-legal *}

<label class="sc-label"><i class="icon icon-check"></i> {l s='Summary' mod='ecm_simcheck'}</label>
<p class="alert alert-warning" id="discount_error" style="display: block;"></p>
<table class="table">

	{if sizeof($discounts)}
	<tbody>
		{foreach $discounts as $discount}
		{if ((float)$discount.value_real == 0 && $discount.free_shipping != 1) || ((float)$discount.value_real == 0 && $discount.code == '')}
			{continue}
		{/if}
			<tr class="cart_discount {if $discount@last}last_item{elseif $discount@first}first_item{else}item{/if}" id="cart_discount_{$discount.id_discount}">
				<td class="cart_discount_name" ">{$discount.name}</td>
				<td class="cart_discount_price">
					<span class="price-discount">
					{if !$priceDisplay}{displayPrice price=$discount.value_real*-1}{else}{displayPrice price=$discount.value_tax_exc*-1}{/if}
					</span>
				</td>
				<td class="cart_discount_delete">1 
					{if strlen($discount.code)}
					<a class="price_discount_delete" onclick = "action('delete_Discount,cart,checkout', '{$discount.id_discount}')"
						title="{l s='Delete' mod='ecm_simcheck'}">
						<i class="icon-trash"></i>
					</a>
					{/if}
				</td>
				<td class="cart_discount_price">
					<span class="price-discount price text-right">{if !$priceDisplay}{displayPrice price=$discount.value_real*-1}{else}{displayPrice price=$discount.value_tax_exc*-1}{/if}</span>
				</td>
			</tr>
		{/foreach}
	</tbody>
	{/if}

	<tfoot>
	{assign var='col_span_subtotal' value='2'}
	
	{assign var='rowspan_total' value=2+$total_discounts_num+$total_wrapping_taxes_num}

	{if $use_taxes && $show_taxes && $total_tax != 0}
		{assign var='rowspan_total' value=$rowspan_total+1}
	{/if}

	{if $priceDisplay != 0}
		{assign var='rowspan_total' value=$rowspan_total+1}
	{/if}

	{if $total_shipping_tax_exc <= 0 && (!isset($isVirtualCart) || !$isVirtualCart) && $free_ship}
		{assign var='rowspan_total' value=$rowspan_total+1}
	{else}
		{if $use_taxes && $total_shipping_tax_exc != $total_shipping}
			{if $priceDisplay && $total_shipping_tax_exc > 0}
				{assign var='rowspan_total' value=$rowspan_total+1}
			{elseif $total_shipping > 0}
				{assign var='rowspan_total' value=$rowspan_total+1}
			{/if}
		{elseif $total_shipping_tax_exc > 0}
			{assign var='rowspan_total' value=$rowspan_total+1}
		{/if}
	{/if}

	{if $voucherAllowed}
		<tr class="cart_total_price">
			<td><label>{l s='Vouchers' mod='ecm_simcheck'}</label></td>
			<td colspan="2"><input type="text" class="form-control discount_name" id="discount_name" name="discount_name" value="{if isset($discount_name) && $discount_name}{$discount_name}{/if}" /></td>
			<td><a class="button btn btn-default button-small" onclick = "add_Discount()"><span>{l s='OK' mod='ecm_simcheck'}</span></a></td>
		</tr>
		{if $displayVouchers}
		<tr>
			<td colspan="4">
				<p id="title" class="title-offers">{l s='Take advantage of our exclusive offers:' mod='ecm_simcheck'}</p>
				<div id="display_cart_vouchers">
					{foreach $displayVouchers as $voucher}
						{if $voucher.code != ''}
						<span class="voucher_name" data-code="{$voucher.code|escape:'html':'UTF-8'}"
						title="{l s='Add this voucher' mod='ecm_simcheck'}">{$voucher.code|escape:'html':'UTF-8'}</span> - {/if}{$voucher.name}<br />
					{/foreach}
				</div>
			</td>
		</tr>
		{/if}
	{/if}





	{if $use_taxes}
		{if $priceDisplay}
			<tr class="cart_total_price">
				<td rowspan="{$rowspan_total}" id="cart_voucher" class="cart_voucher">
				</td>
				<td class="text-right" colspan="2">{if $display_tax_label}{l s='Total products (tax excl.)' mod='ecm_simcheck'}{else}{l s='Total products' mod='ecm_simcheck'}{/if}</td>
				<td  class="price text-right" id="total_product">{displayPrice price=$total_products}</td>
			</tr>
		{else}
			<tr class="cart_total_price">
				<td rowspan="{$rowspan_total}" id="cart_voucher" class="cart_voucher">
				</td>
				<td class="text-right" colspan="2" >{l s='Total products' mod='ecm_simcheck'}</td>
				<td  class="price text-right" id="total_product">{displayPrice price=$total_products_wt}</td>
			</tr>
		{/if}
	{else}
		<tr class="cart_total_price">
			<td rowspan="{$rowspan_total}" id="cart_voucher" class="cart_voucher">
			</td>
			<td class="text-right" colspan="2">{l s='Total products' mod='ecm_simcheck'}</td>
			<td  class="price text-right" id="total_product">{displayPrice price=$total_products}</td>
		</tr>
	{/if}
	<tr{if $total_wrapping == 0} style="display: none;"{/if}>
		<td class="text-right" colspan="2">
			{if $use_taxes}
				{if $display_tax_label}{l s='Total gift wrapping (tax incl.)' mod='ecm_simcheck'}{else}{l s='Total gift-wrapping cost' mod='ecm_simcheck'}{/if}
			{else}
				{l s='Total gift-wrapping cost' mod='ecm_simcheck'}
			{/if}
		</td>
		<td  class="price-discount price text-right" id="total_wrapping">
			{if $use_taxes}
				{if $priceDisplay}
					{displayPrice price=$total_wrapping_tax_exc}
				{else}
					{displayPrice price=$total_wrapping}
				{/if}
			{else}
				{displayPrice price=$total_wrapping_tax_exc}
			{/if}
		</td>
	</tr>
	{if $total_shipping_tax_exc <= 0 && (!isset($isVirtualCart) || !$isVirtualCart) && $free_ship}
		<tr class="cart_total_delivery{if !$opc && (!isset($cart->id_address_delivery) || !$cart->id_address_delivery)} unvisible{/if}">
			<td class="text-right" colspan="2">{l s='Total shipping' mod='ecm_simcheck'}</td>
			<td  class="price text-right" id="total_shipping">
			{if isset($cost["'replace'"])}
				{if $replace != ''}
					{$replace}
				{else}
					{l s='By carrier tariff' mod='ecm_simcheck'}
				{/if}
			{else}
				{l s='Free shipping!' mod='ecm_simcheck'}
			{/if}				
			</td>
		</tr>
	{else}
		{if $use_taxes && $total_shipping_tax_exc != $total_shipping}
			{if $priceDisplay}
				<tr class="cart_total_delivery{if $total_shipping_tax_exc <= 0} unvisible{/if}">
					<td class="text-right" colspan="2">{if $display_tax_label}{l s='Total shipping' mod='ecm_simcheck'}{else}{l s='Total shipping' mod='ecm_simcheck'}{/if}</td>
					{if isset($cost["'replace'"])}
						{if $replace != ''}
							{$price = $replace}
						{else}
							{$price = {l s='By carrier tariff' mod='ecm_simcheck'}}
						{/if}
					{else}
						{$price = {displayPrice price=$total_shipping_tax_exc}}
					{/if}				
					<td  class="price text-right" id="total_shipping">{$price}</td>
				</tr>
			{else}
				<tr class="cart_total_delivery{if $total_shipping <= 0} unvisible{/if}">
					<td class="text-right" colspan="2">{if $display_tax_label}{l s='Total shipping' mod='ecm_simcheck'}{else}{l s='Total shipping' mod='ecm_simcheck'}{/if}</td>
					{if isset($cost["'replace'"])}
						{if $replace != ''}
							{$price = $replace}
						{else}
							{$price = {l s='By carrier tariff' mod='ecm_simcheck'}}
						{/if}
					{else}
						{$price = {displayPrice price=$total_shipping}}
					{/if}				
					<td  class="price text-right" id="total_shipping" >{$price}</td>
				</tr>
			{/if}
		{else}
			<tr class="cart_total_delivery{if $total_shipping_tax_exc <= 0} unvisible{/if}">
				<td class="text-right" colspan="2">{l s='Total shipping' mod='ecm_simcheck'}</td>
				{if isset($cost["'replace'"])}
					{if $replace != ''}
						{$price = $replace}
					{else}
						{$price = {l s='By carrier tariff' mod='ecm_simcheck'}}
					{/if}
				{else}
					{$price = {displayPrice price=$total_shipping_tax_exc}}
				{/if}				
				<td  class="price text-right" id="total_shipping" >{$price}</td>
			</tr>
		{/if}
	{/if}
	<tr class="cart_total_voucher {if $total_discounts == 0}unvisible{/if}">
		<td class="text-right" colspan="2">
			{if $display_tax_label}
				{if $use_taxes && $priceDisplay == 0}
					{l s='Total vouchers' mod='ecm_simcheck'}
				{else}
					{l s='Total vouchers' mod='ecm_simcheck'}
				{/if}
			{else}
				{l s='Total vouchers' mod='ecm_simcheck'}
			{/if}
		</td>
		<td  class="price-discount price text-right" id="total_discount">
			{if $use_taxes && $priceDisplay == 0}
				{assign var='total_discounts_negative' value=$total_discounts * -1}
			{else}
				{assign var='total_discounts_negative' value=$total_discounts_tax_exc * -1}
			{/if}
			{displayPrice price=$total_discounts_negative}
		</td>
	</tr>
	{if $use_taxes && $show_taxes && $total_tax != 0 }
		{if $priceDisplay != 0}
		<tr class="cart_total_price">
			<td class="text-right" colspan="2">{if $display_tax_label}{l s='Total (tax excl.)' mod='ecm_simcheck'}{else}{l s='Total' mod='ecm_simcheck'}{/if}</td>
			<td  class="price text-right" id="total_price_without_tax">{displayPrice price=$total_price_without_tax}</td>
		</tr>
		{/if}
		<tr class="cart_total_tax">
			<td class="text-right" colspan="2">{l s='Tax' mod='ecm_simcheck'}</td>
			<td  class="price text-right" id="total_tax">{displayPrice price=$total_tax}</td>
		</tr>
	{/if}
	<tr class="cart_total_price">
		<td class="total_price_container  text-right" colspan="2">
			<span>{l s='Total' mod='ecm_simcheck'}</span>
			<div class="hookDisplayProductPriceBlock-price" id="HOOK_DISPLAY_PRODUCT_PRICE_BLOCK">
				{hook h="displayCartTotalPriceLabel"}
			</div>
		</td>
		{if $use_taxes}
			{if $priceDisplay}
				{if isset($cost["'ignore'"])}
					{$price = {displayPrice price=$total_price_without_tax-$total_shipping_tax_exc}}
				{else}
					{$price = {displayPrice price=$total_price_without_tax}}
				{/if}				
				<td  class="price text-right" id="total_price_container">
					<span id="total_price_without_tax">{$price}</span>
				</td>
			{else}
				{if isset($cost["'ignore'"])}
					{$price = {displayPrice price=$total_price-$total_shipping}}
				{else}
					{$price = {displayPrice price=$total_price}}
				{/if}				
				<td  class="price text-right" id="total_price_container">
					<span id="total_price">{$price}</span>
				</td>
			{/if}
		{else}
			{if isset($cost["'ignore'"])}
				{$price = {displayPrice price=$total_price_without_tax-$total_shipping_tax_exc}}
			{else}
				{$price = {displayPrice price=$total_price_without_tax}}
			{/if}				
			<td  class="price text-right" id="total_price_container">
				<span id="total_price_without_tax">{$price}</span>
			</td>
		{/if}
	</tr>
	</tfoot>


</table>


<div class="table_block _table-responsive">
<table class="table">
{if $opc}
	<tr><td>
		<label>{l s='Your wishes for order can be written here' mod='ecm_simcheck'}</label>
		<div>
			<textarea class="form-control checkout_ontype" rows="2" name="message" id="message" act = "save_message">{strip}{if isset($oldMessage)}{$oldMessage|escape:'html':'UTF-8'}{/if}{/strip}</textarea>
		</div>
	</td></tr>
	{/if}
	
	{if $recyclablePackAllowed}
	<tr><td>
		<div class="checkbox recyclable">
			<input type="checkbox" name="recyclable" id="recyclable" value="1"{if $recyclable == 1} checked="checked"{/if} 
			 class="oncheck" act="save_cart"/>
			<label for="recyclable">{l s='I would like to receive my order in recycled packaging.' mod='ecm_simcheck'}</label>
		</div>
	</td></tr>
{/if}

{if $giftAllowed}
	<tr><td>
		<div class="checkbox _gift">
			<input type="checkbox" name="gift" id="gift" value="1" class="oncheck" act="save_cart,cart,checkout"
			{if $cart->gift == 1} checked="checked"{/if} />
			<label for="gift">
				{l s='I would like my order to be gift wrapped.' mod='ecm_simcheck'}
				{if $gift_wrapping_price > 0}
					&nbsp;<i>({l s='Additional cost of' mod='ecm_simcheck'}
					<span class="price" id="gift-price">
						{if $priceDisplay == 1}
							{convertPrice price=$total_wrapping_tax_exc_cost}
						{else}
							{convertPrice price=$total_wrapping_cost}
						{/if}
					</span>
					{if $use_taxes && $display_tax_label}
						{if $priceDisplay == 1}
							{l s='(tax excl.)' mod='ecm_simcheck'}
						{else}
							{l s='(tax incl.)' mod='ecm_simcheck'}
						{/if}
					{/if})
					</i>
				{/if}
			</label>
	</div>
	<p id="gift_div">
		<label for="gift_message">{l s='If you\'d like, you can add a note to the gift:' mod='ecm_simcheck'}</label>
		<textarea class="form-control checkout_ontype" rows="2" id="gift_message" class="form-control" name="gift_message" act="save_cart">{$cart->gift_message|escape:'html':'UTF-8'}</textarea>
	</p>
	</td></tr>
{/if}



{if $conditions && $cms_id && (!isset($advanced_payment_api) || !$advanced_payment_api)}
<tr><td>
	{if isset($override_tos_display) && $override_tos_display}
		{$override_tos_display}
	{else}
		<div class="checkbox">
				<input type="checkbox" name="cgv" id="cgv" value="1" class="oncheck" act="set_TOS,cart,checkout" title="{l s='Terms of service' mod='ecm_simcheck'}"
				{if $checkedTOS}checked="checked"{/if} />
				<label for="cgv" class="cgv">{l s='I agree to the terms of service and will adhere to them unconditionally.' mod='ecm_simcheck'}</label>
				<a href="{$link_conditions|escape:'html':'UTF-8'}" class="iframe" rel="nofollow">{l s='(Read the Terms of Service)' mod='ecm_simcheck'}</a>
		</div>
	{/if}
</td></tr>
{else}
<input class="unvisible" type="checkbox" name="cgv" id="cgv" value="1" checked="checked" />
{$checkedTOS = true}
{/if}
{if $msg}{$checkedTOS = false}{/if}

{if $call_me_check}
<tr><td>
	<div class="checkbox">
	<input class="oncheck not_uniform" type="checkbox" name="callme" id="callme" value="1" 
	{if $callme == 1} checked="checked"{/if} act="save_address"/>
	<label for="callme">{l s='Call me please.' mod='ecm_simcheck'}</label>
	</div>
</td></tr>
{/if}

<tr><td class="text-right">
	<p class="btn btn-default button button-medium sc_confirm"
	{if $checkedTOS} onclick = "to_checkout('to_checkout')" {else} disabled title="{l s='Please confirm by terms of service' mod='ecm_simcheck'}"{/if}>
		<span>{l s='Proceed to checkout' mod='ecm_simcheck' mod='ecm_simcheck'}
		<i class="icon-chevron-right right"></i></span>
	</p>
</td></tr>
</table>		
</div>

<script>

var cart_qties = {$cart_qties};
var cart = {$cart|@json_encode};
var timeoutId;
var showerror = false;
if ($('#gift').is(':checked'))
		$('#gift_div').show();
	else
		$('#gift_div').hide();



$(".checkout_ontype").on("change", function (){
	//delay(this,0,true);
})

$(".checkout_ontype").on("input", function (){
	//delay(this,2000,true);
})

$(".checkout_ontype").on("blur", function (){
	delay(this,0,false); //checkout
})

$(".oncheck").on("click", function (){
	action($(this).attr('act'), 'check_'+$(this).is(':checked'), $(this).attr('id'), false);
})


$(".voucher_name").on('click', function(e){
	action('add_Discount,cart,checkout', $(this).data('code'));
});

function to_checkout(){
	var noerror = true;
	var showerror = false;
	$.each($('.customer_ontype:required'), function(index, value) {
		if(typeof($(value).attr('unvisible')) != 'string'){
			if ($(value).prop('defaultValue') != $(value).val() &&  $(value)[0].checkValidity()){
				action($(value).attr('act'),$(value).val(),$(value).attr('id'), false);
			}
		}
	});
	
	$.each($('#simplecheckout_content input:required'), function(index, value) {
		if($(value).hasClass('sc-error')){
			noerror = false;
			showerror = true;
		} else {
			if(typeof($(value).attr('unvisible')) != 'string') {
				if(!$(value)[0].checkValidity()){
					$(value).removeClass('sc-ok').addClass('sc-error');
					noerror = false;
					showerror = true;
				} 
			}
		}
		if (showerror){
			$.growl.error({
				title: $(value).attr('title'),
                size: "medium",
                message: fill_error,
            });
            showerror = false;
		}
	});
	
	if(authMethod == 2){
		if($('.phone').val() == ''){
			$('#authMethod2').show();
			$('.phone').removeClass('sc-ok').addClass('sc-error');
			noerror = false;
		} else {
			$('#authMethod2').hide();
		}
		
	}

	$.each($('.delivery'), function(index, value) {
		if($(value).val() == '0') {
			noerror = false;
			$(value).focus();
				$.growl.error({
					title: $('#'+$(value).attr('id')+' option:selected').text(),
	                size: "medium",
	                message: "",
	            });
		}
	});

    if($('input[name=pvz]').length && $('input[name=pvz]:checked').val() == undefined) {
            noerror = false;
            $.growl.error({
                    title: 'Выберите склад назначения',
                    size: "medium",
                    message: "",
                });
        }
	
	if (!$(".payment_sc_radio").prop('checked')){
		noerror = false;
		$.growl.error({
			title: payment_select_error,
			size: "medium",
			message: "",
		});
	}

	if (noerror) {
		if (typeof(ecm_gre_to_cart) == 'function') ecm_gre_to_cart()
		action('make_order',null,null,false);
	}
}
</script>