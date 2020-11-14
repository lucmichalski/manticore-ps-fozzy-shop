{**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
{block name='product_thumbnail'}
    {include file='catalog/_partials/miniatures/_partials/product-miniature-thumb.tpl'}
{/block}

<div class="product-description">
    {block name='product_category_name'}
        {if $product.category_name != ''}
            <div class="product-category-name text-muted">{$product.category_name}</div>{/if}
    {/block}

    {block name='product_name'}
        <div class="h3 product-title">
            <a href="{$product.url}">{$product.name|truncate:42:'...'}</a>
        </div>
    {/block}

    {block name='product_brand'}
        {if isset($product.manufacturer_name ) && $product.manufacturer_name != ''}
            <div class="product-brand text-muted"> <a href="{$product.url}">{$product.manufacturer_name}</a></div>{/if}
    {/block}
    
    {block name='product_unity'}
        {if $product.unity != ''}
            <div class="product-reference text-muted"> <a href="{$product.url}">{l s='Фасовка' d='Shop.Theme.Actions'}: {$product.unity}</a></div>{/if}
    {/block}
    
    {block name='product_reference'}
        {if $product.reference != ''}
            <div class="product-reference text-muted"> <a href="{$product.url}">{l s='Артикул' d='Shop.Theme.Actions'}: {$product.reference}</a></div>{/if}
    {/block}
    

    
    {block name='product_reviews'}
        {hook h='displayProductListReviews' product=$product}
    {/block}
    {block name='product_price_and_shipping'}
        {if $product.show_price}   
            {if $listing.id_shop_group == 2}
             <div class="product-reference text-muted">{l s='Остаток' d='Shop.Theme.Actions'}: {$product.quantity} </div>
            {/if}
            <div class="product-price-and-shipping">
                {hook h='displayProductPriceBlock' product=$product type="before_price"}
                <a href="{$product.url}"> <span  class="product-price" content="{$product.price_amount}">{$product.price}</span></a>
                {if $product.has_discount}
                    {hook h='displayProductPriceBlock' product=$product type="old_price"}
                    <span class="regular-price text-muted">{$product.regular_price}</span>
                {/if}
                
                {if $product.price_retail}
                <span style="color: #ffffff;font-size: 15px;font-weight: bold;margin: 9px 0;padding: 4px 7px;display: inline-block;background-color: #ed1b34;" >{l s='Цена в гипермаркете:' d='Shop.Theme.Catalog'} {$product.price_retail|string_format:"%.2f"} {l s='грн' d='Shop.Theme.Catalog'}</span>{/if}
                
                
                
                {hook h='displayProductPriceBlock' product=$product type='unit_price'}
                {hook h='displayProductPriceBlock' product=$product type='weight'}
                {if $product.has_discount}
                    {hook h='displayCountDown'}
                {/if}
            </div>
        {/if}
    {/block}



    {block name='product_variants'}
        {if $product.main_variants}
            <div class="products-variants">
                {if $product.main_variants}
                    {include file='catalog/_partials/variant-links.tpl' variants=$product.main_variants}
                {/if}
            </div>
        {/if}
    {/block}

    {block name='product_description_short'}
        <div class="product-description-short text-muted">
            <a href="{$product.url}">{$product.description_short|strip_tags:'UTF-8'|truncate:360:'...'}</a>
        </div>
    {/block}

    {block name='product_add_cart'}
        {include file='catalog/_partials/miniatures/_partials/product-miniature-btn.tpl'}
    {/block}

    {block name='product_add_cart_below'}
        {hook h='displayProductListBelowButton' product=$product}
    {/block}

</div>
