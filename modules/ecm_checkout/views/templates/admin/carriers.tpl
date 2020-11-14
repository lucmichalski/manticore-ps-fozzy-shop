{**
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * We are experts and professionals in PrestaShop
 *
 * @author    Elcommerce <support@elcommece.com.ua>
 * @copyright 2010-2018 Elcommerce
 * @license   Comercial
 * @category  PrestaShop
 * @category  Module
*}


{$required = "'required'"}{$hidden = "'hidden'"}{$value = "'value'"} {$callme ="'callme'"}
{$info = "'info'"}{$replace = "'replace'"}{$ignore = "'ignore'"}{$sp = "'sp'"}{$cost ="'cost'"}
<div class="alert alert-warning">
<strong>{l s='Note: If your hide fields with * in following Prestashop settings, plese fill default value.' mod='ecm_checkout'}</strong>
<ul>
<li>{l s='Localization->Countries->Edit your country.' mod='ecm_checkout'}</li>
<li>{l s='Customers->Addresses->Set required fields for this section.' mod='ecm_checkout'}</li>
</ul>
</div>
<form id="carr_set" action="{$currentIndex}&token={$token}" method="post" enctype="multipart/form-data">
<fieldset class="space">
<div class="row">
	<ul class="nav nav-tabs tabs-left col-xs-2 pr-0 pt-0" style="padding-right: 0px;padding-top: 0px;margin-right: -3px;">
	    {$first = true}
	    {foreach $carriers as $id=>$carrier}
	    <li class="col-xs-12 {if $first}active{/if}" style="padding-right: 0px;">
	    <a data-toggle="pill" href="#menu{$carrier.id_carrier}">{$carrier.name} </a>
	    </li>
	    {$first = false}
		{/foreach}    
	</ul>
	<div class="panel tab-content col-xs-10">
	    {$first = true}
		{foreach $carriers as $id=>$carrier}
		<div id="menu{$carrier.id_carrier}" class="tab-pane fade {if $first}in active{/if}">
	    {$first = false}
		<div class="_box">
			{$idc = $carrier.id_reference}
			{$c = $cs.$idc}
			<div class="row">
			<div class="col-lg-6 col-md-6 col-sm-12">
			<div class="panel">
			<label>{l s='Address fields customize' mod='ecm_checkout'}</label>
			<table class="table">
			<thead>
			<tr>
				<th></th>
				<th>{l s='Req' mod='ecm_checkout'}</th>
				<th>{l s='Hidden' mod='ecm_checkout'}</th>
				<th>{l s='Default value' mod='ecm_checkout'}</th>
			</tr>
			</thead>
			{foreach $items as $item}
				<tr id="{$idc}" item={$item}><td>{trim($item,"'")}</td>
				<td><input class="req" type="checkbox" name="cs[{$idc}][{$item}]['required']" {if isset($c.$item.$required)}checked{/if} ></td>
				<td><input class="hid" type="checkbox" name="cs[{$idc}][{$item}]['hidden']" {if isset($c.$item.$hidden)}checked{/if} ></td>
				<td><input class="value" type="text" name="cs[{$idc}][{$item}]['value']" value="{$c.$item.$value}"
				{if isset($c.$item.$hidden) && isset($c.$item.$required)} required{/if}
				></td></tr>
			{/foreach}
			</table>
			</div>
			</div>
			
			<div class="col-lg-6 col-md-6 col-sm-12">
				<div class="row">
					<div class="panel">
						<div class="row">
						<label for="cs[{$idc}]['callme']">{l s='Call my checkbox' mod='ecm_checkout'}</label>
							<input class="" type="checkbox" name="cs[{$idc}]['callme']" id="cs[{$idc}]['callme']" {if isset($c.$callme)}checked{/if} >
						</div>
						<div class="row">
						<label for="cs[{$idc}][{$cost}]['replace']">{l s='Replace delivery cost' mod='ecm_checkout'}</label>
							<input class="" type="checkbox" name="cs[{$idc}][{$cost}]['replace']" id="cs[{$idc}][{$cost}]['replace']" {if isset($c.$cost.$replace)}checked{/if} 
							title="{l s='Replace cost delivery for this carrier' mod='ecm_checkout'}. {l s='Default (By carrier tariff)' mod='ecm_checkout'}"
							>
							<p>{l s='Replace cost delivery for this carrier' mod='ecm_checkout'}. {l s='Default (By carrier tariff)' mod='ecm_checkout'}</p>
						</div>
						<div class="row">
						<label for="cs[{$idc}][{$cost}]['ignore']">{l s='Ignore delivery cost' mod='ecm_checkout'}</label>
							<input class="" type="checkbox" name="cs[{$idc}][{$cost}]['ignore']" id="cs[{$idc}][{$cost}]['ignore']" {if isset($c.$cost.$ignore)}checked{/if} 
							title="{l s='Show totals witout delivery cost' mod='ecm_checkout'}"
							>
							<p>{l s='Show totals witout delivery cost' mod='ecm_checkout'}</p>
						</div>
					</div>
				</div>
				
			</div>

			
	
		</div>
		</div>
		</div>
		{/foreach}
	</div>
</div>
<div class="panel-footer">
	<button type="submit" name="submit_cs" class="btn btn-default pull-right">
	<i class="process-icon-save"></i> {l s='Save carrier settings' mod='ecm_checkout'}
	</button>
</div>
</fieldset>
</form>

{addJsDefL name='msg_must_complete'}{l s='Must complete!' mod='ecm_checkout' js=1}{/addJsDefL}

<script>
$('.req, .hid').on('change',function(){
	var req = $(this).parent().parent().find($(".req"));
	var hid = $(this).parent().parent().find($(".hid"));
	var value = $(this).parent().parent().find($(".value"));
	if (req.attr('checked') && hid.attr('checked')){
		value.prop('required',true);
		value.attr('required',true);
		if(!value[0].checkValidity()) {
			showErrorMessage(msg_must_complete);
			value.focus();
		}
	} else {
		value.prop('required',false);
		value.attr('required',false);
	}
})

</script>
