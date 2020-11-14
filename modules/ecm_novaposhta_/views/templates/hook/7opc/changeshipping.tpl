	<input id="ac" name="ac" type="hidden" value ="{$ac}"/>
	<input id="id_lang" name="id_lang" type="hidden" value ="{$id_lang}"/>
	<input id="customer" name="customer" type="hidden" value ="{$customer_id}"/>
	<input id="md_page" name="page" type="hidden" value ="cart"/>
	<input id="employee" name="employee" type="hidden" value ="{$employee}"/>
	<input id="cart_id" name="cart_id" type="hidden" value ="{$cart_id}"/>
	<input id="carrier" name="carrier" type="hidden" value ="{$id_carrier}"/>
	<input id="fixcost" name="fixcost" type="hidden" value ="{$fixcost}"/>
	<input id="fill" name="fill" type="hidden" value ="{$fill}"/>
	<input id="mdcost" name="mdcost" type="hidden" value ="{round($cart_np.cost)}"/>
	<input id="total_wt" name="total_wt" type="hidden" value ="{$cartdetails.total_wt}"/>
	<input id="capital_top" name="capital_top" type="hidden" value ="{Configuration::get('ecm_np_capital_top')}"/> 
		<table class="resume_ table_ table-bordered_">
			<tr><td>
				<table class="table-select">
				{if $show_another}
				<tr>
					<td colspan=4>
						<label for="another_recipient">
                        <input id="another_recipient" name="another_recipient" class="not_unifrom not_uniform" 
                            type="checkbox" {$cart_np.another_recipient} onchange="another_update()">
                        {l s='Another recipient' mod='ecm_novaposhta'}
                        </label>
					
					</td>
				</tr>
				{if $cart_np.another_recipient !=''}{$required='required'}{else}{$required=''}{/if}
				<tr class="another_recipient">
					<td><label for="another_firstname">{l s='First name' mod='ecm_novaposhta'}</label></td>
					<td colspan=3>
						<input name="another_firstname" id="another_firstname"
						pattern="^[А-Яа-яЇїІіЄє\s]+$" {$required}
						class="delivery form-control input-sm not_unifrom not_uniform  np_ontyped"
						value="{$cart_np.another_firstname}">
						</input>
					</td>
				</tr>
				<tr class="another_recipient">
					<td><label for="another_lastname">{l s='Last name' mod='ecm_novaposhta'}</label></td>
					<td colspan=3>
						<input name="another_lastname" id="another_lastname"
						pattern="^[А-Яа-яЇїІіЄє\s]+$" {$required}
						class="delivery form-control input-sm not_unifrom not_uniform  np_ontyped"
						value="{$cart_np.another_lastname}">
						</input>
					</td>
				</tr>
				{*if Configuration::get('ecm_simcheck_middlename') or Configuration::get('ecm_checkout_middlename')}
				<tr class="another_recipient">
					<td><label for="another_middlename">{l s='Middle name' mod='ecm_novaposhta'}</label></td>
					<td colspan=3>
						<input name="another_middlename" id="another_middlename"
						pattern="^[А-Яа-яЇїІіЄє\s]+$" {$required}
						class="delivery form-control input-sm not_unifrom not_uniform  np_ontyped"
						value="{$cart_np.another_middlename}">
						</input>
					</td>
				</tr>
				{/if*}
				<tr class="another_recipient">
					<td><label for="another_phone">{l s='Phone' mod='ecm_novaposhta'}</label></td>
					<td colspan=3>
						<input name="another_phone" id="another_phone" {$required}
						pattern="\+?[38]?8?0\d\d ?-?\d\d\d ?-?\d\d ?-?\d\d" title="Формат вводу 050-123-45-67"
						class="delivery form-control input-sm not_unifrom not_uniform  np_ontyped"
						value="{$cart_np.another_phone}">
						</input>
					</td>
				</tr>
				{/if}
				<tr>
					<td><label for="id_area_delivery">{l s='Area' mod='ecm_novaposhta'}</label></td>
					<td colspan=3>
						<select name="id_area_delivery" id="id_area_delivery"
						class="delivery form-control form-control-select " onchange="refreshcity()">
				{foreach from=$Areas item=area key=key}
					<option value="{$key}" area_id="{substr($area,0,3)}"  {if $key == $cart_np.area}selected="selected"{/if} >{substr($area,3)}</option>
				{/foreach}
						</select>
					</td>
				</tr>
				<tr>
					<td><label for="id_city_delivery">{l s='City' mod='ecm_novaposhta'}</label></td>
					<td colspan=3>
						<select name="id_city_delivery" id="id_city_delivery"
						class="delivery form-control  form-control-select " onchange="cost_by_city();refreshware();">
				{html_options options=$Citys selected=$cart_np.city}
						</select>
					</td>
				</tr>
				<tr>
					<td><label for="id_ware_delivery" style="margin-right: 1rem;">{l s='Postoffice' mod='ecm_novaposhta'}</label></td>
					<td colspan=3>
						<select name="id_ware_delivery" id="id_ware_delivery"
						class="delivery form-control  form-control-select " onchange="saveform(0)">
				{html_options options=$Wares selected=$cart_np.ref}
						</select>
					</td>
				</tr>

			{if Configuration::get('ecm_np_address_delivery')}
				<tr>
					<td colspan=4>
						<label for="np_address_delivery">
						<input id="np_address_delivery" name="np_address_delivery" class="not_unifrom not_uniform"
						type="checkbox" {if $cart_np.ref == '1'}checked {/if} value="1"/>
						{$address_delivery_label}
						</label>
					</td>
				</tr>

				{if $cart_np.ref!='1'} {assign var=collapse value='style="display: none;" '}{else}{assign var=collapse value=''}{/if}
				<tr {$collapse} class="np_address_delivery">
					<td><label for="StreetName">{l s='Street' mod='ecm_novaposhta'}</label></td>
					<td colspan=3>
						<input name="StreetName" id="StreetName"
						pattern="^[\-`'0-9А-Яа-яЇїІіЄєЁё\s]+$" 
						class="delivery form-control input-sm not_unifrom not_uniform  np_ontyped"
						value="{$cart_np.StreetName}">
						</input>
					</td>
				</tr>
				<tr {$collapse} class="np_address_delivery">
					<td><label for="BuildingNumber">{l s='Building' mod='ecm_novaposhta'}</label></td>
					<td>
						<input name="BuildingNumber" id="BuildingNumber" 
						class="delivery form-control input-sm not_unifrom not_uniform  np_ontyped"
						value="{$cart_np.BuildingNumber}">
						</input>
					</td>
					<td><label for="Flat" class="mx-2">{l s='Flat' mod='ecm_novaposhta'}</label></td>
					<td>
						<input name="Flat" id="Flat"
						class="delivery form-control input-sm not_unifrom not_uniform  np_ontyped"
						value="{$cart_np.Flat}">
						</input>
					</td>
				</tr>
			{/if}

	{if $show}
	<tr><td colspan=4>
	<div class="form-group">
		<label class="form-control-label">{l s='Details of Nova Poshta delivery' mod='ecm_novaposhta'}</label>
		<div class="">
			{l s='Total weight' mod='ecm_novaposhta'} - <strong>{$cartdetails.weight}</strong> кг.
			{l s='Volumeric weight' mod='ecm_novaposhta'} - <strong>{$cartdetails.vweight}</strong> кг.<br>
			{l s='Cost of order' mod='ecm_novaposhta'} - <strong>{Tools::displayPrice($cartdetails.total_wt)}</strong><br>
			{if isset($cart_np.cost)}
				{l s='Shiping cost' mod='ecm_novaposhta'} - <strong><span id="md_cost">{Tools::displayPrice($cart_np.cost)}</span></strong><br>
				{l s='Comiso of COD' mod='ecm_novaposhta'} - <strong><span id="md_costredelivery">{Tools::displayPrice($cart_np.costredelivery)}</span></strong><br>
			{/if}

		</div>
	</div>
	</td></tr>
	{/if}
			</table>
			</td></tr>
		</table>

	<div id="refreshdelivery"></div>

	{$address_default = Configuration::get('ecm_np_address_delivery') and Configuration::get('ecm_np_address_delivery_def')}
	
	<script>
	var show = {$show|intval};
	var address_default = {$address_default|intval};
	</script>

<style>
#id_ware_delivery option[value='1']{
	font-size:110%;
	font-style: italic;
	font-weight: bold;
	background-color: #f6f6f6;
}
</style>


<!-- /Change shipping module -->
