{$address_default = Configuration::get('ecm_np_address_delivery') and Configuration::get('ecm_np_address_delivery_def')}

<script>
// <![CDATA[
	var ecm_np = '{Configuration::get("ecm_ecm_novaposhta")}';
	var ecm_carrier = '{Configuration::get("ecm_ecm_novaposhta")}';
	var md_carriers = JSON.parse('{Configuration::get("ecm_md_carriers")}');
	var md_page = "settings";
	var address_default = {$address_default|intval};
//]]>
</script>

<input id="countwh" name="countwh" type="hidden" value ="{$countwh}"/>
<input id="customer" name="customer" type="hidden" value ="-{$employee}"/>
<input id="md_page" name="page" type="hidden" value ="settings"/>
<input id="cart_id" name="cart_id" type="hidden" value ="0"/>

<fieldset>
<div class="panel">
	<div class="panel-heading">
		<i class="icon-gear"></i> {l s='Sender counterparty settings' mod='ecm_novaposhta'}
	</div>
		<table><tr><td width="48%">
		<div class="panel">
			<div class="panel-heading">
				<i class="icon-globe"></i> {l s='Areas, Citys and postoffices' mod='ecm_novaposhta'}
			</div>

		<label>{l s='Capital on top' mod='ecm_novaposhta'}</label>
		<div class="margin-form">
			<input type="checkbox" name="capital_top" value="1" {$capital_top} />
			<p class="clear">{l s='Show capital on top list' mod='ecm_novaposhta'}</p>
		</div>
			
		<label>{l s='Get a list of postoffices, cities and areas:' mod='ecm_novaposhta'}</label>
		<div class="margin-form">
			<input class="button" type="submit" name="submitWarehouse" value="{l s='Get' mod='ecm_novaposhta'}, {l s='current: ' mod='ecm_novaposhta'} {$countwh}" />
		</div>
		<hr>
		<div>
			<label for="id_area_delivery">{l s='Area' mod='ecm_novaposhta'}</label>
			<div class="margin-form">
				<select  name="id_area_delivery" id="id_area_delivery" class="address_select form-control" onchange="refreshcity()">
					{foreach from=$Areas item=area key=key}
						<option value="{$key}" area_id="{substr($area,0,3)}"  {if $key == $Area}selected="selected"{/if} >{substr($area,3)}</option>
					{/foreach}
				</select>
			</div>
		</div>
		<div>
			<label for="id_city_delivery">{l s='City' mod='ecm_novaposhta'}</label>
			<div class="margin-form">
					<select name="id_city_delivery" id="id_city_delivery" class="address_select form-control"
					onchange="refreshware()">
					{html_options options=$Citys selected=$City}
					</select>
			</div>
		</div>
		<div>
			<label for="id_ware_delivery">{l s='Postofice' mod='ecm_novaposhta'}</label>
			<div class="margin-form">
					<select  name="id_ware_delivery" id="id_ware_delivery" class="address_select form-control"
					onchange="saveform()">
					{html_options options=$Wares selected=$Ware}
					</select>
			</div>
		</div>

		<div id="sender_address">
		<hr>
		<div class="panel">
			<div class="panel-heading">
				<i class="icon-mail"></i> {l s='Sender address' mod='ecm_novaposhta'}
			</div>
			{if count($Areas)!=1}
			<label for="referal_street">{l s='Searche street, village' mod='ecm_novaposhta'}	</label>
			<input  type="hidden" name="Street" id="Street" value="{$StreetRef}"/>
			<input  type="hidden" name="StreetName" id="StreetName" value="{if $StreetRef}{$StreetName}{/if}"/>
			<input  type="hidden" name="StreetType" id="StreetType" value="{if $StreetRef}{$StreetType}{/if}"/>
			<div class="margin-form">
				<div class="input-group" style="width: 100%;">
					<span class="input-group-addon" name="StreetsType" id="StreetsType">{if $StreetRef}{$StreetType}{/if}</span>
					<input  type="text" placeholder="{l s='live search' mod='ecm_novaposhta'}" name="referal_street" id="referal_street"  value="{if $StreetRef}{$StreetName}{/if}" autocomplete="off"/>
				</div>
				<ul id="street_result"></ul>
				<p class="clear">{l s='Just start typing name (there letters minimum)' mod='ecm_novaposhta'}</p>
			</div>
			<label>{l s='Building / Street' mod='ecm_novaposhta'}</label>
			<div class="margin-form">
				<input name="BuildingNumber" id="BuildingNumber" onchange="saveform()"
				type="text" value="{$BuildingNumber}" />
				<p class="clear">{l s='Set building number or street name for village' mod='ecm_novaposhta'}</p>
			</div>
			<label>{l s='Flat / Building' mod='ecm_novaposhta'}</label>
			<div class="margin-form">
				<input name="Flat" id="Flat"   type="text" value="{$Flat}"  onchange="saveform()"/>
				<p class="clear">{l s='Set flat number or building number for village' mod='ecm_novaposhta'}</p>
			</div>
			{/if}
		</div>
		</div>
	</div>
	<div class="panel">
		<div class="panel-heading {if count($Areas)!=1}panel-hiddable{/if}">
			<i class="icon-cogs"></i> {l s='Blocked areas' mod='ecm_novaposhta'}
			<i class="icon-arrow-down"></i>
		</div>
		{if count($Areas)!=1}
		<div class="margin-form hidden">
			<select multiple size="{count($Areas)/3}" name="BlockedAreas[]" id="BlockedAreas" 
			class="address_select form-control">
			{foreach from=$Areas item=area key=key}
				{if $key != '000'}
				<option value="{$key}" area_id="{substr($area,0,3)}"  {if in_array($key,$BlockedAreas)}selected="selected"{/if} >{substr($area,3)}</option>
				{/if}
			{/foreach}
			</select>
			<p class="clear">{l s='Select (use Shift, Ctrl) areas that are not shown' mod='ecm_novaposhta'}</p>
		</div>
		{/if}
	</div>

	</td>
	<td width="4%">&nbsp;</td>
	<td width="48%">
		<div class="panel">
			<div class="panel-heading">
				<i class="icon-info"></i> {l s='Не удалять перевозчика' mod='ecm_novaposhta'}
			</div>
			<div class="margin-form hiddable">
				<input type="checkbox" name="DONT_TOUCH_CARRIER" value="1" {$DONT_TOUCH_CARRIER}/>
				<p class="clear">{l s='При сбросе модуля текущий перевозчик НоваяПочта удален не будет' mod='ecm_novaposhta'}</p>
				<p class="clear">{l s='Обычно сброс модуля необходим при обновлении версии' mod='ecm_novaposhta'}</p>
			</div>
		</div>

		<div class="panel">
			<div class="panel-heading">
				<i class="icon-cogs"></i> {l s='Blocked type of warehouse' mod='ecm_novaposhta'}
			</div>
			<div class="margin-form">
					<select multiple size="{count($WarehouseTypes)}" name="BlockedWarehouse[]" id="BlockedWarehouse" class="address_select form-control"
					onclick="saveform()">
					{html_options options=$WarehouseTypes selected=$BlockedWarehouse}
					</select>
					<p class="clear">{l s='Select (use Ctrl) warehouse types that are not shown' mod='ecm_novaposhta'}</p>
			</div>
			<label>{l s='One product - one place' mod='ecm_novaposhta'}</label>
			<div class="margin-form">
				<input type="checkbox" name="separatePlace" value="1" {$separatePlace} />
				<p class="clear">{l s='Show branches by weight of one product' mod='ecm_novaposhta'}</p>
			</div>
					

			<center><hr><input class="button" type="submit" name="submitUPDATE" value="{l s='Save' mod='ecm_novaposhta'}" /></center>

		</div>

	</td>

	</tr></table>
</div>
</fieldset>

<script>
$('.panel-hiddable').on('click', function() {
	$(this).next().toggleClass('hidden');
})
</script>