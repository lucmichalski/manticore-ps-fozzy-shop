{if $order_details.id_carrier == $np_id}

<div class="row">
<div class="col-lg-12">
<div class="panel" id="delivery_panel">
<div class="panel-heading"><i class="icon-truck "></i>
	{l s='Details of Nova Poshta delivery' mod='ecm_novaposhta'} <span class="badge"> № {$order_details.id_carrier} </span>
	{l s='address' mod='ecm_novaposhta'} <span class="badge"> № {$order_details.id_address_delivery} </span>
</div>

<fieldset id="np_fieldset">
<input id="id_lang" name="id_lang" type="hidden" value ="{$employee->id_lang}"/>
<input id="employee" name="employee" type="hidden" value ="-{$employee->id}"/>
<input id="customer" name="customer" type="hidden" value ="{$order_details.id_customer}"/>
<input id="md_page" name="page" type="hidden" value ="order"/>
<input id="id_order" name="id_order" type="hidden" value ="{$id_order}"/>
<input id="id_address" name="id_address" type="hidden" value ="{$order_details.id_address_delivery}"/>
<input id="id_ware_od" name="id_ware_od" type="hidden" value ="{$order_details.ware}"/>
<input id="id_city_od" name="id_city_od" type="hidden" value ="{$order_details.city}"/>
<input id="id_area_od" name="id_area_od" type="hidden" value ="{$order_details.area}"/>
<input id="weight_od" name="weight_od" type="hidden" value ="{$order_details.weight_od}"/>
<input id="vweight_od" name="vweight_od" type="hidden" value ="{$order_details.vweight_od}"/>
<input id="id_cart" name="id_cart" type="hidden" value ="{$order_details.id_cart}"/>
<input id="total_paid_real" name="total_paid_real" type="hidden" value ="{$order_details.total_paid_real}"/>
<input id="insurance_od" name="insurance_od" type="hidden" value ="{$order_details.insurance}"/>
<input id="cod_value_od" name="cod_value_od" type="hidden" value ="{Tools::ps_round($order_details.total_products_wt-$order_details.total_discounts)}"/>
<input id="x" name="x" type="hidden" value ="{$order_details.x}"/>
<input id="y" name="y" type="hidden" value ="{$order_details.y}"/>
<input id="ware" name="ware" type="hidden" value ="{$order_details.ware}"/>
<input id="free_limit" name="free_limit" type="hidden" value ="{$order_details.free_limit}"/>
<input id="capital_top" name="capital_top" type="hidden" value ="{Configuration::get('ecm_np_capital_top')}"/> 

{if $order_details.nal == "1" || $order_details.nal == "2"}{assign var=nal_checked value="checked='checked'"}{else}{assign var=nal_checked value=""}{/if}
{if $order_details.customsize == "1"}{assign var=redelivery value="checked='checked'"}{else}{assign var=redelivery value=""}{/if}
{if $order_details.senderpay == "1"}{assign var=pay_checked value="checked='checked'"}{else}{assign var=pay_checked value=""}{/if}
{if $order_details.senderpaynal == "1" || $order_details.nal == "2"}{assign var=nal_pay_checked value="checked='checked'"}{else}{assign var=nal_pay_checked value=""}{/if}
{if $order_details.insurance == "0"}{assign var=insurance value=$order_details.total_products_wt}{else}{assign var=insurance value=$order_details.insurance}{/if}
{if !isset($format)}{assign var=format value="pdf"}{/if}
{$address_default = Configuration::get('ecm_np_address_delivery') and Configuration::get('ecm_np_address_delivery_def')}



{if $order_details.another_recipient==''} 
	{assign var=collapse_u value='style="display: none;" '}
	{assign var=collapse_m value=''}
{else}
	{assign var=collapse_u value=''}
	{assign var=collapse_m value='style="display: none;" '}
{/if}

