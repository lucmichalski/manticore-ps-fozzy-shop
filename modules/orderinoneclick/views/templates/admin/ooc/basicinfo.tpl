{*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @author    Yuri Denisov <contact@splashmart.ru>
*  @copyright 2014-2017 Yuri Denisov
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<script type="text/javascript">
    var find_url = '{$link->getAdminLink('AdminOOC')|escape:'javascript':'UTF-8'}';
    var id_sm_ooc_order = {$ooc_order->id_sm_ooc_order|escape:'html':'UTF-8'};
</script>

<div class="tab-pane tab-content">
    <div id="tab-pane-BasicInfo" class="tab-pane">
	<div class="panel">
            <h3 class="tab">{l s='Order details' mod='orderinoneclick'}</h3>
		<div class="form-group">
		    <div class="ooc_info">
			<table>
			    <thead>
				<tr>
				    <td class="order_info">{l s='Order #' mod='orderinoneclick'}{$ooc_order->id_sm_ooc_order|escape:'html':'UTF-8'}</td>
				    <td class="datetime">{$ooc_order->date|escape:'html':'UTF-8'}</td>
				    <td class="customer">
					{if $ooc_order->id_customer != 0}
					    {l s='Customer' mod='orderinoneclick'}: 
					    {if isset($ooc_customer)}
						{if ($ooc_customer != null)}
						    {$ooc_customer->firstname|escape:'html':'UTF-8'} {$ooc_customer->lastname|escape:'html':'UTF-8'} ({$ooc_customer->email|escape:'html':'UTF-8'})
						{/if}
					    {/if}
					{else}
					    {l s='Guest' mod='orderinoneclick'}: {$ooc_order->id_guest|escape:'html':'UTF-8'}
					{/if}
				    </td>
				    <td>{l s='User fields' mod='orderinoneclick'}</td>
				</tr>
			    </thead>
			    <tbody>
				<tr>
				    <td colspan="3">
					<div>{l s='You can' mod='orderinoneclick'} <a href="#" class="assign_c"> {l s='ASSIGN' mod='orderinoneclick'}</a> {l s='order to exist customer' mod='orderinoneclick'}</div>
					<div>{l s='or' mod='orderinoneclick'}</div>
					<a href="#" id="create_c">{l s='CREATE' mod='orderinoneclick'}</a> {l s='a new customer' mod='orderinoneclick'}</div>
				    </td>
				    <td colspan="2">
					{if !empty($ooc_order->ooc_fields)}
					    <table>
						<thead>
						    <tr class="white">
							<td class="field_name">
							    {l s='Field' mod='orderinoneclick'}
							</td>
							<td class="field_value">
							    {l s='Value' mod='orderinoneclick'}
							</td>
						    </tr>
						</thead>
						<tbody>
						{foreach from=$ooc_order->ooc_fields key=key item=field}
						    <tr class="whitesmoke">
							{if isset($field->name)}
							    <td class="field_name">
								{$field->name.$id_lang|escape:'html':'UTF-8'}: 
							    </td>
							    <td class="field_value">
								{$field->value|escape:'html':'UTF-8'}
							    </td>
							{/if}
						    </tr>
						{/foreach}
						</tbody>
					    </table>
					{else}
					    <div>
						{l s='Order don\'t have additional fields' mod='orderinoneclick'}
					    </div>
					{/if}
				    </td>
				</tr>
			    </tbody>
			</table>
		    </div>
		    {if $ooc_order->ooc_cart->products != ''}
			<div class="ooc_table_products">
			    <table class="products_table">
				<thead>
				    <tr>
					<td class="product_id">{l s='ID' mod='orderinoneclick'}</td>
					<td class="product">{l s='Product' mod='orderinoneclick'}</td>
					<td class="product_description">{l s='Description' mod='orderinoneclick'}</td>
					<td class="product_reference">{l s='Reference' mod='orderinoneclick'}</td>
					<td class="product_availability">{l s='Available' mod='orderinoneclick'}</td>
					<td class="product_unit_price">{l s='Unit price' mod='orderinoneclick'}</td>
					<td class="product_qty">{l s='Qty' mod='orderinoneclick'}</td>
					<td class="product_total">{l s='Total' mod='orderinoneclick'}</td>
				    </tr>
				</thead>
				<tbody>
				    {foreach  from=$ooc_order->ooc_cart->products item=product}
					<tr>
					    <td class="product_id">
						{$product->product_object->id|escape:'html':'UTF-8'}
					    </td>
					    <td class="product">
						<a href="{$link->getAdminLink('AdminProducts')|escape:'htmlall':'UTF-8'}&updateproduct&id_product={$product->product_object->id|escape:'html':'UTF-8'}" target="_blank">
						    <img class="product_image" src="{$link->getImageLink($product->product_object->link_rewrite.$id_lang, $product->image.id_image, 'cart_default')|escape:'html':'UTF-8'}">
						</a>
					    </td>
					    <td class="product_description" valign="top">
						<p>
						    <strong>{$product->product_object->name.$id_lang|escape:'html':'UTF-8'}</strong>
						</p>
						{if ($product->combination)}
						    <p>
							<div><strong>{l s='Combination:' mod='orderinoneclick'}</strong></div>
							{assign var=attributes value=$product->combination->getAttributesName($id_lang)}
							{foreach from=$attributes key=key item=attribute}
							    <div>{$attribute.name|escape:'html':'UTF-8'}</div>
							{/foreach}
						    </p>
						{/if}
						{if ($product->customization)}
						    <p>
							<div><strong>{l s='Customization:' mod='orderinoneclick'}</strong></div>
							{foreach from=$product->customization key=key item=customization}
							    {if $customization->customization_type == 0}
								<div>
								    <a href="{$sm_upload_dir|escape:'htmlall':'UTF-8'}{$customization->customization_value|escape:'htmlall':'UTF-8'}" target="_blank">
									<img src="{$sm_upload_dir|escape:'htmlall':'UTF-8'}{$customization->customization_value|escape:'htmlall':'UTF-8'}_small" alt=""/>
								    </a>
								</div>
							    {/if}
							    {if $customization->customization_type == 1}
								<div>{$customization->customization_value|escape:'html':'UTF-8'}</div>
							    {/if}
							{/foreach}
						    <p>
						{/if}
					    </td>
					    <td class="product_reference">
						{if $product->combination}
						    {$product->combination->reference|escape:'html':'UTF-8'}
						{else}
						    {$product->product_object->reference|escape:'html':'UTF-8'}
						{/if}
					    </td>
					    <td class="product_availability">{$product->available|escape:'html':'UTF-8'}</td>
					    <td class="product_unit_price">
						{displayPrice price=$product->price currency=$ooc_order->ooc_cart->id_currency|escape:'html':'UTF-8'}
					    </td>
					    <td class="product_qty">{$product->quantity|escape:'html':'UTF-8'}</td>
					    <td class="product_total">
						{displayPrice price=$product->price*$product->quantity currency=$ooc_order->ooc_cart->id_currency|escape:'html':'UTF-8'}
					    </td>
					</tr>
				    {/foreach}
				</tbody>
				<tfoot>
				    {if $ooc_order->ooc_cart->total_discount > 0}
					<tr>
					    <td colspan="5" rowspan="4"></td>
					    <td colspan="2" class="total-d">{l s='Without discount' mod='orderinoneclick'}</td>
					    <td colspan="1">
						{displayPrice price=($ooc_order->ooc_cart->order_price + $ooc_order->ooc_cart->total_discount) currency=$ooc_order->ooc_cart->id_currency|escape:'html':'UTF-8'}
					    </td>
					</tr>
					<tr>
					    <td colspan="2" class="total-d">
						{l s='Discount' mod='orderinoneclick'}
						<a href="#" id="show_d">({l s='Vouchers' mod='orderinoneclick'})</a>
					    </td>
					    <td colspan="1">
						{displayPrice price=$ooc_order->ooc_cart->total_discount currency=$ooc_order->ooc_cart->id_currency|escape:'html':'UTF-8'}
					    </td>
					</tr>
				    {/if}
				    <tr>
					{if $ooc_order->ooc_cart->total_discount == 0}
					    <td colspan="5"></td>
					{/if}
					<td colspan="2" class="total">{l s='TOTAL' mod='orderinoneclick'}</td>
					<td class="total-price">
					    {displayPrice price=$ooc_order->ooc_cart->order_price currency=$ooc_order->ooc_cart->id_currency|escape:'html':'UTF-8'}
					</td>
				    </tr>
				</tfoot>
			    </table>
			</div>
		    {/if}
		</div>

		<div class="panel-footer">
		    <a href="{$link->getAdminLink('AdminOOC')|escape:'htmlall':'UTF-8'}" class="btn btn-default"><i class="process-icon-back"></i> {l s='Back' mod='orderinoneclick'}</a>
		</div>
	</div>
    </div>
</div>

<div id="assign_to_customer" class="modal fade">
    <div class="modal-content">
	<div class="assign_wait"></div>
	<div class="modal-header">
	    {l s='Assign order to existing customer' mod='orderinoneclick'}
	    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	</div>
	<div class="modal-body">
	    <div id="ooc_customer_search">
		<div class="input-group">
		    <input id="customer_autocomplete_input" class="ac_input" type="text" name="customer_autocomplete_input" {*if $ooc_order->id_customer == 0}placeholder="{l s='Enter customer name...' mod='orderinoneclick'}"{else}value="sld3333kfjlskdjflskdjf"{/if*}>
		    <input id="customer_autocomplete_hinput" type="hidden" name="customer_autocomplete_hinput">
		    <span class="input-group-addon"><i class="icon-search"></i></span>
		</div>
		<a id="act_assign" href="#">{l s='Assign' mod='orderinoneclick'}</a>
	    </div>
	</div>
    </div>
</div>

<div id="create_customer" class="modal fade">
    {assign var=customer_email value=$ooc_order->getCustomerEmails()}
    {assign var=customer_name value=$ooc_order->getCustomerName()}
    <div class="modal-content">
	<div class="assign_wait"></div>
	<div class="modal-header">
	    {l s='Create a new customer and assign it to order' mod='orderinoneclick'}
	    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	</div>
	<div class="modal-body">
	    <table style="width:100%">
		<body>
		    <tr>
			<td style="padding: 10px;">
			    {l s='Firstname' mod='orderinoneclick'}
			</td>
			<td>
			    <input id="new_customer_firstname" name="new_customer_firstname" class="create_cust_input" {if $customer_name}value="{$customer_name|escape:'html':'UTF-8'}"{/if}>
			</td>
		    </tr>
		    <tr>
			<td style="padding: 10px;">
			    {l s='Lastname' mod='orderinoneclick'}
			</td>
			<td>
			    <input id="new_customer_lastname" name="new_customer_lastname" class="create_cust_input">
			</td>
		    </tr>
		    <tr>
			<td style="padding: 10px;">
			    {l s='Email' mod='orderinoneclick'}
			</td>
			<td>
			    <input id="new_customer_email" name="new_customer_email" class="create_cust_input" {if $customer_email}value="{$customer_email.0|escape:'html':'UTF-8'}"{/if}>
			</td>
		    </tr>
		    <tr>
			<td style="padding: 10px;">
			    {l s='Password' mod='orderinoneclick'}
			</td>
			<td>
			    <input id="new_customer_password" name="new_customer_password" class="create_cust_input">
			</td>
		    </tr>
		</body>
	    </table>
	    <p style="text-align: center; margin-top: 10px">
		<a id="act_create" href="#">{l s='Create' mod='orderinoneclick'}</a>
	    </p>
	</div>
    </div>
</div>

{if $ooc_order->ooc_cart->total_discount > 0}
    <div id="show_discounts" class="modal fade">
	<div class="modal-content">
	    <div class="modal-header">
		{l s='Used vouchers' mod='orderinoneclick'}
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	    </div>
	    <div class="modal-body">
		{foreach  from=$ooc_order->ooc_cart->vouchers item=voucher}
		    <p>
			<div>
			    {$voucher->object->name.$id_lang|escape:'htmlall':'UTF-8'} : {$voucher->object->code|escape:'htmlall':'UTF-8'}
			</div>
		    </p>
		{/foreach}
	    </div>
	</div>
    </div>
{/if}
