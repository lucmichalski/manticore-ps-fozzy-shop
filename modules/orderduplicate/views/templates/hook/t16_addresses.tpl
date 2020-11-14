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
<div class="row">
    <div class="col-md-6">
	<h4>{l s='Invoice Address' mod='orderduplicate'}</h4>
	<ul id="address_list_invoice" class="list-unstyled list-invoice">
	    {foreach from=$addresses item=address key=id_address name=i}
	    <li rel="{$id_address|escape:'htmlall':'UTF-8'}" class="address_item{if $id_address_invoice_selected}{if $id_address_invoice_selected == $id_address} selected{/if}{else if $smarty.foreach.i.index == 0} selected{/if}">{$address|escape:'UTF-8'}</li>
	    {/foreach}
	</ul>
    </div>
    
    <div class="col-md-6">
	<h4>{l s='Shipping Address' mod='orderduplicate'}</h4>
	<ul id="address_list_delivery" class="list-unstyled list-delivery">
	    {foreach from=$addresses item=address key=id_address name=i}
	    <li rel="{$id_address|escape:'htmlall':'UTF-8'}" class="address_item{if $id_address_delivery_selected}{if $id_address_delivery_selected == $id_address} selected{/if}{else if $smarty.foreach.i.index == 0} selected{/if}">{$address|escape:'UTF-8'}</li>
	    {/foreach}
	</ul>
    </div>
</div>