<table class="table">
	<tr><td class="col-md-2"></td><td class="col-md-6"></td>
	<td class="col-md-4" rowspan="10" id="for_map_canvas"><div id="map_canvas"></div></td>
	</tr>
	<tr>
		<td >{l s='EDRPOU' mod='ecm_novaposhta'}</td>
		<td >
			<input class="form-control fixed-width-xxl"id="counterparty" name="counterparty" type="hidden" value ="{$order_details.counterparty}"/>
			<input class="form-control fixed-width-xxl"id="edrpou" name="edrpou" value="{$order_details.edrpou}"></input>
			<a onclick="edrpou()" class="button button-small btn btn-default" title="{l s='Search or Create Counterparty by EDRPOU' mod='ecm_novaposhta'}">
				<span> <i class="icon-search"></i> {l s='Search/Create' mod='ecm_novaposhta'} </span>
			</a>
			<p class="help-block edrpou">{if $order_details.edrpou} {$order_details.EDRPOUinfo.0->CounterpartyFullName}, {$order_details.EDRPOUinfo.0->CityDescription}{/if}</p>
		</td>
	</tr>
	<tr {$collapse_m} class="for_collapse_m">
		<td >{l s='First name' mod='ecm_novaposhta'}</td>
		<td ><input class="form-control fixed-width-xxl"oninvalid="setCustomValidity('Пишіть українською !!! ')" required
		title="Ім'я" oninput="delaysave(4000)" pattern="^[\-`'А-Яа-яЇїІіЄєЁё\s]+$" 
		id="md_firstname" name="firstname" value="{$order_details.firstname}"></input></td>
	</tr>
	<tr {$collapse_m} class="for_collapse_m">
		<td>{l s='Last name' mod='ecm_novaposhta'}</td>
		<td><input class="form-control fixed-width-xxl"oninvalid="setCustomValidity('Пишіть українською !!! ')" required
		title="Прізвище" oninput="delaysave(4000)" pattern="^[\-`'А-Яа-яЇїІіЄєЁё\s]+$" 
		id="md_lastname" name="lastname" value="{$order_details.lastname}"></input></td>
	</tr>
	{if Configuration::get('ecm_simcheck_middlename') or Configuration::get('ecm_checkout_middlename')}
	<tr {$collapse_m} class="for_collapse_m">
		<td>{l s='Middle name' mod='ecm_novaposhta'}</td>
		<td><input class="form-control fixed-width-xxl"oninvalid="setCustomValidity('Пишіть українською !!! ')"
		title="По батькові" oninput="delaysave(4000)" pattern="^[\-`'А-Яа-яЇїІіЄєЁё\s]+$" 
		id="md_middlename" name="middlename" value="{$order_details.middlename}"></input></td>
	</tr>
	{/if}	
	<tr {$collapse_m} class="for_collapse_m">
		<td>{l s='Mobile phone' mod='ecm_novaposhta'}</td>
		<td><input class="form-control fixed-width-xxl"oninvalid="setCustomValidity('Формат вводу 0501234567')" required 
		pattern="\+?[38]?8?0\d\d ?-?\d\d\d ?-?\d\d ?-?\d\d" oninput="delaysave(4000)"
		title="Формат вводу 0501234567"
		id="md_phone" name="phone" value="{$order_details.phone}"></input></td>
	</tr>

	<tr>
		<td>{l s='Another recipient' mod='ecm_novaposhta'}</td>
		<td><input class=""{$order_details.another_recipient} id="another_recipient" name="another_recipient" value="0" type="checkbox" onchange="another_update()"></td>
	</tr>

 	<tr {$collapse_u} class="for_collapse_u">
		<td >{l s='First name' mod='ecm_novaposhta'}</td>
		<td ><input class="form-control fixed-width-xxl"oninvalid="setCustomValidity('Пишіть українською !!! ')"
		title="Ім'я" oninput="delaysave(4000)" pattern="^[\-`'А-Яа-яЇїІіЄєЁё\s]+$" 
		id="another_firstname" name="another_firstname" value="{$order_details.another_firstname}"class="another"/>
		 ({l s='another recipient' mod='ecm_novaposhta'})</td>
	</tr>
	<tr {$collapse_u} class="for_collapse_u">
		<td>{l s='Last name' mod='ecm_novaposhta'}</td>
		<td><input class="form-control fixed-width-xxl"oninvalid="setCustomValidity('Пишіть українською !!! ')"
		title="Прізвище" oninput="delaysave(4000)" pattern="^[\-`'А-Яа-яЇїІіЄєЁё\s]+$" 
		id="another_lastname" name="another_lastname" value="{$order_details.another_lastname}"  class="another"/>
		 ({l s='another recipient' mod='ecm_novaposhta'})</td>
	</tr>
	{if Configuration::get('ecm_simcheck_middlename') or Configuration::get('ecm_checkout_middlename')}
	<tr {$collapse_u} class="for_collapse_u">
		<td>{l s='Middle name' mod='ecm_novaposhta'}</td>
		<td><input class="form-control fixed-width-xxl"oninvalid="setCustomValidity('Пишіть українською !!! ')"
		title="По батькові" oninput="delaysave(4000)" pattern="^[\-`'А-Яа-яЇїІіЄєЁё\s]+$" 
		id="another_middlename" name="another_middlename" value="{$order_details.another_middlename}" class="another"/>
		 ({l s='another recipient' mod='ecm_novaposhta'})</td>
	</tr>
	{/if}
	<tr {$collapse_u} class="for_collapse_u">
		<td>{l s='Mobile phone' mod='ecm_novaposhta'}</td>
		<td><input class="form-control fixed-width-xxl" oninvalid="setCustomValidity('Формат вводу 0501234567')" 
		pattern="\+?[38]?8?0\d\d ?-?\d\d\d ?-?\d\d ?-?\d\d" oninput="delaysave(4000)"
		title="Формат вводу 0501234567"
		id="another_phone" name="another_phone" value="{$order_details.another_phone}" class="another"/>
		 ({l s='another recipient' mod='ecm_novaposhta'})</td>
	</tr>
	<tr>
		<td>{l s='Area' mod='ecm_novaposhta'}</td>
		<td>
			<select name="area" id="id_area_delivery" class="address_select form-control" onchange="refreshcity()">
				{foreach from=$Areas item=area key=key}
					<option value="{$key}" area_id="{substr($area,0,3)}" {if $key == $order_details.area} selected="selected" {/if} >{substr($area,3)}</option>
				{/foreach}
			</select>
		</td>
	</tr>
	<tr>
		<td>{l s='City' mod='ecm_novaposhta'}</td>
		<td>
			<select name="city" id="id_city_delivery" class="address_select form-control"
			onchange="refreshware()">
			{html_options options=$Citys selected=$order_details.city}
			</select>
		</td>
	</tr>
	<tr>
		<td>{l s='Warehouse' mod='ecm_novaposhta'}</td>
		<td>
			<select name="ware" id="id_ware_delivery" class="address_select form-control" 
			onchange="saveform()">
			{html_options options=$Wares selected=$order_details.ware}
			</select>
		</td>
	</tr>
	{if $order_details.ware!='1'} {assign var=collapse value='style="display: none;" '}{else}{assign var=collapse value=''}{/if}
	<tr {$collapse} class="for_collapse">	
		<td>{l s='Street' mod='ecm_novaposhta'}</td>
		<td><input class="form-control fixed-width-xxl np_ontyped" id="StreetName" name="StreetName" value="{$order_details.StreetName}"/></td>
	</tr>
	<tr {$collapse} class="for_collapse">	
		<td>{l s='Building' mod='ecm_novaposhta'}</td>
		<td><input class="form-control fixed-width-xxl np_ontyped" id="BuildingNumber" name="BuildingNumber" value="{$order_details.BuildingNumber}"/></td>
	</tr>
	<tr {$collapse} class="for_collapse">	
		<td>{l s='Flat' mod='ecm_novaposhta'}</td>
		<td><input class="form-control fixed-width-xxl np_ontyped"id="Flat" name="Flat" value="{$order_details.Flat}"/></td>
	</tr>
	<tr>
		<td>{l s='Weight' mod='ecm_novaposhta'}</td>
		<td><input class="form-control fixed-width-xxl"id="weight" name="weight" value="{$order_details.weight}"></input>&nbsp;
			<a onclick="copy('weight')" class="button button-small btn btn-default" title="{l s='Get weight from order' mod='ecm_novaposhta'}">
			<span> <i class="icon-refresh"></i> {l s='From order' mod='ecm_novaposhta'} </span>
			</a>
		</td>
	</tr>
	<tr>
		<td>{l s='Volumetric weight' mod='ecm_novaposhta'}</td>
		<td><input class="form-control fixed-width-xxl"id="vweight" name="vweight" value="{$order_details.vweight}"></input>&nbsp;
			<a onclick="copy('vweight')" class="button button-small btn btn-default" title="{l s='Get volumetric weight from order' mod='ecm_novaposhta'}">
			<span> <i class="icon-refresh"></i> {l s='From order' mod='ecm_novaposhta'} </span>
			</a>
			<a onclick="$('.calculator').toggleClass('hidden')" class="button button-small btn btn-default" title="{l s='Manual calculate' mod='ecm_novaposhta'}">
			<span> <i class="icon-edit"></i> {l s='Calculator' mod='ecm_novaposhta'} </span>
			</a>
			<div class="hidden calculator">
			<input class="form-control fixed-width-sm"placeholder="Ш(см)" value="" class="oncalc x"/> x
			<input class="form-control fixed-width-sm"placeholder="В(см)" value="" class="oncalc y"/> x
			<input class="form-control fixed-width-sm"placeholder="Г(см)" value="" class="oncalc z"/>
			</div>
		</td>
	</tr>
	<tr>
		<td>{l s='Description' mod='ecm_novaposhta'}</td>
		<td><input class="form-control fixed-width-xxl"id="description" name="description" value="{$order_details.description}"></input></td>
	</tr>
	<tr>
		<td>{l s='Pack' mod='ecm_novaposhta'}</td>
		<td><input class="form-control fixed-width-xxl"id="pack" name="pack" value="{$order_details.pack}"></input></td>
	</tr>
	<tr>
		<td>{l s='Packing Number' mod='ecm_novaposhta'}</td>
		<td><input class="form-control fixed-width-xxl"id="PackingNumber" name="PackingNumber" value="{$order_details.PackingNumber}"></input></td>
	</tr>
	<tr>
		<td>{l s='Seats Amount' mod='ecm_novaposhta'}</td>
		<td><input class="form-control fixed-width-xxl"id="seats_amount" name="seats_amount" value="{$order_details.seats_amount}"></input></td>
	</tr>
	<tr>
		<td style="color: red;font-weight: bold;">{l s='RedBox barcode' mod='ecm_novaposhta'}</td>
		<td style="color: red;"><input class="form-control fixed-width-xxl"id="RedBoxBarcode" name="RedBoxBarcode" value="{$order_details.RedBoxBarcode}"></input></td>
	</tr>
	<tr>
		<td>{l s='Internal order number' mod='ecm_novaposhta'}</td>
		<td><input class="form-control fixed-width-xxl"id="InfoRegClientBarcodes" name="InfoRegClientBarcodes" value="{$order_details.InfoRegClientBarcodes}"></input></td>
	</tr>
	<tr style="display:none">
		<td>{l s='Cargo Types' mod='ecm_novaposhta'}</td>
		<td>{html_options name=CargoType options=$CargoTypes selected=$CargoType}</td>
	</tr>
	<tr>
		<td>{l s='Amount of insurance' mod='ecm_novaposhta'} ({$order_details.sign})
		{if Configuration::get('ecm_np_insurance')!=0} (ограничение {Configuration::get('ecm_np_insurance')}){/if}</td>
		<td><input class="form-control fixed-width-xxl"id="insurance" name="insurance" value="{$insurance}"></input>&nbsp;
			<a onclick="copy('insurance')" class="button button-small btn btn-default" title="{l s='Get amount of insurance from order' mod='ecm_novaposhta'}">
			<span> <i class="icon-refresh"></i> {l s='From order' mod='ecm_novaposhta'} </span>
			</a>
		</td>
	</tr>
	<tr>
		<td>{l s='COD amount' mod='ecm_novaposhta'}   ({$order_details.sign})</td>
		<td><input class="form-control fixed-width-xxl"id="cod_value" name="cod_value" value="{$order_details.cod_value}"></input>&nbsp;
			<a onclick="copy('cod_value')" class="button button-small btn btn-default" title="{l s='Get COD amount from order' mod='ecm_novaposhta'}">
			<span> <i class="icon-refresh"></i> {l s='From order' mod='ecm_novaposhta'} </span>
			</a>
		</td>
	</tr>
	<tr>
		<td>{l s='Cache on delivery' mod='ecm_novaposhta'}</td>
		<td>
		<select name="cod" id="cod" class="address_select form-control">
			{html_options options=$cod selected=$order_details.nal}
		</select>
		</td>
	</tr>

	<tr>
		<td>{l s='Payment Method' mod='ecm_novaposhta'}</td>
		<td>
			<select name="payment_method" id="payment_method" class="address_select form-control payment_method" >
			{html_options options=$payment_forms selected=$payment_form}
			</select>
		</td>
	</tr>


	<tr>
		<td>{l s='Sender pay for delivery' mod='ecm_novaposhta'}</td>
		<td><input class=""{$pay_checked} id="pay_check" name="pay_check" value="0" type="checkbox"></td>
	</tr>
	<tr>
		<td>{l s='Sender pay for Cache on delivery' mod='ecm_novaposhta'}</td>
		<td><input class=""{$nal_pay_checked} id="nal_pay_check" name="nal_pay_check" value="0" type="checkbox"></td>
	</tr>
	<tr>
		<td>{l s='Add redelivery to COD' mod='ecm_novaposhta'}</td>
		<td><input class=""{$redelivery} id="redelivery" name="redelivery" value="0" type="checkbox"></td>
	</tr>

	<tr {if count($cards)==1}style="display:none"{/if}>
		<td>{l s='COD on card' mod='ecm_novaposhta'}</td>
		<td>			
			<select name="on_card" id="on_card" class="address_select form-control">
			{html_options options=$cards selected=$card}
			</select>
		</td>
	</tr>

	<tr>
		<td>
			{$TrimMsg} {l s='characters maximum !' mod='ecm_novaposhta'} 
			{l s='Left' mod='ecm_novaposhta'} - 
			<span id="counter">{$TrimMsg - mb_strlen($order_details.msg)}</span>
		</td>
		<td><input class="form-control fixed-width-xxl"id="TrimMsg" name="TrimMsg" type="hidden" value ="{$TrimMsg}"/>
			<textarea id="msg" name="msg" value="{$order_details.msg}" 
			onclick="length_check({$TrimMsg}, 'msg', 'counter')" 
			onkeyup="length_check({$TrimMsg}, 'msg', 'counter')"
			>{$order_details.msg}</textarea>
			<a onclick="copy('msg');setTimeout('length_check({$TrimMsg}, \'msg\', \'counter\')',250)" class="button button-small btn btn-default" title="{l s='Get amount of insurance from order' mod='ecm_novaposhta'}">
			<span> <i class="icon-refresh"></i> {l s='From order' mod='ecm_novaposhta'} </span>
			</a>

		</td>
	</tr>
	<tr>
		<td>{l s='Departure date' mod='ecm_novaposhta'}</td>
		<td><input class="form-control fixed-width-xxl"id="data" name="data" value="{$data}"></input></td>
	</tr>
	<tr>
		<td>{l s='Warehouse' mod='ecm_novaposhta'}</td>
		<td>
			<span style="display:none">
			<select name="area_out" id="id_area_out" class="address_select form-control"
			onchange="refreshoutcity()"> 
			{foreach from=$Areas item=area key=key}
				<option value="{$key}" area_id="{substr($area,0,3)}"  {if $key == $outarea}selected="selected"{/if} >{substr($area,3)}</option>
			{/foreach}
			</select>
			<select name="city_out" id="id_city_out" class="address_select form-control"
			onchange="refreshoutware()"> 
			{html_options options=$outCitys selected=$outcity}
			</select>
			</span>
			<select name="ware_out" id="id_ware_out" class="address_select form-control" onchange="saveform_adm()">
			{html_options options=$outWares selected=$outware}
			</select>
		</td>
	</tr>
