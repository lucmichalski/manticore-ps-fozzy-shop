<div id="product-details" data-product="{$product.embedded_attributes|json_encode}" class="clearfix">
{block name='product_features'}
        <section class="product-features global-features">
            <dl class="data-sheet">
                    <dt class="name">{l s='Артикул' d='Shop.Theme.Catalog'}: {$product.reference}</dt>
                    <dd class="value">{l s='Штрихкод' d='Shop.Theme.Catalog'}: {$product.ean13}</dd>
            </dl>
        </section>
        <section class="product-features">
            <dl class="data-sheet">
            {if isset($product_manufacturer->id)}
                    <meta itemprop="brand" content="{$product_manufacturer->name}">
                    <dt class="name">{l s='Brand' d='Shop.Theme.Catalog'}</dt>
                    <dd class="value"> <a href="{$product_brand_url}">{$product_manufacturer->name}</a></dd>
            {/if} 
            {if isset($product.unity)}
                    <dt class="name">{l s='Фасовка' d='Shop.Theme.Catalog'}</dt>
                    <dd class="value"> {$product.unity}</dd>
            {/if}
                {foreach from=$product.grouped_features item=feature}
                    <dt class="name">{$feature.name}</dt>
                    <dd class="value">{$feature.value|escape:'htmlall'|nl2br nofilter}</dd>
                {/foreach}
            </dl>
        </section>
{/block}

{block name='product_quantities'}
    {if $product.show_quantities}
        <div class="product-quantities">
            <label class="label">{l s='In stock' d='Shop.Theme.Catalog'}</label>
            <span data-stock="{$product.quantity}" data-allow-oosp="{$product.allow_oosp}">{$product.quantity} {if $product.unity == 'кг'}{$product.unity}{else}{$product.quantity_label}{/if}</span>
        </div>
    {/if}
{/block}


{* if product have specific references, a table will be added to product details section *}
{block name='product_specific_references'}
    {if !empty($product.specific_references)}
        <div class="specific-references">
            {foreach from=$product.specific_references item=reference key=key}
                <div>
                    <label class="label">{$key}</label>
                    <span>{$reference}</span>
                </div>
            {/foreach}
        </div>
    {/if}
{/block}

{block name='product_out_of_stock'}
    <div class="product-out-of-stock">
        {hook h='actionProductOutOfStock' product=$product}
    </div>
{/block}
</div>



