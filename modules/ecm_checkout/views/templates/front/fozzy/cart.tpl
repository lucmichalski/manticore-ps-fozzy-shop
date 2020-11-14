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
  
<div class="card-block">
<h5 class="h5">{l s='Products' mod='ecm_checkout'}</h5>
<hr class="separator">
</div>



  <div class="cart-overview">
    <ul class="cart-items">
      {foreach from=$cart.products item=product}
        <li class="cart-item">
            {include file='./product-line.tpl' product=$product}
        </li>
        {if $product.customizations|count >1}<hr>{/if}
      {/foreach}
    </ul>
  </div>

<script>
document.addEventListener('DOMContentLoaded', function(){
	add_cart_event()
})    

if (typeof($)=='function'){
	$(document).ajaxComplete(function( event, xhr, settings) {
		add_cart_event()
	})
}

function add_cart_event(){


$(".cart_quantity_input").off("input change")
$(".cart_quantity_input").on("input change", function (){
	delay_quantity(this,2000,true);
})

$(".cart_quantity_input").off("blur")
$(".cart_quantity_input").on("blur", function (){
	delay_quantity(this,0,false);
})


}
</script>

