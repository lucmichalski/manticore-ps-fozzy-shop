
<input id="ac" name="ac" type="hidden" value ="{$ac}"/>

<input id="module_dir" name="module_dir" type="hidden" value ="{$module_dir}"/>
<input id="id_lang" name="id_lang" type="hidden" value ="{$language.id}"/>
<input id="customer" name="customer" type="hidden" value ="{$customer.id}"/>
<input id="md_page" name="page" type="hidden" value ="cart"/>
<input id="employee" name="employee" type="hidden" value ="{$employee}"/>
<input id="cart_id" name="cart_id" type="hidden" value ="{$cart_id}"/>
<input id="np" name="np" type="hidden" value ="{$np_id}"/>
<input id="fixcost" name="fixcost" type="hidden" value ="{$fixcost}"/>
<input id="fill" name="fill" type="hidden" value ="{$fill}"/>
<input id="mdcost" name="mdcost" type="hidden" value ="{round($cart_np.cost)}"/>
<input id="total_wt" name="total_wt" type="hidden" value ="{$cartdetails.total_wt}"/>
<h4>{l s='Details of Nova Poshta delivery' mod='ecm_novaposhta'}</h4>
<div class="delivery_option alternate_item">
	<table class="resume table table-bordered">
		<tr><td>
			<table class="table-select">
			<tr>
				<td><label for="id_area_delivery">{l s='Area' mod='ecm_novaposhta'}</label></td>
				<td colspan=3>
					<select name="id_area_delivery" id="id_area_delivery" 
					class="form-control form-control-select" onchange="refreshcity()">
						{html_options options=$Areas selected=$cart_np.area}
					</select>
				</td>
			</tr>
			<tr>
				<td><label for="id_city_delivery">{l s='City' mod='ecm_novaposhta'}</label></td>
				<td colspan=3>
					<select name="id_city_delivery" id="id_city_delivery" 
					class="form-control form-control-select" onchange="cost_by_city();refreshware()">
						{html_options options=$Citys selected=$cart_np.city}
					</select>
				</td>
			</tr>
			<tr>
				<td><label for="id_ware_delivery">{l s='Postoffice' mod='ecm_novaposhta'}</label></td>
				<td colspan=3>
					<select name="id_ware_delivery" id="id_ware_delivery" 
					class="form-control form-control-select" onchange="saveform(0)">
						{html_options options=$Wares selected=$cart_np.ref}
					</select>
				</td>
			</tr>
			{if $cart_np.ref != 1} {assign var=collapse value='style="display: none;" '}{else}{assign var=collapse value=''}{/if}
			<tr {$collapse} class="for_collapse">
				<td><label for="StreetName">{l s='Street' mod='ecm_novaposhta'}</label></td>
				<td colspan=3>
					<input name="StreetName" id="StreetName" 
					class="form-control ontyped"
					value="{$cart_np.StreetName}">
					</input>
				</td>
			</tr>
			<tr {$collapse} class="for_collapse">
				<td><label for="BuildingNumber">{l s='Building' mod='ecm_novaposhta'}</label></td>
				<td>
					<input name="BuildingNumber" id="BuildingNumber" 
					class="form-control ontyped"
					value="{$cart_np.BuildingNumber}">
					</input>
				</td>
				<td><label for="Flat">{l s='Flat' mod='ecm_novaposhta'}</label></td>
				<td>
					<input name="Flat" id="Flat" 
					class="form-control ontyped"
					value="{$cart_np.Flat}">
					</input>
				</td>
			</tr>
			{if $show}
			<tr><td></td>
				<td colspan=3>
					{l s='Total weight' mod='ecm_novaposhta'} - <strong>{$cartdetails.weight}</strong> кг. 
					{l s='Volumeric weight' mod='ecm_novaposhta'} - <strong>{$cartdetails.vweight}</strong> кг.<br>
					{l s='Cost of order' mod='ecm_novaposhta'} - <strong>{$cartdetails.total_wt}</strong> {$currency.sign}<br>
					{if isset($cart_np.cost)} 
						{l s='Shiping cost' mod='ecm_novaposhta'} - <strong><span id="md_cost">{round($cart_np.cost,2)}</span></strong> {$currency.sign}<br>
						{l s='Comiso of COD' mod='ecm_novaposhta'} - <strong><span id="md_costredelivery">{round($cart_np.costredelivery,2)}</span></strong> {$currency.sign}<br>
					{/if}
				</td>
			</tr>
			{/if}
		</table>
		</td></tr>
	</table>

</div>
	
<div id="refreshdelivery"></div>
<script> 
	$('.for_hide').hide() 
</script>

<style>
.table-bordered{
	width:100% !important;
}


.table-select tbody{
	border-top: none !important;
}
.table-select tr td{
	padding: 3px !important;
	border: none !important; 
}
</style>

{*

*}