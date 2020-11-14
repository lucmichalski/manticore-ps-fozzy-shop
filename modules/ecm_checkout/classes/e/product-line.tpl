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



<tr id="product_{$product.id_product}_{$product.id_product_attribute}_{if $quantityDisplayed > 0}nocustom{else}0{/if}_{$product.id_address_delivery|intval}{if !empty($product.gift)}_gift{/if}" class="cart_item{if isset($productLast) && $productLast && (!isset($ignoreProductLast) || !$ignoreProductLast)} last_item{/if}{if isset($productFirst) && $productFirst} first_item{/if}{if isset($customizedDatas.$productId.$productAttributeId) AND $quantityDisplayed == 0} alternate_item{/if} address_{$product.id_address_delivery|intval} {if $odd}odd{else}even{/if}">
	<td>
		<a href="{$link->getProductLink($product.id_product, $product.link_rewrite, $product.category, null, null, $product.id_shop, $product.id_product_attribute, false, false, true)|escape:'html':'UTF-8'}">
		<img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'small_default')|escape:'html':'UTF-8'}" 
		alt="{$product.name|escape:'html':'UTF-8'}" {if isset($smallSize)}
		width="{$smallSize.width}px" height="{$smallSize.height}px" {/if} /></a>
	</td>
	<td class="cart_description">
		{capture name=sep} : {/capture}
		{capture}{l s=' : '}{/capture}
		<p class="product-name"><a href="{$link->getProductLink($product.id_product, $product.link_rewrite, $product.category, null, null, $product.id_shop, $product.id_product_attribute, false, false, true)|escape:'html':'UTF-8'}">{$product.name|escape:'html':'UTF-8'}</a></p>
		{if isset($product.attributes) && $product.attributes}<small><a href="{$link->getProductLink($product.id_product, $product.link_rewrite, $product.category, null, null, $product.id_shop, $product.id_product_attribute, false, false, true)|escape:'html':'UTF-8'}">{$product.attributes|@replace: $smarty.capture.sep:$smarty.capture.default|escape:'html':'UTF-8'}</a></small>{/if}
		{if $product.reference}<small class="cart_ref">{l s='SKU' mod='ecm_simcheck'}{$smarty.capture.default}{$product.reference|escape:'html':'UTF-8'}</small>{/if}
	</td>
	<td class="cart_unit text-right" data-title="{l s='Unit price' mod='ecm_simcheck'}">
		<ul class="price" id="product_price_{$product.id_product}_{$product.id_product_attribute}{if $quantityDisplayed > 0}_nocustom{/if}_{$product.id_address_delivery|intval}{if !empty($product.gift)}_gift{/if}">
			{if !empty($product.gift)}
				<li class="gift-icon">{l s='Gift!' mod='ecm_simcheck'}</li>
			{else}
            	{if !$priceDisplay}
					<li class="price{if isset($product.is_discounted) && $product.is_discounted && isset($product.reduction_applies) && $product.reduction_applies} special-price{/if}">{convertPrice price=$product.price_wt}</li>
				{else}
               	 	<li class="price{if isset($product.is_discounted) && $product.is_discounted && isset($product.reduction_applies) && $product.reduction_applies} special-price{/if}">{convertPrice price=$product.price}</li>
				{/if}
				{if isset($product.is_discounted) && $product.is_discounted && isset($product.reduction_applies) && $product.reduction_applies}
                	<li class="price-percent-reduction small">
            			{if !$priceDisplay}
            				{if isset($product.reduction_type) && $product.reduction_type == 'amount'}
                    			{assign var='priceReduction' value=($product.price_wt - $product.price_without_specific_price)}
                    			{assign var='symbol' value=$currency->sign}
                    		{else}
                    			{assign var='priceReduction' value=(($product.price_without_specific_price - $product.price_wt)/$product.price_without_specific_price) * 100 * -1}
                    			{assign var='symbol' value='%'}
                    		{/if}
						{else}
							{if isset($product.reduction_type) && $product.reduction_type == 'amount'}
								{assign var='priceReduction' value=($product.price - $product.price_without_specific_price)}
								{assign var='symbol' value=$currency->sign}
							{else}
								{assign var='priceReduction' value=(($product.price_without_specific_price - $product.price)/$product.price_without_specific_price) * -100}
								{assign var='symbol' value='%'}
							{/if}
						{/if}
						{if $symbol == '%'}
							&nbsp;{$priceReduction|string_format:"%.2f"|regex_replace:"/[^\d]0+$/":""}{$symbol}&nbsp;
						{else}
							&nbsp;{convertPrice price=$priceReduction}&nbsp;
						{/if}
					</li>
					<li class="old-price">{convertPrice price=$product.price_without_specific_price}</li>
				{/if}
			{/if}
		</ul>
	</td>

	<td class="cart_quantity text-center" data-title="{l s='Quantity' mod='ecm_simcheck'}">
		{if (isset($cannotModify) && $cannotModify == 1)}
			<span>
				{if $quantityDisplayed == 0 AND isset($customizedDatas.$productId.$productAttributeId)}
					{$product.customizationQuantityTotal}
				{else}
					{$product.cart_quantity-$quantityDisplayed}
				{/if}
			</span>
		{else}
			{if isset($customizedDatas.$productId.$productAttributeId) AND $quantityDisplayed == 0}
				<span id="cart_quantity_custom_{$product.id_product}_{$product.id_product_attribute}" >{$product.customizationQuantityTotal}</span>
			{/if}
			{if !isset($customizedDatas.$productId.$productAttributeId) OR $quantityDisplayed > 0}
				<input type="hidden" value="{if $quantityDisplayed == 0 AND isset($customizedDatas.$productId.$productAttributeId)}{$customizedDatas.$productId.$productAttributeId|@count}{else}{$product.cart_quantity-$quantityDisplayed}{/if}" name="quantity_{$product.id_product}_{$product.id_product_attribute}_hidden" />
				<input size="2" type="text" autocomplete="off" 
					class="input-number cart_quantity_input form-control grey" min="{$product.minimal_quantity}"
					value="{if $quantityDisplayed == 0 AND isset($customizedDatas.$productId.$productAttributeId)}{$customizedDatas.$productId.$productAttributeId|@count}{else}{$product.cart_quantity-$quantityDisplayed}{/if}"  
					name="quantity_{$product.id_product}_{$product.id_product_attribute}" 
					id="quantity_{$product.id_product}_{$product.id_product_attribute}"
					prod_attr = "{$product.id_product}_{$product.id_product_attribute}"
					act="set_quantity,{$renderSeq}"
					/>
			    <div class="cart_quantity_button clearfix">
			        <a rel="nofollow" class="cart_quantity_down btn btn-default button-minus"
			        onclick="action('quantity_down,{$renderSeq}', '{$product.id_product}_{$product.id_product_attribute}')"
			        id="cart_quantity_down_{$product.id_product}_{$product.id_product_attribute}" 
			        title="{if $product.cart_quantity-$quantityDisplayed == 1}{l s='Delete' mod='ecm_simcheck'}{else}{l s='Remove' mod='ecm_simcheck'}{/if}">
			            <span><i class="icon-minus"></i></span>
			        </a>
			        <a rel="nofollow" class="cart_quantity_up btn btn-default button-plus" 
			        onclick="action('quantity_up,{$renderSeq}', '{$product.id_product}_{$product.id_product_attribute}')"
			        id="cart_quantity_up_{$product.id_product}_{$product.id_product_attribute}"  title="{l s='Add' mod='ecm_simcheck'}">
			            <span><i class="icon-plus"></i></span>
			        </a>
			    </div>
			{/if}
		{/if}

	</td>
	<td class="align-text-top align-top" width="5%" >
		{if empty($product.gift)}
			<a rel="nofollow" class="cart_quantity_delete btn" 
	        onclick="action('quantity_delete,{$renderSeq}', '{$product.id_product}_{$product.id_product_attribute}')"
	        id="cart_quantity_delete_{$product.id_product}_{$product.id_product_attribute}"  title="{l s='Delete' mod='ecm_simcheck'}">
	            <span><i class="icon-trash icon-sc-trash"></i></span>
	        </a>
        {/if}
	</td>
	<td width="16.666667%" class="cart_total text-right" data-title="{l s='Total' mod='ecm_simcheck'}">
		<span class="price" id="total_product_price_{$product.id_product}_{$product.id_product_attribute}{if $quantityDisplayed > 0}_nocustom{/if}_{$product.id_address_delivery|intval}{if !empty($product.gift)}_gift{/if}">
			{if !empty($product.gift)}
				<span class="gift-icon">{l s='Gift!' mod='ecm_simcheck'}</span>
			{else}
				{if $quantityDisplayed == 0 AND isset($customizedDatas.$productId.$productAttributeId)}
					{if !$priceDisplay}{displayPrice price=$product.total_customization_wt}{else}{displayPrice price=$product.total_customization}{/if}
				{else}
					{if !$priceDisplay}{displayPrice price=$product.total_wt}{else}{displayPrice price=$product.total}{/if}
				{/if}
			{/if}
		</span>
		{hook h='displayCartExtraProductActions' product=$product}
	</td>
</tr>

{if Configuration::Get('ecm_gre_active')}
	{$name = $product.name|escape:'quotes'}
	{$brand = $product.manufacturer_name|escape:'quotes'}
	{$category_name = $product.category_name|escape:'quotes'}
	<script>
	var comb = "#quantity_{$product.id_product}_{$product.id_product_attribute}";
	var to_push = {
		'name': '{$name}',
		'id': '{$product.reference}',
		'price': '{$product.price}',
		'brand': '{$brand}',
		'category': '{$category_name}',
		'quantity': $(comb).val(),
	}
	//console.log(to_push)
	gre_products.push(to_push);
	gre_products_references += "'{$product.reference}'"+',';
	gre_products_prices += "'{$product.price}'"+',';
	</script>
{/if}