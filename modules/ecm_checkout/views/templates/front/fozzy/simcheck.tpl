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
	<div id="ecm_checkout" class="row col-lg-12 col-xs-12">	
		<div class="row col-lg-12 col-sm-12 float-left">
      <span id="cart_place">{$cart_place nofilter}</span>
    </div>
    <div class="row col-lg-12 col-sm-12 float-left">
			<div id="carriers">
				<span id="carrier_place" class="card">{$carrier_place nofilter}</span>
			</div>
      <div id="customers">
				<p class="alert alert-warning" id="errors_msg" hidden><span id="login_errors"></span></p>
				<span id="customer_place" class="card">{$customer_place nofilter}</span>
				
			</div>
		</div>
		<div  id="checkouts" class="row col-lg-12 col-sm-12">
			<span id="payment_place" class="card col col-xs-12 col-lg-7 float-left">{$payment_place nofilter}</span>
			<span id="checkout_place" class="card col col-xs-12 col-lg-5 float-left">{$checkout_place nofilter}</span>
		</div>
	</div>	
	{/if}

	<div class="loading" id="pleaseWaitDialog"></div>
		
{include 'module:ecm_checkout/views/templates/front/trans_js.tpl'}

<!-- /ecm_simplecheckout module -->
{/block}