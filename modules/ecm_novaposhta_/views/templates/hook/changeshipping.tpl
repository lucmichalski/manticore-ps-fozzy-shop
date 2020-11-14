
<!-- ecm_novaposta shipping module -->
	<input id="ac" name="ac" type="hidden" value ="{$ac}"/>
	<input id="id_lang" name="id_lang" type="hidden" value ="{$cart->id_lang}"/>
	<input id="customer" name="customer" type="hidden" value ="{$cart->id_customer}"/>
	<input id="md_page" name="page" type="hidden" value ="cart"/>
	<input id="employee" name="employee" type="hidden" value ="{$employee}"/>
	<input id="cart_id" name="cart_id" type="hidden" value ="{$cart->id}"/>
	<input id="np" name="np" type="hidden" value ="{$np_id}"/>
	<input id="carrier" name="carrier" type="hidden" value ="{$cart->id_carrier}"/>
	<input id="fixcost" name="fixcost" type="hidden" value ="{$fixcost}"/>
	<input id="fill" name="fill" type="hidden" value ="{$fill}"/>
	<input id="mdcost" name="mdcost" type="hidden" value ="{round($cart_np.cost)}"/>
	<input id="total_wt" name="total_wt" type="hidden" value ="{$cartdetails.total_wt}"/>
	<input id="capital_top" name="capital_top" type="hidden" value ="{Configuration::get('ecm_np_capital_top')}"/> 
{if !isset($delivery_option) and !$ac}
	{literal}<script>
    	if ($("#ac").val()==1 || $("div").is("#opc_delivery_methods")) location.reload();
    	else window.location = window.location.href.toString().replace("?step=2", "") + "?step=2";
	</script>{/literal}
{else}
	{if $cart->id_carrier == $np_id}

	<h4>{l s='Details of Nova Poshta delivery' mod='ecm_novaposhta'}</h4>
