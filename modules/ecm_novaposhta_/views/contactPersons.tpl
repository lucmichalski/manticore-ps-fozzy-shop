<div class="panel">
<div class="panel-heading"><i class="icon-user"></i> {l s='Contact persons' mod='ecm_novaposhta'}</div>
	{if $by_order != ''} <p class="alert alert-warning">
		{l s='Edit counterparty reserved for legal persons' mod='ecm_novaposhta'} 
{*		<button type="button" onclick ="clear_clone('{$counterparty}')" 
		title="{l s='Clear contacts and create loyality contact' mod='ecm_novaposhta'}" class="close">
		<i class="icon-trash"></i> & <i class="icon-save"></i>
		</button> *}
		</p>
	{/if} 
	<table class="table configuration" id="contacts"><thead><tr>
	<th>{l s='Last name' mod='ecm_novaposhta'}</th>
	<th>{l s='First name' mod='ecm_novaposhta'}</th>
	<th>{l s='Middle name' mod='ecm_novaposhta'}</th>
	<th>{l s='Phone' mod='ecm_novaposhta'}</th>
	<th>{l s='Email' mod='ecm_novaposhta'}</th>
	<th colspan="2"/></tr></thead>
	<tbody>
	{if count($contacts) > 0 }
		{foreach from=$contacts item='contact'}
			{if $active == $contact->Ref}
				{assign var="checked" value="checked='checked'" nocache}
			{else}
				{assign var="checked" value="" nocache}
			{/if}
			<tr id="row_{$contact->Ref}">
			<td><input type="text" {$by_order} id="LastName_{$contact->Ref}" value="{$contact->LastName}"/></td>
			<td><input type="text" {$by_order} id="FirstName_{$contact->Ref}" value="{$contact->FirstName}"/></td>
			<td><input type="text" {$by_order} id="MiddleName_{$contact->Ref}" value="{$contact->MiddleName}"/></td>
			<td><input type="text" {$by_order} id="Phones_{$contact->Ref}" value="{$contact->Phones}"/></td>
			<td><input type="email" {$by_order} id="Email_{$contact->Ref}" value="{$contact->Email}"/></td>
			<td><input type="radio" id="use_{$contact->Ref}" onclick ="use_cp('{$contact->Ref}')" id ="contact_{$contact->Ref}" name="contact" {$checked} title="{l s='Check for use this contact person' mod='ecm_novaposhta'}" /></td>
			<td><button type="button" {$by_order} onclick ="update_cp('{$contact->Ref}')" title="{l s='Update' mod='ecm_novaposhta'}" class="btn btn-default"><i class="icon-save"></i></button></td>
{*			<td><button type="button" onclick ="delete_cp('{$contact->Ref}')" title="{l s='Delete' mod='ecm_novaposhta'}" class="btn btn-default"><i class="icon-trash"></i></button></td> *}
			</tr>
		{/foreach}
	{/if}
	</tbody>
	<thead>
	<td><input type="text" id ="LastName_cp" title="{l s='Last Name for new contact person' mod='ecm_novaposhta'}" /></td>
	<td><input type="text" id ="FirstName_cp" title="{l s='First Name for new contact person' mod='ecm_novaposhta'}"/></td>
	<td><input type="text" id ="MiddleName_cp" title="{l s='Middle Name for new contact person' mod='ecm_novaposhta'}"/></td>
	<td><input type="text" id ="Phones_cp" title="{l s='Phone for new contact person' mod='ecm_novaposhta'}"/></td>
	<td><input type="email" id ="Email_cp" title="{l s='Email for new contact person' mod='ecm_novaposhta'}"/></td>
	<td colspan="3"><button type="button" onclick ="add_cp()" title="{l s='Add new' mod='ecm_novaposhta'}" class="btn btn-default"><i class="icon-save"></i></button></td>
	</tr></thead>
	</table>
<input id="counterparty" type="hidden" value ="{$counterparty}"/>
</div>
