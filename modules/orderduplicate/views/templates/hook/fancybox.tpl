{**
* OrderDuplicate
*
* @category  Module
* @author    silbersaiten <info@silbersaiten.de>
* @support   silbersaiten <support@silbersaiten.de>
* @copyright 2015 silbersaiten
* @version   1.0.0
* @link      http://www.silbersaiten.de
* @license   See joined file licence.txt
*}
<div id="content" class="bootstrap" style="margin: 0; padding: 14px;">
    {if ! $order}
	<p class="alert alert-danger">{l s='The order you\'re trying to clone does not exist' mod='orderduplicate'}
		{if ! $prod}
		<br>{l s='The articles in the order are not available' mod='orderduplicate'}
		{/if}
	</p>
    {else}
  <input type="hidden" name="id_address_delivery" value="{$address_delivery->id}" />
	<input type="hidden" name="id_address_invoice" value="{$address_delivery->id}" />
	<input type="hidden" name="id_order" value="{$order->id}" />
  
	
	<div class="container">
  
	    <div class="row">
		<div class="panel">
		    <div class="panel-heading">{l s='Order #' mod='orderduplicate'} {$order->id|escape:'html':'UTF-8'}</div>
		    <div class="panel-body">
			<div class="row">
			    <div class="col-md-4">
					<div class="panel">
					    <div class="panel-heading">{l s='Клиент' mod='orderduplicate'}</div>
					    <div class="panel-body">
							<div class="form-group">
							    <input type="text" name="customer_select" id="customer_select" value="{$customer->firstname|escape:'html':'UTF-8'} {$customer->lastname|escape:'html':'UTF-8'}" />
							    <input type="hidden" name="id_customer" value="{$customer->id}" />
							</div>
					    </div>
					</div>
					<div class="panel">
					    <div class="panel-heading">{l s='Тип оплаты' mod='orderduplicate'}</div>
					    <div class="panel-body">
							<div class="form-group">
							    <select name="payment_method">
								{foreach from=$methods item=method}
								    {if $selected_method == $method.id_module}
                    <option value="{$method.id_module|escape:'html':'UTF-8'}" {if $selected_method == $method.id_module}selected="selected"{/if}>{$method.displayName|escape:'html':'UTF-8'}</option>
								    {/if}
                {/foreach}
							    </select>
							</div>
					    </div>
					</div>
					
			    </div>
          <div class="col-md-4">
          <div class="panel">
					    <div class="panel-heading">{l s='Тип заказа' mod='orderduplicate'}</div>
					    <div class="panel-body">
							<div class="form-group">
							    <select name="order_type">
                    <option value="Возврат">{l s='Возврат' mod='orderduplicate'}</option>
                    <option value="Довоз">{l s='Довоз' mod='orderduplicate'}</option>
                 <!--   <option value="Заказ">{l s='Заказ' mod='orderduplicate'}</option>  -->
							    </select>
							</div>
					    </div>
					</div>
          <div class="panel">
					    <div class="panel-heading">{l s='Статус' mod='orderduplicate'}</div>
					    <div class="panel-body">
							<div class="form-group">
							    <select name="order_state">
								{foreach from=$states item=state}
                {if $state.id_order_state == 939}
								    <option value="{$state.id_order_state|escape:'html':'UTF-8'}">{$state.name|escape:'html':'UTF-8'}</option>
								{/if}
                {/foreach}
							    </select>
							</div>
					    </div>
					</div>
          </div>
			    <div class="col-md-4">
          <div class="panel">
					    <div class="panel-heading">{l s='Товары' mod='orderduplicate'}</div>
					    <div class="panel-body">
							<div class="form-group">
                  <select name="products" class="fixed-width-xxl fixed-width-xl" id="products" multiple="multiple">
								{foreach from=$products item=product}
								    <option value="{$product.id_order_detail}">{$product.product_name}</option>
                {/foreach}
							    </select>
							</div>
					    </div>
					</div>
					<div class="panel">
					    <div class="panel-heading">{l s='Адрес' mod='orderduplicate'}</div>
					    <div class="panel-body">
							<div id="addresses_container">
              <div class="row">
            	<ul id="address_list_delivery" class="list-unstyled list-delivery">
            	    <li rel="{$id_address|escape:'htmlall':'UTF-8'}" class="address_item selected">{$address nofilter}</li>
            	</ul>
              </div>
</div>
              
              
              
              </div>
					    </div>  
					</div>
			    </div>
			</div>
		    </div>
		</div>
	    </div>
	    <div class="row">
		<a href="#" class="btn btn-primary" id="cloneOrder">{l s='Создать' mod='orderduplicate'}</a>
	    </div>
	</div>
    {/if}
</div>

