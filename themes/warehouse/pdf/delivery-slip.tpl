<div style="font-size: 8px; color: #444">

<!-- ADDRESSES -->
{$req_tab}
<table style="width: 100%; font-size: 12px; color: #000;">
	<tr>
		<td style="text-align: left;"><span style="font-weight: bold;">Платник:</span></td>
	</tr>  
	<tr>
		<td style="text-align: left;"><span style="font-weight: bold;">{$delivery_address_true}</span></td>
	</tr>                   
</table>
<table style="width: 100%; font-size: 12px; color: #000;">
	<tr>
		<td style="text-align: left;"><span style="font-weight: bold;">Отримувач:</span></td>
	</tr>  
	<tr>
		<td style="text-align: left;"><span style="font-weight: bold;">{$delivery_address_true}</span></td>
	</tr>                   
</table>
<!-- / ADDRESSES -->

<br /><br />

<table style="width: 100%; text-align: center; border: 1px solid #CCC; font-size: 9px;">
	<tr>
		<td style="width: 20%; background-color: #CCC; color: #000;">
			<b>{l s='Счет №:' pdf='true'}</b>
		</td>
		<td style="width: 20%; background-color: #CCC; color: #000;">
			<b>{l s='От:' pdf='true'}</b>
		</td>
		<td style="width: 60%; background-color: #CCC; color: #000;">
			<b>{l s='Оплата:' pdf='true'}</b>
		</td>
	</tr>
	<tr>
		<td style="width: 20%;">
			{$order->id}
		</td>
		<td style="width: 20%;">
			{$order->date_add|date_format:"%d-%m-%Y"}
		</td>
		<td style="width: 60%;">
			{foreach from=$order->getOrderPaymentCollection() item=payment}
				<b>{$payment->payment_method}</b> : {displayPrice price=$order->total_products currency=$order->id_currency}
			{foreachelse}
				{l s='No payment' pdf='true'}
			{/foreach}
		</td>
	</tr>
</table>

<br />

		
<!-- PRODUCTS TAB -->
<table style="width: 100%; font-size: 8px; border-color: #CCC;">
	<tr style="line-height:13px;">
    <td style="text-align: left; background-color: #CCC; color: #000; padding-left: 10px; font-weight: bold; width: 3%">{l s='№' pdf='true'}</td>
		<td style="text-align: left; background-color: #CCC; color: #000; padding-left: 10px; font-weight: bold; width: 7%">{l s='Артикул' pdf='true'}</td>
    <td style="text-align: left; background-color: #CCC; color: #000; padding-left: 10px; font-weight: bold; width: 11%">{l s='EAN 13' pdf='true'}</td>
		<td style="text-align: left; background-color: #CCC; color: #000; padding-left: 10px; font-weight: bold; width: 37%">{l s='товар' pdf='true'}</td>
		<!-- unit price tax excluded is mandatory -->
		<td style="background-color: #CCC; color: #000; text-align: right; font-weight: bold; width: 7%">{l s='Ед.' pdf='true'}</td>
		<td style="background-color: #CCC; color: #000; text-align: right; font-weight: bold; width: 10%">
			{l s='Цена' pdf='true'}
		</td>
		<td style="background-color: #CCC; color: #000; text-align: center; font-weight: bold; width: 15%">{l s='Кол.' pdf='true'}</td>
		<td style="background-color: #CCC; color: #000; text-align: right; font-weight: bold; width: 10%">
			{l s='Всего' pdf='true'}
		</td>
	</tr>
	<!-- PRODUCTS -->
	{$i=1}
  {foreach $order_details as $order_detail}
	{cycle values='#FFF,#EEE' assign=bgcolor}
	<tr style="line-height:13px; background-color:{$bgcolor};">
		<td style="text-align: center; color: #000; border: 1px solid #CCC;">
			{$i++}
		</td>
    <td style="text-align: center; color: #000; border: 1px solid #CCC;">
			{if !empty($order_detail.product_reference)}
				{$order_detail.product_reference}
			{else}
				--
			{/if}
		</td>
    <td style="text-align: center; color: #000; border: 1px solid #CCC;">
			{$order_detail.ean13}
		</td>
		<td style="text-align: left; color: #000; border: 1px solid #CCC;">{$order_detail.product_name}</td>
		<!-- unit price tax excluded is mandatory -->
		<td style="text-align: right; color: #000; border: 1px solid #CCC;">
			{$order_detail.unity}
		</td>
		<td style="text-align: right; color: #000; border: 1px solid #CCC;">
		{if $tax_excluded_display}
			{displayPrice currency=$order->id_currency price=$order_detail.unit_price_tax_excl}
		{else}
			{displayPrice currency=$order->id_currency price=$order_detail.unit_price_tax_incl}
		{/if}
		</td>
		<td style="text-align: center; color: #000; border: 1px solid #CCC;">{$order_detail.product_quantity}</td>
		<td style="text-align: right; color: #000; border: 1px solid #CCC;">
		{if $tax_excluded_display}
			{displayPrice currency=$order->id_currency price=$order_detail.total_price_tax_excl}
		{else}
			{displayPrice currency=$order->id_currency price=$order_detail.total_price_tax_incl}
		{/if}
		</td>
	</tr>
		{foreach $order_detail.customizedDatas as $customizationPerAddress}
			{foreach $customizationPerAddress as $customizationId => $customization}
				<tr style="line-height:6px;background-color:{$bgcolor}; ">
					<td style="line-height:13px; text-align: left; width: 60%; vertical-align: top">

							<blockquote>
								{if isset($customization.datas[$smarty.const._CUSTOMIZE_TEXTFIELD_]) && count($customization.datas[$smarty.const._CUSTOMIZE_TEXTFIELD_]) > 0}
									{foreach $customization.datas[$smarty.const._CUSTOMIZE_TEXTFIELD_] as $customization_infos}
										{$customization_infos.name}: {$customization_infos.value}
										{if !$smarty.foreach.custo_foreach.last}<br />
										{else}
										<div style="line-height:0.4pt">&nbsp;</div>
										{/if}
									{/foreach}
								{/if}

								{if isset($customization.datas[$smarty.const._CUSTOMIZE_FILE_]) && count($customization.datas[$smarty.const._CUSTOMIZE_FILE_]) > 0}
									{count($customization.datas[$smarty.const._CUSTOMIZE_FILE_])} {l s='image(s)' pdf='true'}
								{/if}
							</blockquote>
					</td>
					<td style="text-align: right; width: 15%"></td>
					<td style="text-align: center; width: 10%; vertical-align: top">({$customization.quantity})</td>
					<td style="width: 15%; text-align: right;"></td>
				</tr>
			{/foreach}
		{/foreach}
	{/foreach}
	<!-- END PRODUCTS -->

	<!-- CART RULES -->
	{assign var="shipping_discount_tax_incl" value="0"}
	{foreach $cart_rules as $cart_rule}
	{cycle values='#FFF,#DDD' assign=bgcolor}
		<tr style="line-height:16px;background-color:{$bgcolor}; text-align:right;">
			<td colspan="{if !$tax_excluded_display}6{else}5{/if}">{$cart_rule.name}</td>
			<td>
				{if $cart_rule.free_shipping}
					{assign var="shipping_discount_tax_incl" value=$order_invoice->total_shipping_tax_incl}
				{/if}
				{if $tax_excluded_display}
					- {displayPrice currency=$order->id_currency price=$cart_rule.value_tax_excl}
				{else}
					- {displayPrice currency=$order->id_currency price=$cart_rule.value}
				{/if}
			</td>
		</tr>
	{/foreach}
	<!-- END CART RULES -->
