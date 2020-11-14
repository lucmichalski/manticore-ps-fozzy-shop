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

<!-- ecm_simplecheckout module -->
<div class="block">
	<div id="simplecheckout_content" class="row block_content col-lg-12">
	{if $cart_qties == 0}
		<div class="row">
			<p class="alert alert-warning">{l s='Your shopping cart is empty.' mod='ecm_simcheck'}</p>
		</div>
	{else}
		<div class="row">
			<div id="cart_place" class="col-lg-12 col-sm-12"></div>
			<p class="alert alert-warning unvisible" id="errors_msg"><span id="login_errors"></span></p>
		</div>
		<div class="row" class="col-lg-12 col-sm-12">
			<div id="customer_place" class="col-lg-4 col-sm-5"></div>
			<div id="carrier_place" class="col-lg-4 col-sm-5"></div>
			<div id="payment_place" class="col-lg-4 col-sm-5"></div>
		</div>
	{/if}
	</div>
</div>
<div class="loading" id="pleaseWaitDialog"></div>
		

{addJsDef renderSeq={$renderSeq}}
{addJsDef renderCustomerSeq={$renderCustomerSeq}}
{addJsDef hide_header={$hide_header}}
{addJsDef hide_column_right={$hide_column_right}}
{addJsDef hide_column_left={$hide_column_left}}
{addJsDefL name='fill_error'}{l s='Need fill' mod='ecm_simcheck' js=1}{/addJsDefL}
{addJsDefL name='payment_select_error'}{l s='Please select payment method' mod='ecm_simcheck' js=1}{/addJsDefL}

<script>
var cart_qties = {$cart_qties};
var cart = {$cart|@json_encode};
var strict_auth = {(int)Configuration::get('ecm_simcheck_strict_auth')};
</script>
<!-- /ecm_simplecheckout module -->
