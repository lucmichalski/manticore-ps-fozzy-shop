	{if isset($products) && $products}
	<div class="block_content">
		{assign var='nbItemsPerLine' value=4}
		{assign var='nbLi' value=$products|@count}

		{if isset($image_type) && isset($image_types[$image_type])}  
			{assign var='imageSize' value=$image_types[$image_type].name}
		{else}
			{assign var='imageSize' value='home_default'} 
		{/if}
    {if !isset($id)}
    {assign var='id' value='no_id'}
    {/if}
    {assign var='colnb' value=2}
    {assign var='category_products' value=1}
    {assign var='line_xs' value=1}
    {assign var='line_ms' value=1}
    {assign var='line_sm' value=3}
    {assign var='line_md' value=4}
    {assign var='line_lg' value=4}
		<div {if isset($id) && $id} id="{$id}"{/if} {if ! isset($ar)} {if isset($warehouse_vars.carousel_style) && $warehouse_vars.carousel_style == 0} class="alternative-slick-arrows" {/if}{/if} >
			<div class="slick_carousel {if isset($iqitGenerator)}iqitcarousel{else}slick_carousel_defaultp{/if} slick_carousel_style " {if ( isset($iqitGenerator) || isset($category_products) ) }data-slick='{literal}{{/literal}{if isset($category_products)}"dots": false, {else if $dt}"dots": true, {/if} "slidesToShow": {$line_lg}, "slidesToScroll": {$line_lg}, "responsive": [ 
					{ "breakpoint": 1320, "settings": { "slidesToShow": {$line_md}, "slidesToScroll": {$line_md}}}, { "breakpoint": 1000, "settings": { "slidesToShow": {$line_sm}, "slidesToScroll": {$line_sm}}}, { "breakpoint": 768, "settings": { "slidesToShow": {$line_ms}, "slidesToScroll": {$line_ms}}}, { "breakpoint": 480, "settings": { "slidesToShow": {$line_xs}, "slidesToScroll": {$line_xs}}} ]{literal}}{/literal}'{/if} >
				{foreach from=$products item=product name=homeFeaturedProducts}
				{if isset($colnb)}{if ($smarty.foreach.homeFeaturedProducts.first)}<div class="iqitcarousel-produc">{/if}{/if}
				<div class="ajax_block_product {if $smarty.foreach.homeFeaturedProducts.first}first_item{elseif $smarty.foreach.homeFeaturedProducts.last}last_item{else}item{/if} {if $smarty.foreach.homeFeaturedProducts.iteration%$nbItemsPerLine == 0}last_item_of_line{elseif $smarty.foreach.homeFeaturedProducts.iteration%$nbItemsPerLine == 1} {/if} {if $smarty.foreach.homeFeaturedProducts.iteration > ($smarty.foreach.homeFeaturedProducts.total - ($smarty.foreach.homeFeaturedProducts.total % $nbItemsPerLine))}last_line{/if}">
					<div class="product-container clearfix">
						
						<div class="product-image-container">
						{capture}add=1&amp;id_product={$product.id_product|intval}{if isset($static_token)}&amp;token={$static_token}{/if}{/capture}
						<a  href="{$link->getPageLink('cart', true, NULL, $smarty.capture.default, false)|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='Add to cart'} {$product.name|escape:'html':'UTF-8'}">
									<img src="/img/mars/{$product.id_product}.png" style="width: 100%;border:none;" />
								</a>

			

					</div><!-- .product-image-container> -->
					{if isset($product.is_virtual) && !$product.is_virtual}{hook h="displayProductDeliveryTime" product=$product}{/if}

				 {if (((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
					<div  class="content_price">
						{if isset($product.show_price) && $product.show_price && !isset($restricted_country_mode)}
							<span class="price product-price">
								{hook h="displayProductPriceBlock" product=$product type="before_price"}
								{$product.price}
							</span>
							<meta  content="{$currency->iso_code}" />
							
						{/if}
					</div>
					{/if}


					</div><!-- .product-container> -->


					</div>
					{if isset($colnb)}
					{if ($smarty.foreach.homeFeaturedProducts.iteration%$colnb == 0) && !$smarty.foreach.homeFeaturedProducts.last}</div><div class="iqitcarousel-product">{/if}
					{if $smarty.foreach.homeFeaturedProducts.last}</div>{/if}{/if}
					{/foreach}
				</div>
			</div>
		</div>

{/if}