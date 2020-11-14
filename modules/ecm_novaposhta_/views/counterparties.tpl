<div class="panel">
<div class="panel-heading"><i class="icon-user"></i> {l s='Counterparties' mod='ecm_novaposhta'}</div>
	{if $by_order != ''} 
		<p class="alert alert-warning">
		{l s='Edit counterparty reserved for legal persons' mod='ecm_novaposhta'}
		</p>
	{else}
		
	{/if}
<table class="table configuration" id="counterparties"><thead><tr>
<th>{l s='Description' mod='ecm_novaposhta'}</th>
<th>{l s='OwnershipForm' mod='ecm_novaposhta'}</th>
<th>{l s='EDRPOU' mod='ecm_novaposhta'}</th>
<th>{l s='City' mod='ecm_novaposhta'}</th>
<th colspan="2"/></tr></thead><tbody>
	{if count($counterparties)}
		{foreach from=$counterparties item='counterpartie'}
			{if $counterparty == $counterpartie->Ref}
				{assign var="checked" value="checked='checked'" nocache}
			{else}
				{assign var="checked" value="" nocache}
			{/if}
			<tr id="row_{$counterpartie->Ref}">
			<td><input type="text" {$by_order} id="Description_{$counterpartie->Description}" value="{$counterpartie->Description}"/></td>
			<td><input type="text" {$by_order} id="OwnershipFormDescription_{$counterpartie->OwnershipFormDescription}" value="{$counterpartie->OwnershipFormDescription}"/></td>
			<td><input type="text" {$by_order} id="EDRPOU_{$counterpartie->EDRPOU}" value="{$counterpartie->EDRPOU}"/></td>
			<td><input type="text" {$by_order} id="CityDescription_{$counterpartie->CityDescription}" value="{$counterpartie->CityDescription}"/></td>
			<td><input type="radio" id="use_{$counterpartie->Ref}" onclick ="use_c('{$counterpartie->Ref}')" id ="contact_{$counterpartie->Ref}" name="counterpartie" {$checked} title="{l s='Check for use this contact person' mod='ecm_novaposhta'}" /></td>
			<td><button type="button" onclick ="update_c('{$counterpartie->Ref}')" title="{l s='Update' mod='ecm_novaposhta'}" class="btn btn-default"><i class="icon-save"></i></button></td>
{*			<td><button type="button" onclick ="delete_c('{$counterpartie->Ref}')" title="{l s='Delete' mod='ecm_novaposhta'}" class="btn btn-default"><i class="icon-trash"></i></button></td> *}
			</tr>
		{/foreach}
	{/if}
</tbody>
{if $by_order == ''}	
<thead>
<td><input type="text" id ="Description_c"/></td>
<td><select id="OwnershipForm_c" class="address_select form-control">{html_options options=$OwnershipFormsList selected=$Ownership}</select></td>
<td><input type="text" id ="EDRPOU_c"/></td>
<td><input type="text" id ="CityDescription_c"/></td>
<td colspan="2"><button type="button" onclick ="add_c()" title="{l s='Add new' mod='ecm_novaposhta'}" class="btn btn-default"><i class="icon-save"></i></button></td>
</tr></thead>
{/if}
</table>
</div>
