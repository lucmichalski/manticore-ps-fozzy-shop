<table style="width: 100%; font-size: 20px; color: #000;">
	<tr>
		<td style="width: 70%; border-top: 1px solid #000;"><span style="font-weight: bold;">{l s='Доставка' pdf='true'}</span>: <span style="font-weight: normal;">{$shipping}</span>
     <br><span style="font-weight: bold;">{$delivery_adrress_city}<br />{$delivery_adrress_pdf_short}</span>
     {if isset($HOOK_DISPLAY_PDF)}<span style="font-weight: bold;">{$HOOK_DISPLAY_PDF}</span>{/if} 
     <br><b>{l s='Оплата:' pdf='true'}</b> {$order->payment}
    </td>
    <td style="width: 30%;  border-top: 1px solid #000;"><p> </p><img src="http://fozzyshop.com.ua/modules/nove_customplugins/barcode/barcodec.php?code=111{$order->id}&height=100"/></td>   
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
    <td style="width: 30%;  border-top: 1px solid #000;"><p> </p><p> </p><img src="http://fozzyshop.com.ua/modules/nove_customplugins/barcode/barcodec.php?code=111{$order->id+10000}&height=100"/></td>   
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

<!-- / PRODUCTS TAB -->
</div>
