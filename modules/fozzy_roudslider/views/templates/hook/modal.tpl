<div id="blockcart-modal-{$product.id}" class="blockcart-modal-product modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <span class="h4">{$product.name} <span class="fasovka">/ {$product.unity}</span></span>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row align-items-center"">
                    <div class="col-xs-12 divide-right mb-1">
                        <div class="row no-gutters align-items-center">
                             <div class="product_info">        
                                  <div class="col-7 divide-right">
                                    <span class="product-price">{$product.price}</span>
                                    
                                  </div>
                                  <div class="col-5 product-reference_summ">                            
                                  {foreach from=$product_sp item=price_collection}
                                        <span class="product-price-opt-summ">{l s='При покупке' d='Shop.Theme.Checkout'} <span class="opt-qty">{if $product.unity == 'кг'}{$price_collection.from_quantity|string_format:"%.2f"}{else}{$price_collection.from_quantity|string_format:"%d"}{/if}</span>+ <br><span class="product-price-opt"><span class="opt-price">{$price_collection.price|string_format:"%.2f"}</span> грн</span></span>
                                    {/foreach}
                                  </div>    
                             </div>     
                                  <div class="types">
                                      <div class="container">
                                          <div class="control">
                                              <div id="type_{$product.id_product}" class="krutilka"></div>
                                          </div>
                                      </div>
                                  </div>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <div class="cart-content pt-0">
                            <div class="product-in_cart">{l s='Сейчас в корзине:' d='Shop.Theme.Actions'}
                            <span>
                              {if $product.unity == 'кг'}
                                {if $product.cart_quantity < 1}
                                  {$product.cart_quantity*1000|string_format:"%d"}&nbsp;гр
                                {else}
                                  {$product.cart_quantity|string_format:"%.2f"}&nbsp;кг
                                {/if}
                              {else}
                                {$product.cart_quantity|string_format:"%d"}&nbsp;шт
                              {/if}
                            </span>
                            </div>
                            <div class="cart-content-btn">
                            <input type="hidden" name="cart_quantity" value="{$product.cart_quantity}">
                            <input type="hidden" name="product_price" value="{$product.price}">
                            <input type="hidden" name="price_with_reduction" value="{$product.price_with_reduction}">
                            <input type="hidden" name="start_step" value="{if $product.unity == 'кг'}0.1{else}1{/if}">
                            <input type="hidden" name="half_step" value="{if $product.unity == 'кг'}0.5{else}1{/if}">
                            <input type="hidden" name="start_ed" value="{if $product.unity == 'кг'}кг{else}шт{/if}">
                            <input type="hidden" id="popup_change_{$product.id_product}" value="0">
                            <input type="hidden" id="popup_fromcart_{$product.id_product}" value="0">
                                <div class="product-add-cart">
                                        <form action="{$product.update_quantity_url}" method="post">
                            
                                        <input type="hidden" name="id_product" value="{$product.id_product}">
                                        <div class="input-group input-group-add-cart">
                                         <button tabindex="1" id="popup_input_{$product.id_product}" class="col-10 btn btn-product-list add-to-cart set_{$product.id_product}" data-button-action="add-to-cart" type="submit">{l s='Применить' d='Shop.Theme.Actions'}</button>
                                        <div class="qty" style="display:none;"> 
                                            <input name="qty" value="{$product.cart_quantity}" class="input-group form-control input-qty" min="{$product.minimal_quantity}" style="display: block;"></div>
                                        <a class="col-1 remove-from-cart" rel="nofollow" href="{$product.remove_from_cart_url}" data-link-action="delete-from-cart"  data-id-product="{$product.id_product}" data-id-product-attribute="0" data-id-customization="" title="{l s='Удалить' d='Shop.Theme.Actions'}">
            <i class="fa fa-trash-o" aria-hidden="true"></i>
        </a>
                                        </div>  
                                        </div>
                            
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
      </div>