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



    <div class="product-prices">


        {block name='hook_display_product_rating'}
            {hook h='displayProductRating' product=$product}
        {/block}



        

        {if $product.show_price}



        {block name='product_price'}
            <div class="{if $product.has_discount}has-discount{/if}"
                 itemprop="offers"
                 itemscope
                 itemtype="https://schema.org/Offer"
            >

                {if isset($product.seo_availability)}
                    <link itemprop="availability" href="{$product.seo_availability}"/>
                {else}
                    <link itemprop="availability" href="https://schema.org/InStock"/>
                {/if}
                <meta itemprop="priceCurrency" content="{$currency.iso_code}">

                <div>
                    {if $product.has_discount}
                        <span class="product-discount">
                            {hook h='displayProductPriceBlock' product=$product type="old_price"}
                            <span class="regular-price">{$product.regular_price}</span>
                         </span>
                     {/if}    
                    <span class="current-price" style="color: #ed1b34;font-size: 30px;font-weight: bold;" itemprop="price" class="product-price" content="{$product.price_amount}">{$product.price}</span>
                    {if $product.has_discount}
                        {if isset($product.specific_prices.to) && $product.specific_prices.to != '0000-00-00 00:00:00'}<meta itemprop="priceValidUntil" content="{$product.specific_prices.to}"/>{/if}
                    {/if}
                </div>
                {if $prices_im_act}
                <span style="color: #ffffff;font-size: 15px;font-weight: bold;margin: 9px 0;padding: 4px 7px;display: inline-block;background-color: #ed1b34;" >{l s='Цена в гипермаркете:' d='Shop.Theme.Catalog'} {$prices_im.0.price_retail|string_format:"%.2f"} {l s='грн' d='Shop.Theme.Catalog'}</span>
                {if $prices_im.0.factor}
                <span style="color: #ed1b34;font-size: 15px;font-weight: bold;margin: 9px 0;display: inline-block;" >{l s='Внимание! Цена:' d='Shop.Theme.Catalog'} {$prices_im.0.price|string_format:"%.2f"} {l s='грн' d='Shop.Theme.Catalog'} {l s='действует при покупке товара до' d='Shop.Theme.Catalog'} {$prices_im.0.factor|string_format:"%.0f"} {l s='единиц.' d='Shop.Theme.Catalog'}</span>
                {/if}
                {/if}
                {block name='horeca_price'}
                  {if $id_shop_group == 2}
                  <span style="color: #046f29;font-size: 20px;font-weight: bold;margin: 9px 0;display: block;" >{l s='Розница:' d='Shop.Theme.Catalog'} {$product.z_price|string_format:"%.2f"} {l s='грн' d='Shop.Theme.Catalog'}</span>
                  <span style="color: #302e3a;font-size: 15px;font-weight: bold;margin: 9px 0;display: block;" >{l s='Категория:' d='Shop.Theme.Catalog'} {$product.wholesale_price|string_format:"%.2f"} {l s='грн' d='Shop.Theme.Catalog'}</span>
                  {/if}
                {/block}
            </div>
        {/block}

        {block name='product_without_taxes'}
            {if $priceDisplay == 2}
                <p class="product-without-taxes text-muted">{l s='%price% tax excl.' d='Shop.Theme.Catalog' sprintf=['%price%' => $product.price_tax_exc]}</p>
            {/if}
        {/block}

        {block name='product_pack_price'}
            {if $displayPackPrice}
                <p class="product-pack-price">
                    <span>{l s='Instead of %price%' d='Shop.Theme.Catalog' sprintf=['%price%' => $noPackPrice]}</span>
                </p>
            {/if}
        {/block}

        {block name='product_ecotax'}
            {if $product.ecotax.amount > 0}
                <p class="price-ecotax text-muted">{l s='Including %amount% for ecotax' d='Shop.Theme.Catalog' sprintf=['%amount%' => $product.ecotax.value]}
                    {if $product.has_discount}
                        {l s='(not impacted by the discount)' d='Shop.Theme.Catalog'}
                    {/if}
                </p>
            {/if}
        {/block}

        {hook h='displayProductPriceBlock' product=$product type="weight" hook_origin='product_sheet'}

        <div class="tax-shipping-delivery-label text-muted">
            {if $configuration.display_taxes_label}
                {$product.labels.tax_long}
            {/if}
            {hook h='displayProductPriceBlock' product=$product type="price"}
            {hook h='displayProductPriceBlock' product=$product type="after_price"}
            {if $product.additional_delivery_times == 1}
                {if $product.delivery_information}
                    <span class="delivery-information">{$product.delivery_information}</span>
                {/if}
            {elseif $product.additional_delivery_times == 2}
                {if $product.quantity > 0}
                    <span class="delivery-information">{$product.delivery_in_stock}</span>
                    {* Out of stock message should not be displayed if customer can't order the product. *}
                    {elseif $product.quantity <= 0 && $product.add_to_cart_url}
                    <span class="delivery-information">{$product.delivery_out_stock}</span>
                {/if}
            {/if}
        </div>
        {hook h='displayCountDown'}
        {/if}
    </div>






