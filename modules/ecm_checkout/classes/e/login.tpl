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



<div class="row  to_login hidden">
	<div class="col-xs-12 col-sm-12">
		<span class="btn pull-right btn-default" title="{l s='Close window' mod='ecm_simcheck'}" onclick = "to_login()">
		<i class="icon-cross "></i>
		</span>    
		<div class="box center">
		    <div class="form-group">
		        <label for="login">
		        {if $authMethod == 2}
		            {l s='Email or phone' mod='ecm_simcheck'}
		        {elseif $authMethod == 1}
		            {l s='Phone' mod='ecm_simcheck'}
		        {else}
		            {l s='Email' mod='ecm_simcheck'}
		        {/if}
		        </label>
		        <input class="is_required validate account_input form-control" data-validate="isEmail" id="login" name="login" value="">
		    </div>
		    <div class="form-group">
		        <label for="passwd">
		            {l s='Password' mod='ecm_simcheck'}
		        </label>
		        <input class="is_required validate account_input form-control" data-validate="isPasswd" id="passwd" name="passwd" value="" type="password">
		    </div>
		    <p class="submit">
		        <button class="btn btn-default button-medium" onclick="login()">
		            <span>
		                {l s='Sign in' mod='ecm_simcheck'}
		                <i class="icon-chevron-right right"></i>
		            </span>
		        </button>
		    </p>
		    <p class="lost_password form-group">
		        <a href="password-recovery" title="{l s='Recover your forgotten password' mod='ecm_simcheck'}" rel="nofollow">
		            {l s='Forgot the password?' mod='ecm_simcheck'}
		        </a>
		    </p>
		</div>
	</div>
</div>
<style>

</style>