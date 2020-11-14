
<!-- NovaPoshta shipping module -->
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
<div class="pad">
	{if $show_another}
	<div class=" form-row">
	   <span class="custom-checkbox">
			<input type="checkbox" id="another_recipient" name="another_recipient" value="1" {$cart_np.another_recipient} onchange="another_update()"/>
			<span><i class="material-icons rtl-no-flip checkbox-checked">&#xE5CA;</i></span>
			<label for="another_recipient">{l s='Another recipient' mod='ecm_novaposhta'}</label>
		</span>
	</div>
	{if $cart_np.another_recipient !=''}{$required='required'}{else}{$required=''}{/if}
	
	<div class=" form-row another_recipient">
		<input id="another_firstname" class="form-control np_ontyped another"
		placeholder = "{l s='First name' mod='ecm_novaposhta'}"
		title = "{l s='First name' mod='ecm_novaposhta'} {l s='for another recipient' mod='ecm_novaposhta'}"
		{$required}
		value="{$cart_np.another_firstname}">
		</input>
	</div>
	
	<div class=" form-row another_recipient">
		<input id="another_lastname" class="form-control np_ontyped another"
		placeholder = "{l s='Last name' mod='ecm_novaposhta'}"
		title = "{l s='Last name' mod='ecm_novaposhta'} {l s='for another recipient' mod='ecm_novaposhta'}"
		{$required}
		value="{$cart_np.another_lastname}">
		</input>
	</div>
	{if Configuration::get('ecm_simcheck_middlename') or Configuration::get('ecm_checkout_middlename')}
	<div class=" form-row another_recipient">
		<input id="another_middlename" class="form-control np_ontyped another"
		placeholder = "{l s='Middle name' mod='ecm_novaposhta'}"
		title = "{l s='Middle name' mod='ecm_novaposhta'} {l s='for another recipient' mod='ecm_novaposhta'}"
		{*pattern="^[\-`'А-Яа-яЇїІіЄєЁё\s]+$"*} {$required}
		value="{$cart_np.another_middlename}">
		</input>
	</div>
	{/if}
	<div class=" form-row another_recipient">
		<input id="another_phone" class="form-control np_ontyped another"
		placeholder = "{l s='Phone' mod='ecm_novaposhta'}" 
		title = "{l s='Phone' mod='ecm_novaposhta'} {l s='for another recipient' mod='ecm_novaposhta'}"
		{*pattern="\+?[38]?8?0\d\d ?-?\d\d\d ?-?\d\d ?-?\d\d"*} {$required}
		value="{$cart_np.another_phone}" title="Формат вводу 050-123-45-67">
		</input>
	</div>
	{/if}
	
	
	
	<div class=" form-row">
		<select id="id_area_delivery" class="form-control form-control-select delivery" onchange="refreshcity()">
			{foreach from=$Areas item=area key=key}
				<option value="{$key}" area_id="{substr($area,0,3)}"  {if $key == $cart_np.area}selected="selected"{/if} >{substr($area,3)}</option>
			{/foreach}
		</select>
	</div>

	<div class=" form-row">
		<select id="id_city_delivery" class="form-control form-control-select delivery" onchange="cost_by_city();refreshware();">
			{html_options options=$Citys selected=$cart_np.city}
		</select>
	</div>

	<div class=" form-row">
		<select id="id_ware_delivery" class="form-control form-control-select delivery" onchange="saveform(0)">
			{html_options options=$Wares selected=$cart_np.ref}
		</select>
	</div>

{if Configuration::get('ecm_np_address_delivery')}
	<div class=" form-row">
	   <span class="custom-checkbox">
			<input id="np_address_delivery" name="np_address_delivery" 
			type="checkbox" {if $cart_np.ref == '1'}checked{/if} value="1"/>
			<span><i class="material-icons rtl-no-flip checkbox-checked">&#xE5CA;</i></span>
			<label for="np_address_delivery">{$address_delivery_label}</label>
		</span>
	</div>

	<div class=" form-row np_address_delivery">
		<input id="StreetName" class="form-control np_ontyped"
		placeholder = "{l s='Street' mod='ecm_novaposhta'}"
		title = "{l s='Street' mod='ecm_novaposhta'}"
		pattern="^[`'0-9А-Яа-яЇїІіЄєЁё\-\s]+$" 
		value="{$cart_np.StreetName}">
		</input>
	</div>

	<div class=" form-row np_address_delivery">
		<input id="BuildingNumber" class="form-control np_ontyped"
		placeholder = "{l s='Building' mod='ecm_novaposhta'}"
		title = "{l s='Building' mod='ecm_novaposhta'}"
		value="{$cart_np.BuildingNumber}">
		</input>
	</div>

	<div class=" form-row np_address_delivery">
		<input id="Flat" class="form-control np_ontyped"
		placeholder = "{l s='Flat' mod='ecm_novaposhta'}"
		title = "{l s='Flat' mod='ecm_novaposhta'}"
		value="{$cart_np.Flat}">
		</input>
	</div>
{/if}

	{if $show}
	<hr>
	<div class=" form-row">
		<label class="col-md-3 form-control-label" for="Flat">{l s='Details of Nova Poshta delivery' mod='ecm_novaposhta'}</label>
			{l s='Total weight' mod='ecm_novaposhta'} - <strong>{$cartdetails.weight}</strong> кг.
			{l s='Volumeric weight' mod='ecm_novaposhta'} - <strong>{$cartdetails.vweight}</strong> кг.<br>
			{l s='Cost of order' mod='ecm_novaposhta'} - <strong>{Tools::displayPrice($cartdetails.total_wt)}</strong><br>
			{if isset($cart_np.cost)}
				{l s='Shiping cost' mod='ecm_novaposhta'} - <strong><span id="md_cost">{Tools::displayPrice($cart_np.cost)}</span></strong><br>
				{l s='Comiso of COD' mod='ecm_novaposhta'} - <strong><span id="md_costredelivery">{Tools::displayPrice($cart_np.costredelivery)}</span></strong><br>
			{/if}

	</div>
	{/if}
</div>
<hr>

<style>
	#id_ware_delivery option[value='1']{
		font-size:110%;
		font-style: italic;
		font-weight: bold;
		background-color: #f6f6f6;
	}
</style>

	{$address_default = Configuration::get('ecm_np_address_delivery') and Configuration::get('ecm_np_address_delivery_def')}
	
	<script>
	var show = {$show|intval};
	var address_default = {$address_default|intval};
	</script>

<!-- /NovaPoshta shipping module -->