</table>

<hr>
<table>
	
	<tr>
		<td>
			{if $order_details.tracking_number == ""}
			{assign var=shipping_number value=""}
			<a onclick="makettn('{$order_details.id_order}','0')" class="button button-small btn btn-default pull-left" 
			title="{l s='Make EN' mod='ecm_novaposhta'}">
			<span> <i class="icon-truck"></i> {l s='Make EN' mod='ecm_novaposhta'} </span>
			</a>

			{else}
			{assign var=shipping_number value=$order_details.tracking_number}
			<a onclick="makettn('{$order_details.id_order}','1')" class="button button-small btn btn-default pull-left" 
			title="{l s='Update EN #' mod='ecm_novaposhta'}">
			<span> <i class="icon-refresh"></i> {l s='Update EN #' mod='ecm_novaposhta'} </span>
			</a>&nbsp;<span class="badge">{$order_details.tracking_number}</span>
			{/if}
		</td>
		<td rowspan="4" valign="top"><div id="result" name="result" style="max-width:400px"> </div></td>
	</tr>
	{if $shipping_number == ""}
	<tr>
		<td><br>
			<a onclick="cost()" class="button button-small btn btn-default"
				title="{l s='Recalculate shipping' mod='ecm_novaposhta'}">
				<span> <i class="icon-refresh"></i> 
				{l s='Recalculate shipping' mod='ecm_novaposhta'} </span>
			</a>
		</td>
	</tr>
	{else}
	<tr>
		<td><br>
			<a href="https://my.novaposhta.ua/orders/printDocument/orders/{$order_details.ref}/type/{$format}/apiKey/{$api_key}/zebra/zebra" target="_blank"
							  class="button button-small btn btn-default" title="{l s='Print Mark' mod='ecm_novaposhta'}">
				<span> <i class="icon-print"></i> {l s='TTN' mod='ecm_novaposhta'} </span>
			</a>
			<a href="https://my.novaposhta.ua/orders/printMarking100x100/orders/{$order_details.ref}/type/{$format}/apiKey/{$api_key}/zebra/zebra" target="_blank"
							  class="button button-small btn btn-default" title="{l s='Print Mark' mod='ecm_novaposhta'}">
				<span> <i class="icon-print"></i> {l s='Mark 100x100' mod='ecm_novaposhta'} </span>
			</a>
			<a style="display:none"
			onclick="CheckPossibilityCreateReturn('{$order_details.tracking_number}')" class="button button-small btn btn-default" title="{l s='Check for possibility parcel return' mod='ecm_novaposhta'}">
				<span> <i class="icon-refresh"></i> {l s='Return is possibility?' mod='ecm_novaposhta'} </span>
			</a>
			<a onclick="deletettn('{$order_details.id_order}')" class="button button-small btn btn-default" title="{l s='Delete TTN' mod='ecm_novaposhta'}">
				<span> <i class="icon-trash"></i> {l s='Delete TTN' mod='ecm_novaposhta'} </span>
			</a>
			<a onclick="checkpackage('{$shipping_number}','{$order_details.id_order}')" class="button button-small btn btn-default" title="{l s='Tracking parcel' mod='ecm_novaposhta'}">
				<span> <i class="icon-eye"></i> {l s='Tracking' mod='ecm_novaposhta'} </span>
			</a>
		</td>
	</tr>
	<tr>
		<td><br>
			<div class="alert alert-warning">
			<strong>{l s='After printing document don’t  editable!!!' mod='ecm_novaposhta'}</strong>
			</div>
		</td>
	</tr>
	{/if}
	{if $order_details.item_quantity > 1}
	<tr>
		<td><br>
			<a onclick="splitorder('{$order_details.id_order}')" class="button button-small btn btn-default pull-left" 
			title="{l s='Split order' mod='ecm_novaposhta'}">
			<span> {l s='Split into' mod='ecm_novaposhta'} {$order_details.item_quantity} {l s='separate orders' mod='ecm_novaposhta'} </span>
			</a>
		</td>
	</tr>
	{/if}
	<tr>
		<td><br>
			<div class="alert alert-warning">
			<strong>{l s='After changing products/price/quantity please reload page' mod='ecm_novaposhta'}. </br>
					{l s='Do not forget to check the amount of insurance and COD' mod='ecm_novaposhta'} !!!</strong>
			</div>
		</td>
	</tr>
