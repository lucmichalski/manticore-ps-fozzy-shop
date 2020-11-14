{if $authMethod > 0}


<button class="btn btn-primary hidden-xs-down" id="btn-sms" name="btn-sms" 
	title="{l s='Recovery forgot password by sms' mod='ecm_checkout'}"
	onclick="simcheck_sms()">
	<span>{l s='SMS recovery' mod='ecm_checkout'}<i class="icon-chevron-right right"></i></span>
</button>


<script>
	var ajaxSms="{$ajaxSms|escape:'html'}";
	var smsRecovery = "{l s='Recovery forgot password by sms' mod='ecm_checkout' js=1}";
	var sms = "{l s='SMS recovery' mod='ecm_checkout' js=1}";
	var phone = "{l s='Phone mobile' mod='ecm_checkout' js=1}";
	var phone_email = "{l s='Phone mobile or Email' mod='ecm_checkout' js=1}";
	var page = "{$page.page_name}";

</script>

{/if}