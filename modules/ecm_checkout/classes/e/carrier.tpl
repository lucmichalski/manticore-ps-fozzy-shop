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



<label class="sc-label"><i class="icon icon-truck"></i> {l s='Shipping' mod='ecm_simcheck'}</label>
{if isset($carriers) && $id_carrier && isset($HOOK_BEFORECARRIER)}
<div id="HOOK_BEFORECARRIER">
		{$HOOK_BEFORECARRIER}
</div>
{/if}
<div class="table_block table-responsive">
{if isset($delivery_option_list) && $id_carrier}
	{foreach $delivery_option_list as $id_address => $option_list}
		<table class="table _table-bordered">
		{foreach $option_list as $key => $option}
			<tr class="sc_carrier_row has-spinner
			{if isset($delivery_option[$id_address]) && $delivery_option[$id_address] == $key} 
				sc_carrier_row_checked
			{/if}" value="{$key}" 
			title="{foreach $option.carrier_list as $carrier}{/foreach}{if $option.unique_carrier}{if isset($carrier.instance->delay[$cookie->id_lang])}{l s='Delivery time:' mod='ecm_simcheck'}&nbsp;{$carrier.instance->delay[$cookie->id_lang]|escape:'htmlall':'UTF-8'}{/if}{/if}

{if count($option_list) > 1}{if $option.is_best_grade}{if $option.is_best_price}{l s='The best price and speed' mod='ecm_simcheck'}{else}{l s='The fastest' mod='ecm_simcheck'}{/if}{elseif $option.is_best_price}{l s='The best price' mod='ecm_simcheck'}{/if}{/if}">
				{if Configuration::get('ecm_simcheck_show_radio')}
				<td width="16px">
					<input id="delivery_option_{$id_address|intval}_{$option@index}" 
					class="delivery_sc_radio" type="radio" name="delivery_option[{$id_address|intval}]" 
					data-key="{$key}" data-id_address="{$id_address|intval}" 
					value="{$key}"
					{if isset($delivery_option[$id_address]) && $delivery_option[$id_address] == $key} 
						checked="checked"
					{/if} />
				</td>
				{/if}
				{if Configuration::get('ecm_simcheck_show_logo')}
				<td class="_delivery_option_logo" style="text-align: center;">
					{foreach $option.carrier_list as $carrier}
						{if $carrier.logo}
							<img class="order_carrier_logo" src="{$carrier.logo|escape:'htmlall':'UTF-8'}" alt="{$carrier.instance->name|escape:'htmlall':'UTF-8'}"/>
						{elseif !$option.unique_carrier}
							{$carrier.instance->name|escape:'htmlall':'UTF-8'}
							{if !$carrier@last} - {/if}
						{/if}
					{/foreach}				
				</td>
				{/if}
				
				<td>
					{if $option.unique_carrier}
						{foreach $option.carrier_list as $carrier}
							{$carrier.instance->name|escape:'htmlall':'UTF-8'}
						{/foreach}
						{if isset($carrier.instance->delay[$cookie->id_lang]) and !Configuration::get('ecm_simcheck_simple_name')}
							<br />{l s='Delivery time:' mod='ecm_simcheck'}&nbsp;{$carrier.instance->delay[$cookie->id_lang]|escape:'htmlall':'UTF-8'}
						{/if}
					{/if}
					{if count($option_list) > 1 and !Configuration::get('ecm_simcheck_simple_name')}
					<br />
						{if $option.is_best_grade}
							{if $option.is_best_price}
								<span class="best_grade best_grade_price best_grade_speed">{l s='The best price and speed' mod='ecm_simcheck'}</span>
							{else}
								<span class="best_grade best_grade_speed">{l s='The fastest' mod='ecm_simcheck'}</span>
							{/if}
						{elseif $option.is_best_price}
							<span class="best_grade best_grade_price">{l s='The best price' mod='ecm_simcheck'}</span>
						{/if}
					{/if}
				</td>
				
				{if Configuration::get('ecm_simcheck_show_price')}
				<td class="_delivery_option_price">
					{$ref = $option['carrier_list'][trim($key,',')]['instance']->id_reference}
					{if $option.total_price_with_tax && !$option.is_free && (!isset($free_shipping) || (isset($free_shipping)))}
						{if isset($cs[$ref]["'cost'"]["'replace'"])}
							{if $cs[$ref]["'cost'"]["'value'"] != ''}
								{$cs[$ref]["'cost'"]["'value'"]}
							{else}
								{l s='By carrier tariff' mod='ecm_simcheck'}
							{/if}
						{else}
							{if $use_taxes == 1}
								{if $priceDisplay == 1}
									{convertPrice price=$option.total_price_without_tax}{if $display_tax_label} {l s='(tax excl.)' mod='ecm_simcheck'}{/if}
								{else}
									{convertPrice price=$option.total_price_with_tax}{if $display_tax_label} {l s='(tax incl.)' mod='ecm_simcheck'}{/if}
								{/if}
							{else}
								{convertPrice price=$option.total_price_without_tax}
							{/if}
						{/if}
					{else}
						{l s='Free' mod='ecm_simcheck'}
					{/if}
				</td>
				{/if}
			</tr>
		{/foreach}
		</table>
	{/foreach}
{/if}
</div>
{if $info}
<label class="sc-label"><i class="icon icon-info"></i> {l s='Additional info about carrier' mod='ecm_simcheck'}</label>
<div class="table_block table-responsive">
	<table class="table _table-bordered"><tr><td>
	{$info}
	</td></tr></table>
</div>
{/if}

{if isset($HOOK_EXTRACARRIER_ADDR) && $id_carrier && isset($HOOK_EXTRACARRIER_ADDR.$id_address)}
<div class="hook_extracarrier" id="HOOK_EXTRACARRIER_{$id_address}">
	{$HOOK_EXTRACARRIER_ADDR.$id_address}
</div>
{/if}
{if !$id_carrier}
<script>
$('#carrier_place').hide();
</script>
{/if}

<script>
//(".delivery_sc_radio").on("click", function (){
//	action('set_carrier,carrier,cart', $(".delivery_sc_radio:checked").val());
//});
$(".sc_carrier_row").on("click", function (){
	$(".delivery_sc_radio").removeAttr('checked');
	$(".sc_carrier_row").removeClass('sc_carrier_row_checked');
	$(this).addClass('sc_carrier_row_checked');
	action('set_carrier,cart,carrier,payment,checkout,customer', $(this).attr('value'));
});

var cart_qties = {$cart_qties};
var cart = {$cart|@json_encode};



</script>

	
