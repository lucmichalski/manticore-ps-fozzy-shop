{if $useMiddlename == 1}
<div id= "middlename-group" class="form-group row">
    <label class="col-md-3 form-control-label required" for="middlename" >
              {l s='Middle name' mod='ecm_checkout'}
          </label>
    <div class="col-md-6">
        <input class="{*is_required*} validate form-control" data-validate="isName" type="text" name="middlename" id="middlename" value="{$middlename}" autocomplete="on"/>
    </div>
    <div class="col-md-3 form-control-comment"></div>
</div>


{/if}
{if $authMethod > 0}
<div id= "phone-group" class="form-group row">
    <label for="phone" class="col-md-3 form-control-label {if $authMethod == 1}required{/if}">
        {l s='Phone' mod='ecm_checkout'}
    </label>
    <div class="col-md-6">
		<input class="{if $authMethod == 1}is_required{elseif $authMethod == 2}mixed{/if} validate form-control " data-validate="isPhoneNumber" type="tel" name="phone" id="phone" value="{$phone}" autocomplete="on"/>
    </div>
    <div class="col-md-3 form-control-comment"></div>
</div>
{/if}