</table>

</fieldset>
</div>
</div>
</div>


{if $shipping_number != ""}
<script>
	checkpackage('{$shipping_number}','{$order_details.id_order}')
</script>
{/if}

{if $order_details.x != 0 and $order_details.y != 0  and Configuration::Get('PS_API_KEY')}
	{if $version >= '1.7'}
	<script>
		$(document).ready(function(){
			var script = document.createElement('script');
			script.type = 'text/javascript';
			script.async = true;
			script.defer = true;
			script.src = 'https://maps.googleapis.com/maps/api/js?key={Configuration::Get("PS_API_KEY")}&callback=initialize';
			document.body.appendChild(script);
		});
	</script>
	{/if}

	<script>

	$(document).ready(function(){
		if(Number($("#x").val())!=0){
			$("#map_canvas").css("width", "300px");
			$("#map_canvas").css("height", "300px");
			initialize();
		}else{
			$("#gmnoscreen").addEventListener("DOMAttrModified", function (ev) {
				$("#map_canvas").css("width", "190px");
				$("#map_canvas").css("height", "190px");
				$("#map-delivery-canvas").appendTo($("#map_canvas"));
			}, false);
			
			$("#map-delivery-canvas").on("DOMSubtreeModified", function (event) { 
				$("#map_canvas").css("width", "190px");
				$("#map_canvas").css("height", "190px");
				$("#map-delivery-canvas").appendTo($("#map_canvas"));
			});
		}
	});

	function initialize(){     
		var myLatlng = new google.maps.LatLng($("#y").val(), $("#x").val());
		var myOptions = {
			zoom: 15,
			center: myLatlng,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		}
		var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
		var marker = new google.maps.Marker({
			position: myLatlng,
			map: map,
			title: $("#id_ware_delivery :selected").text() 
		});	
	}

	</script>
{/if}
{else}
{/if}