<div class="delivery_option alternate_item">
	<table class="resume table table-bordered">
		<tbody><tr><td>
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

				{if $cart_np.another_recipient==''} {assign var=collapse_u value='style="display: none;" '}{else}{assign var=collapse_u value=''}{/if}
				<tr {$collapse_u} class="for_collapse_u">
					<td class="col-md-3 col-sm-2 col-xs-2"><label for="another_firstname">{l s='First name' mod='ecm_novaposhta'}</label></td>
					<td class="{if !$ac}col-md-4 col-lg-4 col-sm-4 col-xs-3{else}col-md-9{/if}" colspan=3>
						<input name="another_firstname" id="another_firstname"
						oninput="setCustomValidity('')"
						pattern="^[`'А-Яа-яЇїІіЄє\s]+$" 
						class="form-control np_ontyped another"
						title="{l s='First name' mod='ecm_novaposhta'}"
						value="{$cart_np.another_firstname}"
						{if $cart_np.another_recipient!=''}required{/if}>
						</input>
					</td>
					{if !$ac}<td class="col-md-5 col-lg-5 col-sm-4 col-xs-1"> </td>{/if}
				</tr>
				<tr {$collapse_u} class="for_collapse_u">
					<td class="col-md-3 col-sm-2 col-xs-2"><label for="another_lastname">{l s='Last name' mod='ecm_novaposhta'}</label></td>
					<td class="{if !$ac}col-md-4 col-lg-4 col-sm-4 col-xs-3{else}col-md-9{/if}" colspan=3>
						<input name="another_lastname" id="another_lastname"
						oninput="setCustomValidity('')"
						pattern="^[`'А-Яа-яЇїІіЄє\s]+$" 
						class="form-control np_ontyped another"
						title="{l s='Last name' mod='ecm_novaposhta'}"
						value="{$cart_np.another_lastname}"
						{if $cart_np.another_recipient!=''}required{/if}>
						</input>
					</td>
					{if !$ac}<td class="col-md-5 col-lg-5 col-sm-4 col-xs-1"> </td>{/if}
				</tr>
				{if Configuration::get('ecm_simcheck_middlename') or Configuration::get('ecm_checkout_middlename')}
				<tr {$collapse_u} class="for_collapse_u">
					<td class="col-md-3 col-sm-2 col-xs-2"><label for="another_middlename">{l s='Middle name' mod='ecm_novaposhta'}</label></td>
					<td class="{if !$ac}col-md-4 col-lg-4 col-sm-4 col-xs-3{else}col-md-9{/if}" colspan=3>
						<input name="another_middlename" id="another_middlename"
						oninput="setCustomValidity('')"
						pattern="^[`'А-Яа-яЇїІіЄє\s]+$" 
						class="form-control np_ontyped another"
						title="{l s='Middle name' mod='ecm_novaposhta'}"
						value="{$cart_np.another_middlename}"
						{if $cart_np.another_recipient!=''}required{/if}>
						</input>
					</td>
					{if !$ac}<td class="col-md-5 col-lg-5 col-sm-4 col-xs-1"> </td>{/if}
				</tr>
				{/if}
				<tr {$collapse_u} class="for_collapse_u">
					<td class="col-md-3 col-sm-2 col-xs-2"><label for="another_phone">{l s='Phone' mod='ecm_novaposhta'}</label></td>
					<td class="{if !$ac}col-md-4 col-lg-4 col-sm-4 col-xs-3{else}col-md-9{/if}" colspan=3>
						<input name="another_phone" id="another_phone"
						class="form-control np_ontyped another" type="tel"
						title="{l s='Phone' mod='ecm_novaposhta'}"
						value="{$cart_np.another_phone}"
						{if $cart_np.another_recipient!=''}required{/if}>
						</input>
					</td>
					{if !$ac}<td class="col-md-5 col-lg-5 col-sm-4 col-xs-1"> </td>{/if}
				</tr>
				{/if}
				<tr>
					<td class="col-md-3 col-sm-2 col-xs-2"><label for="id_area_delivery">{l s='Area' mod='ecm_novaposhta'}</label></td>
					<td class="{if !$ac}col-md-4 col-lg-4 col-sm-4 col-xs-3{else}col-md-9{/if}" colspan=3>
						<select name="id_area_delivery" id="id_area_delivery"
						class="form-control delivery" onchange="refreshcity()">
						{foreach from=$Areas item=area key=key}
							<option value="{$key}" area_id="{substr($area,0,3)}"  {if $key == $cart_np.area}selected="selected"{/if} >{substr($area,3)}</option>
						{/foreach}
						</select>
					</td>
					{if !$ac}<td class="col-md-5 col-lg-5 col-sm-4 col-xs-1"> </td>{/if}
				</tr>
				<tr>
					<td class="col-md-3 col-sm-2 col-xs-2"><label for="id_city_delivery">{l s='City' mod='ecm_novaposhta'}</label></td>
					<td class="{if !$ac}col-md-4 col-lg-4 col-sm-4 col-xs-3{else}col-md-9{/if}" colspan=3>
						<select name="id_city_delivery" id="id_city_delivery"
						class="form-control delivery" onchange="cost_by_city();refreshware()">
							{html_options options=$Citys selected=$cart_np.city}
						</select>
					</td>
					{if !$ac}<td class="col-md-5 col-lg-5 col-sm-4 col-xs-1"> </td>{/if}
				</tr>
				<tr>
					<td class="col-md-3 col-sm-2 col-xs-2"><label for="id_ware_delivery">{l s='Postoffice' mod='ecm_novaposhta'}</label></td>
					<td class="{if !$ac}col-md-4 col-lg-4 col-sm-4 col-xs-3{else}col-md-9{/if}" colspan=3>
						<select name="id_ware_delivery" id="id_ware_delivery"
						class="form-control delivery" onchange="saveform(0)">
							{html_options options=$Wares selected=$cart_np.ref}
						</select>
					</td>
					<td class="col-md-5 col-lg-5 col-sm-4 col-xs-1"> </td>
				</tr>
				
