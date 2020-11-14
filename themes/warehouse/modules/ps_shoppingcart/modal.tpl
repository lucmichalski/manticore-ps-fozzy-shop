<div id="blockcart-modal-wrap">

<div id="blockcart-notification" class="ns-box {if !$product}ns-box-danger{/if} ns-effect-thumbslider">
    <div class="ns-box-inner row align-items-center no-gutters">
        {if $product}
        {if $product.cover}
        <div class="ns-thumb col-3">
            <img src="{$product.cover.bySize.small_default.url}"
                 alt="{$product.name|escape:'quotes'}"
                 class="img-fluid">
        </div>
        {/if}
        <div class="ns-content col-9">
            <span class="ns-title"><i class="fa fa-check" aria-hidden="true"></i> <strong>{$product.name}</strong> {l s='is added to your shopping cart' d='Shop.Theme.Checkout'}</span>
        </div>
        <div class="ns-delivery col-12 mt-4">{hook h='displayCartAjaxInfo'}</div>
        {else}
            <div class="ns-content col-12">
                <span class="ns-title"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i>  {l s='There are not enough products in stock' d='Shop.Theme.Checkout'}</span>
            </div>
        {/if}

    </div>
</div>

</div>