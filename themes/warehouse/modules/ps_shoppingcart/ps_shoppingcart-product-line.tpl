<div class="row no-gutters align-items-center">
    <div class="col-2">
        <span class="product-image media-middle">
      {if $product.cover} <a href="{$product.url}"><img src="{$product.cover.bySize.small_default.url}" alt="{$product.name|escape:'quotes'}"
                                   class="img-fluid"></a>{/if}
</span>
    </div>
    <div class="col col-info">
        <div class="pb-1">
            <a href="{$product.url}">{$product.name}</a>
        </div>

        {if isset($product.attributes) && $product.attributes}
            <div class="product-attributes text-muted pb-1">
                {foreach from=$product.attributes key="attribute" item="value"}
                    <div class="product-line-info">
                        <span class="label">{$attribute}:</span>
                        <span class="value">{$value}</span>
                    </div>
                {/foreach}
            </div>
        {/if}

        <span>{$product.price}</span>
    </div>
    <div class="col col-qty">
        <a id="btn_change_incart_{$product.id}" data-toggle="modal" data-link-place="cart-preview" data-target="#blockcart-modal-{$product.id}" data-product="{$product.id}" class="btn btn-product-list in-a-cart">
        <span class="down-inbutton"><i class="fa fa-minus cbp-iconbars"></i></span>
        {if $product.unity == 'кг'}
        <span class="button_qty_in_wrapper">
        <span class="button_qty_in">
                    {if $product.cart_quantity < 1}
                      {$product.cart_quantity*1000|string_format:"%d"}
                    {else}
                      {$product.cart_quantity|string_format:"%.2f"}
                    {/if}
        </span>
        <span class="button_qty_in_u">{if ($product.cart_quantity > 0 && $product.cart_quantity < 1) || (!$product.cart_quantity)} гр{else} кг{/if}</span>
        </span>
        {else}
        <span class="button_qty_in_wrapper"><span class="button_qty_in">{$product.cart_quantity|string_format:"%d"}</span> шт</span>{/if}
        <span class="up-inbutton"><i class="fa fa-plus cbp-iconbars"></i></span></a>
            
    </div>
    <div class="col col-1">
        <a class="remove-from-cart"
           rel="nofollow"
           href="{$product.remove_from_cart_url}"
           data-link-action="delete-from-cart"
           data-link-place="cart-preview"
           data-id-product             = "{$product.id_product|escape:'javascript'}"
           data-minimal = "{if $product.unity == 'кг'}{$product.minimal_quantity|string_format:"%.2f"}{else}{$product.minimal_quantity|string_format:'%d'}{/if}"
           data-id-product-attribute   = "{$product.id_product_attribute|escape:'javascript'}"
           data-id-customization   	  = "{$product.id_customization|escape:'javascript'}"
           title="{l s='remove from cart' d='Shop.Theme.Actions'}"
        >
            <i class="fa fa-trash-o" aria-hidden="true"></i>
        </a>
    </div>
</div>
{hook h='displayModalCartExtraProductActions' product=$product}

