<table style="width: 100%; font-size: 20px; color: #000;">
	<tr>
		<td style="width: 70%; border-top: 1px solid #000;"><span style="font-weight: bold;">{l s='Доставка' pdf='true'}</span>: <span style="font-weight: normal;">{$shipping}</span>
     <br><span style="font-weight: bold;">{$delivery_adrress_city}<br />{$delivery_adrress_pdf_short}</span>
     {if isset($HOOK_DISPLAY_PDF)}<span style="font-weight: bold;">{$HOOK_DISPLAY_PDF}</span>{/if} 
     <br><b>{l s='Оплата:' pdf='true'}</b> {$order->payment}
    </td>
    <td style="width: 30%;  border-top: 1px solid #000;"><p>{$cart1} </p><img src="http://192.168.0.2/modules/nove_customplugins/barcode/barcodec.php?code=111{$order->id}&height=100"/></td>   
	</tr>
</table>
<br>
<br>
<table style="width: 100%; font-size: 20px; color: #000;">
	<tr>
		<td style="width: 70%; border-top: 1px solid #000;"><p> </p><b>{l s='Заказ №:' pdf='true'}</b> {$order->id}
    <br><span style="font-weight: bold;">{l s='Доставка' pdf='true'}</span>: <span style="font-weight: normal;">{$shipping}</span>
    <br><span style="font-weight: bold;">{$delivery_adrress_city}<br />{$delivery_adrress_pdf_short}</span>
     {if isset($HOOK_DISPLAY_PDF)}<span style="font-weight: bold;">{$HOOK_DISPLAY_PDF}</span>{/if} 
     <br><b>{l s='Оплата:' pdf='true'}</b> {$order->payment}
    </td> 
    <td style="width: 30%;  border-top: 1px solid #000;"><p> </p><p> </p><img src="http://192.168.0.2/modules/nove_customplugins/barcode/barcodec.php?code=111{$order->id+10000}&height=100"/></td>   
	</tr>
</table>

<br pagebreak="true"/>
<div style="font-size: 8pt; color: #444">
<!-- ADDRESSES  -->
<table style="width: 100%; font-size: 12pt; color: #000;">
	<tr >
  		<td style="width: 50%; border: 1px solid #000;"><b>{l s='Заказ №:' pdf='true'}</b> {$order->id}
    <br><span style="font-weight: bold;">{l s='Доставка' pdf='true'}</span>: <span style="font-weight: bold;">{$shipping}</span>
     <span style="font-weight: bold;">{hook h='DisplayPDFInvoice2' order=$order->id}</span>
     <br><b>{l s='Оплата:' pdf='true'}</b> {$order->payment}
    </td>     
    <td style="width: 50%; text-align: right; border: 1px solid #000;"><br />{$delivery_adrress_company}<br />{$delivery_adrress_name}<br />{$delivery_adrress_city}<br />{$delivery_adrress_pdf}<br />{$delivery_mphone}<br />{$delivery_other}<br />{$delivery_phone}</td>
	</tr>
</table>    
<!-- / ADDRESSES -->
{if ( isset($order_invoice->note) && $order_invoice->note ||  isset($messages) && $messages)}
<table style="width: 100%; text-align: center; border: 1px solid #000; font-size: 10pt;">
	<tr>
		<td>
{if isset($order_invoice->note) && $order_invoice->note}
<table style="width: 100%">
	<tr>
		<td style="width: 15%"></td>
		<td style="width: 85%">{$order_invoice->note|nl2br}</td>
	</tr>
</table>
{/if}
{if isset($messages) && $messages}
<table style="width: 100%">
  {foreach $messages as $message}
	<tr>
		<td style="text-align: left; font-weight: bold; font-size: 20pt; margin-top: 5px; margin-bottom: 5px;"><span style="color: #000;">{if $message.id_employee > 0}{$message.elastname} {$message.efirstname}{else}{$message.clastname} {$message.cfirstname}{/if}:</span> {$message.message}</td>
	</tr>
  {/foreach}
</table>
{/if}
		</td>
	</tr>

</table>

{/if}

