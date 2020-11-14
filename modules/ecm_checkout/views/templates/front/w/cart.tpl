{**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
  
<div class="card-block"></div>
<section class="card-block">
    <ul class="cart-items">
      <li class="cart-item-header hidden-sm-down">
		<div class="row small-gutters">
	        <div class="col-6">{l s='Product' mod='ecm_checkout'}</div>
            <div class="col-6">
	            <div class="row small-gutters">
			        <div class="col">{l s='Unit price' mod='ecm_checkout'}</div>
	                <div class="col">{l s='Qty' mod='ecm_checkout'}</div>
	                <div class="col">{l s='Total' mod='ecm_checkout'}</div>
	                <div class="col col-auto"><i class="fa fa-trash-o invisible" aria-hidden="true"></i></div>
                </div>
            </div>
        </div>
       </li>
     
      {foreach from=$cart.products item=product}
       <li class="cart-item">
            {include file='./product-line.tpl' product=$product}
       </li>
       {if $product.customizations|count >1}<hr>{/if}
      {/foreach}
    </ul>
</section>

<script>

$(".cart_quantity_input").on("input change", function (){
	delay_quantity(this,2000,true);
})

$(".cart_quantity_input").on("blur", function (){
	delay_quantity(this,0,false);
})


var cart_qties = {$cart_qties|intval};
var id_cart = {$id_cart|intval};

</script>