{if Configuration::get('ecm_np_address_delivery')}
<tr>
	<td colspan=4>
		<label for="np_address_delivery">
			<input id="np_address_delivery" name="np_address_delivery"  class="not_unifrom not_uniform" type="checkbox" {if $cart_np.ref=='1'}checked {/if} value="1"/>
			{$address_delivery_label}
		</label>
	</td>
</tr>
{/if}
				
				{if $cart_np.ref!='1'} {assign var=collapse value='style="display: none;" '}{else}{assign var=collapse value=''}{/if}
				<tr {$collapse} class="for_collapse">
					<td class="col-md-3 col-sm-2 col-xs-2"><label for="StreetName">{l s='Street' mod='ecm_novaposhta'}</label></td>
					<td class="{if !$ac}col-md-4 col-lg-4 col-sm-4 col-xs-3{else}col-md-9{/if}" colspan=3>
						<input name="StreetName" id="StreetName"
						pattern="^[\-`'0-9А-Яа-яЇїІіЄєЁё\s]+$" 
						class="form-control np_ontyped"
						value="{$cart_np.StreetName}">
						</input>
					</td>
					{if !$ac}<td class="col-md-5 col-lg-5 col-sm-4 col-xs-1"> </td>{/if}
				</tr>
				<tr {$collapse} class="for_collapse">
					<td class="col-md-3 col-sm-2 col-xs-2"><label for="BuildingNumber">{l s='Building' mod='ecm_novaposhta'}</label></td>
					<td class="">
						<input name="BuildingNumber" id="BuildingNumber"
						class="form-control np_ontyped"
						value="{$cart_np.BuildingNumber}">
						</input>
					</td>
					<td class="col-md-1 col-lg-1 col-sm-1 col-xs-1 text-right"><label for="Flat">{l s='Flat' mod='ecm_novaposhta'}</label></td>
					<td class="">
						<input name="Flat" id="Flat"
						class="form-control np_ontyped"
						value="{$cart_np.Flat}">
						</input>
					</td>
					{if !$ac}<td class="col-md-5 col-lg-5 col-sm-4 col-xs-1"> </td>{/if}
				</tr>
				{if $show}
				<tr><td></td>
					<td colspan=3>
						{if Configuration::get('ecm_simcheck_active') || isset($delivery_option)}
							{l s='Total weight' mod='ecm_novaposhta'} - <strong>{$cartdetails.weight}</strong> кг.<br>
							{l s='Volumeric weight' mod='ecm_novaposhta'} - <strong>{$cartdetails.vweight}</strong> кг.<br>
							{l s='Cost of order' mod='ecm_novaposhta'} - <strong>{number_format($cartdetails.total_wt,2)}</strong> {$currency->sign}<br>
							{if isset($cart_np.cost)}
								{l s='Shiping cost' mod='ecm_novaposhta'} - <strong><span id="md_cost">{number_format($cart_np.cost,2)}</span></strong> {$currency->sign}<br>
								{l s='Comiso of COD' mod='ecm_novaposhta'} - <strong><span id="md_costredelivery">{number_format($cart_np.costredelivery,2)}</span></strong> {$currency->sign}<br>
							{/if}
						{else}
							{l s='Please by patient!' mod='ecm_novaposhta'}
						{/if}
					</td>
				</tr>
				{/if}
			</table>
			</td></tr>
		</table>

	</div>

	<div id="refreshdelivery"></div>

	{$address_default = Configuration::get('ecm_np_address_delivery') and Configuration::get('ecm_np_address_delivery_def')}
	
	<script>
	var show = {$show|intval};
	var address_default = {$address_default|intval};
	</script>

	<style>
	
	.table-select tbody{
		border-top: none !important;
	}
	.table-select tr td{
		padding: 3px !important;
		border: none !important; 
	}
	</style>
	{/if}
{/if}
<!-- /ecm_novaposta shipping module -->
