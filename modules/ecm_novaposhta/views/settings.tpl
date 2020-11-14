<style>
.bootstrap input[type="email"] {
    background-color: #f5f8f9;
    background-image: none;
    border: 1px solid #c7d6db;
    border-radius: 3px;
    color: #555;
    display: block;
    font-size: 12px;
    height: 31px;
    line-height: 1.42857;
    padding: 6px 8px;
    transition: border-color 0.15s ease-in-out 0s, box-shadow 0.15s ease-in-out 0s;
    width: 100%;
}</style>

{assign var=checkvalue value=1}
<input id="id_lang" name="id_lang" type="hidden" value ="{$lang}"/>
<input id="np_module_dir" name="np_module_dir" type="hidden" value ="{$np_module_dir}"/>
<input id="customer" name="customer" type="hidden" value ="-{$employee}"/>
<input id="md_page" name="page" type="hidden" value ="settings"/>

<fieldset class="space">
<div class="panel">
	<div class="panel-heading">
		<i class="icon-gear"></i> {l s='Settings' mod='ecm_novaposhta'}
	</div>
		<table>
			<tr valign="top">
				<td width="48%">
					<label>{l s='API key' mod='ecm_novaposhta'}</label>
					<div class="margin-form">
						<input type="text" name="API_KEY" placeholder="{l s='API key' mod='ecm_novaposhta'}" required
						value="{$API_KEY}"/>
						<p class="clear">{l s='Enter your API key (in the particular office at site "Nova poshta")' mod='ecm_novaposhta'}</p>
					</div>
					
					
					<label>{l s='By oder' mod='ecm_novaposhta'}</label>
					<div class="margin-form">
						<input type="checkbox" name="by_order"  value="1" {$byorder}/>
						<p class="clear">{l s='Check if you work by "Order", else "Loyality programm"' mod='ecm_novaposhta'}</p>
					</div>
					
		<div class="hide">
		</div>
		
					<label>{l s='Description' mod='ecm_novaposhta'}</label>
					<div class="margin-form">
						<input type="text" name="description" placeholder="{l s='Description' mod='ecm_novaposhta'}" required
						value="{$description}"/>
						<p class="clear">{l s='Description of cargo' mod='ecm_novaposhta'}</p>
					</div>
					
					<label>{l s='Pack' mod='ecm_novaposhta'}</label>
					<div class="margin-form">
						<input type="text" name="pack" placeholder="{l s='Pack' mod='ecm_novaposhta'}"
						value="{$pack}"/>
						<p class="clear">{l s='Packing of cargo' mod='ecm_novaposhta'}</p>
					</div>
					
					<label>{l s='Amount of insurance' mod='ecm_novaposhta'} ({$sign})</label>
					<div class="margin-form">
						<input type="text" name="insurance" placeholder="{l s='Amount of insurance' mod='ecm_novaposhta'}" required
						value="{$insurance}"/>
						<p class="clear">{l s='Amount of insurance, 0 for full price' mod='ecm_novaposhta'}</p>
					</div>
					
					<label>{l s='Send mail for delivery NP' mod='ecm_novaposhta'}</label>
					<div class="margin-form">
					<input type="checkbox" name="SendNPmail" value="1" {$SendNPmail} />
						<p class="clear">{l s='If checked, send additional mail notification' mod='ecm_novaposhta'}</p>
					</div>
				
					<label>{l s='Send mail for administrator' mod='ecm_novaposhta'}</label>
					<div class="margin-form">
						<input type="email" name="SendNPadminmail" value="{$SendNPadminmail}" 
						placeholder="{l s='administrator e-mail' mod='ecm_novaposhta'}"/>
						<p class="clear">{l s='If filled, send mail notification for administrator' mod='ecm_novaposhta'}</p>
					</div>
				
					<label>{l s='Payment Method' mod='ecm_novaposhta'}</label>
					<div class="margin-form">
						{html_options name=payment_method options=$payment_forms selected=$payment_form}
						<p class="clear">{l s='Select payment method for delivery' mod='ecm_novaposhta'}</p>
					</div>
				
				
					<label>{l s='Printing format' mod='ecm_novaposhta'}</label>
					<div class="margin-form">
						{html_options name=format options=$formats selected=$format}					
						<p class="clear">{l s='Select format for printed form' mod='ecm_novaposhta'}</p>
					</div>
				
					<label>{l s='Info for order number' mod='ecm_novaposhta'}</label>
					<div class="margin-form">
						{html_options name=InfoRegClientBarcode options=$InfoRegClientBarcodes selected=$InfoRegClientBarcode}					
						<p class="clear">{l s='Select info for order number' mod='ecm_novaposhta'}</p>
					</div>
				
					<label>{l s='Add detail description' mod='ecm_novaposhta'}</label>
					<div class="margin-form">
						<input type="checkbox" name="add_msg" value="1" {$add_msg} />
						<p class="clear">{l s='If checked, detail description add to order' mod='ecm_novaposhta'}</p>
					</div>
				
					<label>{l s='Address delivery' mod='ecm_novaposhta'}</label>
					<div class="margin-form">
						<input type="checkbox" name="address_delivery" value="1" {$address_delivery} />
						<p class="clear">{l s='If checked, enable address delivery' mod='ecm_novaposhta'}</p>
					</div>
				
					<label>{l s='Address delivery by default' mod='ecm_novaposhta'}</label>
					<div class="margin-form">
						<input type="checkbox" name="address_delivery_def" value="1" {$address_delivery_def} />
						<p class="clear">{l s='If checked, set address delivery by default' mod='ecm_novaposhta'}</p>
					</div>
				
					<label>{l s='Another recipient' mod='ecm_novaposhta'}</label>
					<div class="margin-form">
						<input type="checkbox" name="another_recipient" value="1" {$another_recipient} />
						<p class="clear">{l s='If checked, enable another recipient for checkout' mod='ecm_novaposhta'}</p>
					</div>
				
					<label>{l s='Show detail cost of delivery' mod='ecm_novaposhta'}</label>
					<div class="margin-form">
						<input type="checkbox" name="show" value="1" {$show} />
						<p class="clear">{l s='If checked, detail cost information is show' mod='ecm_novaposhta'}</p>
					</div>
				
					<label>{l s='Correct address delivery'  mod='ecm_novaposhta'}</label>
					<div class="margin-form">
						<input type="checkbox" name="correct_address" value="1" {$correct_address} />
						<p class="clear">{l s='If checked, the address delivery are corrected' mod='ecm_novaposhta'}</p>
					</div>
					
					<label>{l s='Another alias'  mod='ecm_novaposhta'}</label>
					<div class="margin-form">
						<input type="text" name="another_alias" placeholder="{l s='NP another, for example' mod='ecm_novaposhta'}" required
						value="{$another_alias}"/>
						<p class="clear">{l s='Enter name for anoter address' mod='ecm_novaposhta'}</p>
					</div>
					
					<label>{l s='Use middle name'  mod='ecm_novaposhta'}</label>
					<div class="margin-form">
						<input type="checkbox" name="middlename" value="1" {$middlename} />
					</div>
					
				
				</td>
				<td width="4%"></td>
				<td width="48%">
				<div class="panel">
					<div class="panel-heading">
						<i class="icon-money"></i> {l s='Наложенный платеж' mod='ecm_novaposhta'}
					</div>
				<div style="display:none">				
					<label>{l s='Add fix delivery to order price' mod='ecm_novaposhta'}</label>
					<div class="margin-form">
						<input type="checkbox" name="addtoorder" value="1" {$addtoorder} />
						<p class="clear">{l s='If checked, when you make the EH to the amount of the order is added fix shipping cost' mod='ecm_novaposhta'}</p>
					</div>
				</div>				
				
					<label>{l s='Add redelivery cost to shiping cost' mod='ecm_novaposhta'}</label>
					<p class="alert alert-warning">
					{l s='Для работы этой настройки требуется установка модуля оплаты'  mod='ecm_novaposhta'}
					<a href="https://elcommerce.com.ua/moduli-dlya-prestashop/55-modul-oplata-nalichnymi-pri-poluchenii-v-otdelenii-novoy-pochty.html" target="_blank>">{l s='Нова Пошта' mod='ecm_novaposhta'}</a></br>
					{l s='Для корректного отображения в одностраничниках требуется их доработка' mod='ecm_novaposhta'}</br>
					</p>
					<div class="margin-form">
					<input type="checkbox" name="redelivery" value="1" {$redelivery} />
						<p class="clear">{l s='Если включено, то к стоимости доставки будет добавлена комисия наложенного платежа' mod='ecm_novaposhta'}</p>
					</div>
					
					<label>{l s='Default payment control' mod='ecm_novaposhta'}</label>
					<div class="margin-form">
					<input type="checkbox" name="AfterpaymentOnGoods" value="1" {$AfterpaymentOnGoods} />
						<p class="clear">{l s='If checked, use payment control for COD by default' mod='ecm_novaposhta'}</p>
					</div>
				
				</div>
				<div class="panel">
					<div class="panel-heading">
						<i class="icon-money"></i> {l s='Оплата доставки' mod='ecm_novaposhta'}
					</div>

					<div class="panel">
						<label>{l s='Fixed cost for delivery' mod='ecm_novaposhta'} ({$sign})</label>
						<div class="margin-form">
						<input type="text" name="fixcost" value="{$fixcost}"/> 
						<input type="text" name="fixcost_address" value="{$fixcost_address}" title="{l s='for address delivery' mod='ecm_novaposhta'}"/> 
						<input type="checkbox" name="chk_fixcost" value="1" {$chk_fixcost} />
							<p class="clear">{l s='If checked, use fixed cost for delivery' mod='ecm_novaposhta'}</p>
						</div>
						
						<label>{l s='Use fixed cost for order' mod='ecm_novaposhta'}</label>
						<div class="margin-form">
						<input type="checkbox" name="only_fixcost" value="1" {$only_fixcost} />
							<p class="clear">{l s='If checked, for order checkout use only fixed cost for delivery' mod='ecm_novaposhta'}</p>
						</div>
						
						<div class="panel">
							<label>{l s='Local cost for COD' mod='ecm_novaposhta'}</label>
							<div class="margin-form">
							<input type="checkbox" name="chk_fixcost_cod" value="1" {$chk_fixcost_cod} />
								<p class="clear">{l s='If checked, use local cost for COD' mod='ecm_novaposhta'}</p>
							</div>
							
							<label>{l s='Percentage control for pay' mod='ecm_novaposhta'}</label>
							<div class="margin-form">
								<input type="text" name="percentage"  value="{$percentage}"/>
								<p class="clear">{l s='A percentage of the value of the order' mod='ecm_novaposhta'}</p>
							</div>
							
							<label>{l s='Shipping rate' mod='ecm_novaposhta'} ({$sign})</label>
							<div class="margin-form">
								<input type="text" name="comiso" value="{$comiso}"/>
								<p class="clear">{l s='Redelivery shipping rate' mod='ecm_novaposhta'}</p>
							</div>
							
							<label>{l s='COD manual correction' mod='ecm_novaposhta'}</label>
							<div class="margin-form">
							<input type="checkbox" name="cod_manual_correction" value="1" {$cod_manual_correction} />
								<p class="clear">{l s='If checked, COD crrecteed manual only' mod='ecm_novaposhta'}</p>
							</div>
							
						{*	<label>{l s='Ignore real paid for COD' mod='ecm_novaposhta'}</label>
							<div class="margin-form">
							<input type="checkbox" name="ignore_real_paid" value="1" {$ignore_real_paid} />
								<p class="clear">{l s='If checked, calculation COD ignore real paid' mod='ecm_novaposhta'}</p>
							</div> *}
						</div>
					</div>
				
					<label>{l s='Sender pay' mod='ecm_novaposhta'}</label>
					<div class="margin-form">
					<input type="checkbox" name="senderpay" value="1" {$senderpay} />
						<p class="clear">{l s='If checked, sender pay for delivery' mod='ecm_novaposhta'}</p>
					</div>

					<label>{l s='Add shipping cost to COD' mod='ecm_novaposhta'}</label>
					<div class="margin-form">
					<input type="checkbox" name="add_COD" value="1" {$add_COD} />
						<p class="clear">{l s='If checked, amount of shipiing cost added to COD' mod='ecm_novaposhta'}
						({l s='we hope you understand what you are doing' mod='ecm_novaposhta'})</p>
					</div>

					<div class="panel">
						<div class="panel-heading">{l s='Free shipping' mod='ecm_novaposhta'}</div>
						<label>{l s='Free Limit' mod='ecm_novaposhta'} ({$sign})</label>
						<div class="margin-form">
							<input type="text" name="FreeLimit" value="{$FreeLimit}"/><input title="{l s='for address delivery' mod='ecm_novaposhta'}" type="text" name="FreeLimitAddr" value="{$FreeLimitAddr}"/>
							<p class="clear">{l s='Limit total paid for free shipping' mod='ecm_novaposhta'}</p>
						</div>
	
						<label>{l s='Maximum weight' mod='ecm_novaposhta'}</label>
						<div class="margin-form">
							<input type="text" name="FreeLimitMaxWeight" value="{$FreeLimitMaxWeight}"/><input title="{l s='for address delivery' mod='ecm_novaposhta'}" type="text" name="FreeLimitMaxWeightAddr" value="{$FreeLimitMaxWeightAddr}"/>
							<p class="clear">{l s='Maximum total weight for free shipping' mod='ecm_novaposhta'}</p>
						</div>
	
						<label>{l s='Ignore free limit' mod='ecm_novaposhta'}</label>
						<div class="margin-form">
						<input type="checkbox" name="ignore_freelimit" value="1" {$ignore_freelimit} />
							<p class="clear">{l s='If checked, free limit ignore when update order' mod='ecm_novaposhta'}</p>
						</div>
					
					</div>			
					
					
					<label>{l s='Buyer pay' mod='ecm_novaposhta'}</label>
					<div class="margin-form">
					<input type="checkbox" name="buyerpay" value="1" {$buyerpay} />
						<p class="clear">{l s='If checked, buyer pay for delivery, when order total less for free shipping' mod='ecm_novaposhta'}</p>
						<p class="clear">{l s='This setting have highest priority !!!' mod='ecm_novaposhta'}</p>
						<p class="clear">{l s='Please set correctly free limit in this module!!!' mod='ecm_novaposhta'}</p>
					</div>
				
					

					<label>{l s='Sender pay for redelivery' mod='ecm_novaposhta'}</label>
					<div class="margin-form">
					<input type="checkbox" name="senderpay_redelivery" value="1" {$senderpay_redelivery} />
						<p class="clear">{l s='If checked, sender pay for redelivery' mod='ecm_novaposhta'}</p>
					</div>
				
					<label>{l s='Не включать доставку в итоговую сумму' mod='ecm_novaposhta'}</label>
					<div class="margin-form">
					<input type="checkbox" name="no_add_to_total" value="1" {$no_add_to_total} />
						<p class="clear">{l s='Если включено, то доставка никогда не будет включаться в итоговую сумму счета' mod='ecm_novaposhta'}</p>
						<p class="clear" style="color:red;"><strong>{l s='Настройка только формирует счет без учета доставки, отображение в корзине, в счетах и письмах требует самостоятельной доработки соответствующих шаблонов  и классов.' mod='ecm_novaposhta'}</strong></p>
					</div>
				
				</div>	


					<label>{l s='Message length' mod='ecm_novaposhta'}</label>
					<div class="margin-form">
						<input type="text" name="TrimMsg" value="{$TrimMsg}"/>
						<p class="clear">{l s='Default message length 100 charachters' mod='ecm_novaposhta'}.
						{l s='To increase this value, please contact Support NP' mod='ecm_novaposhta'}</p>
					</div>

					<label>{l s='Cargo Types' mod='ecm_novaposhta'}</label>
					<div class="margin-form">
						{html_options name=CargoType options=$CargoTypes selected=$CargoType}					
						<p class="clear">{l s='Select default cargo type' mod='ecm_novaposhta'}</p>
					</div>
				
					<label>{l s='Time B' mod='ecm_novaposhta'}</label>
					<div class="margin-form">
						<input name="time" value="{$time}"/>
						<p class="clear">{l s='Set time for automatic shift date on next day from this time to 00:00' mod='ecm_novaposhta'}</p>
					</div>
				
					<label>{l s='Default weght' mod='ecm_novaposhta'}</label>
					<div class="margin-form">
						<input name="weght" value="{$weght}"/>
						<p class="clear">{l s='Set minimal weght' mod='ecm_novaposhta'}</p>
					</div>
				
					<label>{l s='Default volumetric weght' mod='ecm_novaposhta'}</label>
					<div class="margin-form">
						<input name="vweght" value="{$vweght}"/>
						<p class="clear">{l s='Set minimal volumetric weght' mod='ecm_novaposhta'}</p>
					</div>
					
					
				</td>
			</tr>
		</table>
	<center><hr><input class="button" type="submit" name="submitUPDATE" value="{l s='Save' mod='ecm_novaposhta'}" /></center>
</div>
</fieldset>
{literal}
<script>
//getlastarea($("#customer").val());
</script>
{/literal}