<table style="width: 100%">
	<tr>
		<td style="text-align: left; font-weight: bold; font-size: 20pt; margin-top: 5px; margin-bottom: 5px;">&nbsp;</td>
	</tr>
</table>		
<!-- PRODUCTS TAB -->
<table style="width: 100%; font-size: 8px; border-color: #CCC;">
	<tr style="line-height:14px;">
    <td style="text-align: left; background-color: #CCC; color: #000; padding-left: 10px; font-weight: bold; width: 3%">{l s='№' pdf='true'}</td>
		<td style="text-align: left; background-color: #CCC; color: #000; padding-left: 10px; font-weight: bold; width: 7%">{l s='Артикул' pdf='true'}</td>
    <td style="text-align: left; background-color: #CCC; color: #000; padding-left: 10px; font-weight: bold; width: 11%">{l s='Штрихкод' pdf='true'}</td>
		<td style="text-align: left; background-color: #CCC; color: #000; padding-left: 10px; font-weight: bold; width: 47%">{l s='Товар' pdf='true'}</td>
		<!-- unit price tax excluded is mandatory -->
		<td style="background-color: #CCC; color: #000; text-align: right; font-weight: bold; width: 7%">{l s='Ед.' pdf='true'}</td>
		<td style="background-color: #CCC; color: #000; text-align: center; font-weight: bold; width: 5%">{l s='Кол.' pdf='true'}</td>
    <td style="background-color: #CCC; color: #000; text-align: right; font-weight: bold; width: 10%">
			{l s='Цена' pdf='true'}
		</td>
		<td style="background-color: #CCC; color: #000; text-align: right; font-weight: bold; width: 10%">
			{l s='Всего' pdf='true'}
		</td>
	</tr>
	<!-- PRODUCTS -->
	{$i=1}
  {assign var="recent" value=""}
  {foreach $order_details as $order_detail}
	{cycle values='#FFF,#EEE' assign=bgcolor}
  {if $order_detail.parent_category != $recent}
  <tr style="line-height:14px; background-color:#ffffff;">
    <td style="text-align: center; color: #000; border: none; font-weight: bold; font-size: 10px;" colspan = "8">
			{$order_detail.parent_category}
		</td>
  </tr>
  {/if}
	<tr style="line-height:12px;font-size: 8px; background-color:{$bgcolor};">
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
    <td style="text-align: center; color: #000; border: 1px solid #CCC;">{$order_detail.product_quantity*1}</td>
		<td style="text-align: right; color: #000; border: 1px solid #CCC;">
		{if $tax_excluded_display}
			{displayPrice currency=$order->id_currency price=$order_detail.unit_price_tax_excl}
		{else}
			{displayPrice currency=$order->id_currency price=$order_detail.unit_price_tax_incl}
		{/if}
		</td>
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
				<tr style="line-height:14px;background-color:{$bgcolor}; ">
					<td style="line-height:14px; text-align: left; width: 60%; vertical-align: top">

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
    {$recent = $order_detail.parent_category}
	{/foreach}
	<!-- END PRODUCTS -->

	<!-- CART RULES -->
	{assign var="shipping_discount_tax_incl" value="0"}
	{foreach $cart_rules as $cart_rule}
	{cycle values='#FFF,#DDD' assign=bgcolor}
		<tr style="line-height:25px;background-color:{$bgcolor}; text-align:right;">
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
		<td style="width: 15%; text-align: right;">{displayPrice currency=$order->id_currency price=$order->total_products}</td>
	</tr>
	{/if}

	{if $shipping_all.0.shipping_cost_tax_excl}
	<tr style="line-height:15px;">
		<td style="text-align: right; font-weight: bold">{l s='Доставка' pdf='true'}</td>
		<td style="width: 15%; text-align: right;">
				{displayPrice currency=$order->id_currency price=$shipping_all.0.shipping_cost_tax_excl}
		</td>
	</tr>
	{/if}
	<tr style="line-height:15px;">
		<td style="text-align: right; font-weight: bold">{l s='Итого' pdf='true'}</td>
		<td style="width: 15%; text-align: right;">{displayPrice currency=$order->id_currency price=$order->total_paid_tax_excl}</td>
	</tr>

</table>
<!-- / PRODUCTS TAB -->
</div>
