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
		<div class="row col-lg-12 col-xs-12">
			<p class="cart-grid-body alert alert-warning">{l s='Your shopping cart is empty.' mod='ecm_checkout'}</p>
		</div>
	{else}
	<div id="ecm_checkout">	
		<div class="row col-lg-12 col-sm-12">
			<div class="cart-grid-right col-xs-12 col-lg-12">
				<div id="cart_place" class="card"></div>
				<p class="alert alert-warning" id="errors_msg" hidden>
				<span id="login_errors"></span></p>
			</div>
		</div>
		<div class="row col-lg-12 col-sm-12">
			<div class="cart-grid-right col-xs-12 col-lg-4">
				<span id="customer_place" class="card"></span>
			</div>
			<div class="cart-grid-right col-xs-12 col-lg-4">
				<span id="carrier_place" class="card"></span>
				<br>
				<span id="payment_place" class="card"></span>
			</div>
			<div class="cart-grid-right col-xs-12 col-lg-4">
				<span id="checkout_place" class="card"></span>
			</div>
		</div>
	</div>
	{/if}

	<div class="loading" id="pleaseWaitDialog"></div>
		
{include 'module:ecm_checkout/views/templates/front/trans_js.tpl'}

<!-- /ecm_simplecheckout module -->
{/block}