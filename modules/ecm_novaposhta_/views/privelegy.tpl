
<input id="countwh" name="countwh" type="hidden" value ="{$countwh}"/>
<input id="customer" name="customer" type="hidden" value ="-{$employee}"/>
<input id="md_page" name="page" type="hidden" value ="settings"/>
<input id="cart_id" name="cart_id" type="hidden" value ="0"/>

<fieldset>
<div class="panel">
	<div class="panel-heading">
		<i class="icon-gear"></i> {l s='Privileged groups and warehouses' mod='ecm_novaposhta'}
	</div>
		<table><tr>
		<td width="48%">
		<div class="panel">
			<div class="panel-heading">
				<i class="icon-globe"></i> {l s='Groups' mod='ecm_novaposhta'}
			</div>
			<div class="margin-form">
					<select multiple size="{count($groups)}" name="privileged_group[]" id="privileged_group" class="address_select form-control">
					{html_options options=$groups selected=$privileged_group}
					</select>
					<p class="clear">{l s='Select (use Ctrl) groups that are is privileged' mod='ecm_novaposhta'}</p>
			</div>
		
		
		</div>

	</td>
	<td width="4%">&nbsp;</td>
	<td width="48%">
		<div class="panel">
			<div class="panel-heading">
				<i class="icon-cogs"></i> {l s='Warehouses' mod='ecm_novaposhta'}
			</div>
			<div class="margin-form">
					<select multiple size="{count($WarehouseTypes)}" name="privileged_ware[]" id="privileged_ware" class="address_select form-control">
					{html_options options=$WarehouseTypes selected=$privileged_ware}
					</select>
					<p class="clear">{l s='Select (use Ctrl) warehouse types that are is privileged' mod='ecm_novaposhta'}</p>
			</div>
			


	</div>

	</td>

	</tr></table>
	<center><hr><input class="button" type="submit" name="submitUPDATE" value="{l s='Save' mod='ecm_novaposhta'}" /></center>
</div>
</fieldset>
