{**
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * We are experts and professionals in PrestaShop
 *
 * @author    Elcommerce <support@elcommece.com.ua>
 * @copyright 2010-2018 Elcommerce
 * @license   Comercial
 * @category  PrestaShop
 * @category  Module
*}

{extends file=$layout}

{block name='content'}
<!-- ecm_simplecheckout module -->
	{if $cart_qties == 0}
		<div class="row col-lg-12 col-xs-12 card">
			<p class="alert alert-warning">{l s='Your shopping cart is empty.' mod='ecm_checkout'}</p>
		</div>
	{else}
	<div id="ecm_checkout">	
		<div class="row col-lg-4 col-sm-12 float-left">
			<div class="cart-grid-right col-xs-12 col-lg-12">
				<p class="alert alert-warning" id="errors_msg" hidden><span id="login_errors"></span></p>
				<span id="customer_place" class="card">{$customer_place nofilter}</span>
				<span id="carrier_place" class="card">{$carrier_place nofilter}</span>
				<span id="payment_place" class="card">{$payment_place nofilter}</span>
			</div>
			
		</div>
		<div class="col-lg-8 col-sm-12 float-left">
			<span id="cart_place" class="card">{$cart_place nofilter}</span>
			<span id="checkout_place" class="card">{$checkout_place nofilter}</span>
		</div>
	</div>	
	{/if}

	<div class="loading" id="pleaseWaitDialog"></div>
		
{include 'module:ecm_checkout/views/templates/front/trans_js.tpl'}

<!-- /ecm_simplecheckout module -->
{/block}