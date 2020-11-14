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


<label class="sc-label"><i class="icon-credit-card"></i> {l s='Payment' mod='ecm_simcheck'}</label>
{*<div id="HOOK_TOP_PAYMENT">{$HOOK_TOP_PAYMENT}</div>*}
<div class="table_block table-responsive">
	<table class="table">
	{foreach $payments as $name => $payment}
		<tr class="sc_payment_row{if $payment.warning}_disabled{/if} has-spinner {$payment.hide}
			{if isset($cookie->current_payment) && !$payment.warning && $cookie->current_payment == $name}
				sc_payment_row_checked
			{/if}" 
			value="{$name}">
			{if Configuration::get('ecm_simcheck_show_radio')}
			<td width="16px">
				<input id="payment_option_{$name}" 
				class="payment_sc_radio" type="radio" name="payment_option[{$name}]" 
				value="{$name}" {if $payment.warning}disabled{/if}
				{if isset($cookie->current_payment) && !$payment.warning && $cookie->current_payment == $name}checked="checked"{/if}" 
			</td>
			{/if}
			{if Configuration::get('ecm_simcheck_show_logo')}
			<td><img src="{$payment.icon}" width="{$size.width}"></td>
			{/if}
			<td>
				<strong>{$payment.payment}</strong>
				{if !Configuration::get('ecm_simcheck_simple_name_pay')}
					<br>{$payment.desc} <b style="color:red">{$payment.warning}</b>
				{/if}
			</td>
	
		</tr>
	{/foreach}
	</table>
<div id="checkout_place"></div>	
</div>
{*
<p class="btn btn-default has-spinner" onclick = "action('cart,carrier,payment,checkout,customer', {$cart->id_customer})">
    {l s='Reload ALL' mod='ecm_simplecheckout'}
</p>
<p>
    <div id="debug"></div>
</p>
*}

<script>
$(".sc_payment_row").on("click", function (){
	$(".payment_sc_radio").removeAttr('checked');
	$(".sc_payment_row").removeClass('sc_payment_row_checked');
	$(this).addClass('sc_payment_row_checked');
	action('set_payment,cart,carrier,payment,checkout,customer', $(this).attr('value'));
});
</script>