</table>

<table style="width: 100%;">
	{if (($order_invoice->total_paid_tax_incl - $order_invoice->total_paid_tax_excl) > 0)}
	<tr style="line-height:15px;">
		<td style="width: 85%; text-align: right; font-weight: bold">{l s='Product Total (Tax Incl.)' pdf='true'}</td>
		<td style="width: 15%; text-align: right;">{displayPrice currency=$order->id_currency price=$order->total_products_wt}</td>
	</tr>
	{else}
	<tr style="line-height:15px;">
		<td style="width: 85%; text-align: right; font-weight: bold">{l s='За товары' pdf='true'}</td>
		<td style="width: 15%; text-align: right;">{displayPrice currency=$order->id_currency price=($order->total_products - $order->total_products/6)}</td>
	</tr>
	{/if}
	<tr style="line-height:15px;">
		<td style="text-align: right; font-weight: bold">{l s='НДС' pdf='true'}</td>
		<td style="width: 15%; text-align: right;">{displayPrice currency=$order->id_currency price=($order->total_products/6)}</td>
	</tr>	

	<tr style="line-height:15px;">
		<td style="text-align: right; font-weight: bold">{l s='Итого' pdf='true'}</td>
		<td style="width: 15%; text-align: right;">{displayPrice currency=$order->id_currency price=$order->total_products}</td>
	</tr>

</table>
<!-- / PRODUCTS TAB -->

{if isset($order_invoice->note) && $order_invoice->note}
<div style="line-height: 1pt">&nbsp;</div>
<table style="width: 100%">
	<tr>
		<td style="width: 15%"></td>
		<td style="width: 85%">{$order_invoice->note|nl2br}</td>
	</tr>
</table>
{/if}

{if isset($messages) && $messages}
<div style="line-height: 1pt">&nbsp;</div>
<table style="width: 100%">
	<tr>
		<td style="text-align: left; font-weight: bold; font-size:30px;">{$messages}</td>
	</tr>
</table>
{/if}

{if isset($HOOK_DISPLAY_PDF)}

<table style="width: 100%">
	<tr>
		<td style="width: 15%"></td>
		<td style="width: 85%">{$HOOK_DISPLAY_PDF}</td>
	</tr>
</table>
{/if}

</div>
