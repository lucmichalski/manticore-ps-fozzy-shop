{if $useMiddlename == 1}
<div id="middlename-group" class="form-group" style="display:none">
    <label class="control-label col-lg-3">
        <span class="label-tooltip" data-toggle="tooltip" data-html="true"  data-original-title="{l s='Invalid characters 0-9!<>,;?=+()@#' mod='ecm_checkout'}">{l s='Middle name' mod='ecm_checkout'}</span>
    </label>
    <div class="col-lg-4">
	    <input type="text" name="middlename" id="middlename" value="{$middlename}" autocomplete="on">
    </div>
</div>
{/if}
{if $authMethod > 0}
<div id= "phone-group" class="form-group" style="display:none">
	<label class="control-label col-lg-3">
        <span class="label-tooltip" data-toggle="tooltip" data-html="true"  data-original-title="{l s='Phone number as login identifier' mod='ecm_checkout'}">{l s='Phone' mod='ecm_checkout'}</span>
	</label>
    <div class="col-lg-4">
		<div class="input-group">
			<span class="input-group-addon"><i class="icon-phone"></i></span>
			<input type="text" name="phone" id="phone" value="{$phone}" autocomplete="on">
		</div>
	</div>
</div>
{/if}
{addJsDef ajaxUrl={$ajaxUrl|escape:'html'}}
{addJsDef id_customer={$id_customer}}
{addJsDef authMethod={$authMethod}}
{addJsDefL name='formatError'}{l s='Format phone number error!' mod='ecm_checkout' js=1}{/addJsDefL}
{addJsDefL name='hasPhone'}{l s='A user with such a phone already exists in the site database. Change is impossible!' mod='ecm_checkout' js=1}{/addJsDefL}
{addJsDefL name='success'}{l s='Update success!' mod='ecm_checkout' js=1}{/addJsDefL}
