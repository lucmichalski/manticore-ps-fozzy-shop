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

<div class="card-block"><h5 class="h5"><i class="material-icons mi-payment">payment</i> {l s='Payment' mod='ecm_checkout'}</h5></div>
<hr class="separator">

<section class="card-block">
{block name='step_content'}

  {hook h='displayPaymentTop'}

  {if $is_free}
    <p>{l s='No payment needed for this order' d='Shop.Theme.Checkout'}</p>
  {/if}
  <div class="payment-options {if $is_free}hidden-xs-up{/if}">
    {foreach from=$payment_options item="module_options"}
      {foreach from=$module_options item="option"}
        {if $option.id === $selected_payment_option}
            <input hidden id="action" name="action" value="{$option.action}"/>
        {/if}
        <div>
          <div id="{$option.id}-container" class="payment-option clearfix">
            <span class="custom-radio float-xs-left">
              <input
                class="ps-shown-by-js {if $option.binary} binary {/if} select_payment_option"
                id="{$option.id}" data-module-name="{$option.module_name}"
                name="payment-option" type="radio" required
                {if $selected_payment_option == $option.id || $is_free} checked {/if}
                title="{$option.call_to_action_text}"
              >
              <span></span>
            </span>

			
        
            <label for="{$option.id}">
              <span>{$option.call_to_action_text}</span>
              {if $option.logo}
                <img src="{$option.logo}">
              {/if}
            </label>

          </div>
        </div>
		
		{if  $option.id === $selected_payment_option && $option.sc_description}
          <div id="{$option.id}-additional-information" class="js-additional-information definition-list additional-information">
            {$option.sc_description nofilter}
          </div>
        {/if}

		{if !Configuration::get('ecm_checkout_simple_name_pay') && $option.id === $selected_payment_option && $option.additionalInformation}
          <div id="{$option.id}-additional-information" class="js-additional-information definition-list additional-information">
            {$option.additionalInformation nofilter}
          </div>
        {/if}

        {if $option.id === $selected_payment_option}
			<div id="pay-with-{$option.id}-form" class="js-payment-option-form">
			{if  $option.form}
				<div class="selected_payment_option">{$option.form nofilter}</div>
			{else}
				<form id="payment-form" method="POST" action="{$option.action nofilter}">
				  {if isset($option.inputs)}
					  {foreach from=$option.inputs item=input}
						<input type="{$input.type}" name="{$input.name}" value="{$input.value}">
					  {/foreach}
				  {/if}
				  <button style="display:none" id="pay-with-{$option.id}" type="submit"></button>
				</form>
			{/if}
			</div>
		{/if}
      {/foreach}
    {foreachelse}
      <p class="alert alert-danger">{l s='Unfortunately, there are no payment method available.' d='Shop.Theme.Checkout'}</p>
    {/foreach}
  </div>


  {hook h='displayPaymentByBinaries'}



{/block}


</section>


<script>

$(".sc_payment_row").on("click", function (){
	$(".payment_sc_radio").removeAttr('checked');
	$(".sc_payment_row").removeClass('sc_payment_row_checked');
	$(this).addClass('sc_payment_row_checked');
	action('set_payment,'+renderSeq, $(this).attr('value'));
});

$(".select_payment_option").on("change", function (){
    action('set_payment,payment',$(this).attr('id'));
});

if ($('.selected_payment_option').length != 0){
	$('#action').val($('.selected_payment_option').children('form').attr('action'));
}

</script>

