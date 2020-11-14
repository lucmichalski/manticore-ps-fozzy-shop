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

<div class="card-block"><h5 class="h5"><i class="fa fa-truck"></i> {l s='Shipping' mod='ecm_checkout'}</h5></div>
<hr class="separator">

<section class="card-block">
  <div id="hook-display-before-carrier">
    {$hookDisplayBeforeCarrier nofilter}
  </div>
  <div class="form-row">
    {if $delivery_options|count}
          {block name='delivery_options'}
              {foreach from=$delivery_options item=carrier key=carrier_id}
			      {if $carrier.sc_name}{$carrier.name = $carrier.sc_name}{/if}
                {if Configuration::get('ecm_checkout_show_price')} {$width = 10} {else} {$width = 11} {/if}
                <hr class="separator">
                <div class="row sc_carrier_row {if $delivery_option == $carrier_id} sc_carrier_row_checked{/if}" 
                title = "{$carrier.delay}" value="{$carrier_id},">
	   			{if Configuration::get('ecm_checkout_show_radio')} {$width = $width-1}
					<div class="col-1">
                      <span class="custom-radio">
                        <input type="radio" name="delivery_option[{$id_address}]"
                        id="delivery_option_{$carrier.id}" value="{$carrier_id}"
                        {if $delivery_option == $carrier_id} checked{/if}  required title="{$carrier.name}"/>
                        <span></span>
                      </span>
                    </div>
                {/if}
                {if Configuration::get('ecm_checkout_show_logo')} {$width = $width-2}
                    <div class="col-2">
                    {if $carrier.logo}
	                        <img src="{$carrier.logo}" alt="{$carrier.name}" />
                    {else}&nbsp;{/if}
                    </div>
                {/if}
                    <div class="col-{$width}">
                        <span class="carrier-name h5">{$carrier.name}</span>
                        {if !Configuration::get('ecm_checkout_simple_name')}
                            </br>
                            <span class="carrier-delay">{$carrier.delay}</span>
							{if $carrier.is_best_grade}
								{if $carrier.is_best_price}
									<span class="carrier-delay">{l s='The best price and speed' mod='ecm_checkout'}</span>
								{else}
									<span class="carrier-delay">{l s='The fastest' mod='ecm_checkout'}</span>
								{/if}
							{elseif $carrier.is_best_price}
								<span class="carrier-delay">{l s='The best price' mod='ecm_checkout'}</span>
							{/if}
						{/if}
                   </div>

                 {if Configuration::get('ecm_checkout_show_price')}
                    <div class="col-1 product-line-grid-right">
                        <span class="value carrier-price">{$carrier.price}</span>
                    </div>
                 {/if}
                 </div>
                  {if $carrier.info and $delivery_option == $carrier_id}
                  <div class="carrier-extra-content"{if $delivery_option != $carrier_id} style="display:none;"{/if}>
                    <hr class="separator">
						{$carrier.info nofilter}
                  </div>
                  {/if}
                 {if $carrier.extraContent and $delivery_option == $carrier_id}
                  <div class="carrier-extra-content"{if $delivery_option != $carrier_id} style="display:none;"{/if}>
                    <hr class="separator">
						{$carrier.extraContent nofilter}
                  </div>
                  {/if}
                  <div class="clearfix"></div>
              {/foreach}
          {/block}

    {else}
      <p class="alert alert-danger">{l s='Unfortunately, there are no carriers available for your delivery address.' d='Shop.Theme.Checkout'}</p>
    {/if}
  </div>

  <div id="hook-display-after-carrier">
    {$hookDisplayAfterCarrier nofilter}
  </div>

  <div id="extra_carrier"></div>

</section>




<script>
//(".delivery_sc_radio").on("click", function (){
//	action('set_carrier,carrier,cart', $(".delivery_sc_radio:checked").val());
//});
$(".sc_carrier_row").on("click", function (){
	$(".delivery_sc_radio").removeAttr('checked');
	$(".sc_carrier_row").removeClass('sc_carrier_row_checked');
	$(this).addClass('sc_carrier_row_checked');
	action('set_carrier', $(this).attr('value'));
});

var cart_qties = {$cart_qties|intval};
var id_cart = {$id_cart|intval};



</script>

	
