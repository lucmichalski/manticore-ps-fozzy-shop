<section>
  <h2>
    {if $products|@count == 1}
      {l s='Product by same brand:' d='Modules.Nove_Brandproducts.Shop'}
    {else}
      {l s='Products by same brand:' d='Modules.Nove_Brandproducts.Shop'}
    {/if}
  </h2>
  <div>
      {foreach from=$products item="product"}
          {include file="catalog/_partials/miniatures/product.tpl" product=$product}
      {/foreach}
  </div>
</section>
