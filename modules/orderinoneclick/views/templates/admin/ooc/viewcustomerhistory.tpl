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
*  @copyright 2014-2016 Yuri Denisov
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div class="tab-pane tab-content">
    <div id="tab-pane-ViewCustomerHistory" class="tab-pane">
        {if isset($ooc_orders) && !empty($ooc_orders)}
	<div class="panel">
            <div class="panel-heading">{l s='History for customer' mod='orderinoneclick'}: {if isset($ooc_customer)}{$ooc_customer->firstname|escape:'html':'UTF-8'} {$ooc_customer->lastname|escape:'html':'UTF-8'} ({$ooc_customer->email|escape:'html':'UTF-8'}){/if}</div>
            <div class="form-group">
                <div class="table-responsive-row clearfix">
                    <table class="table sm_ooc_order">
                        <thead>
                            <tr class="nodrag nodrop">
                                <th class="center fixed-width-xs">{l s='ID Order' mod='orderinoneclick'}</th>
                                <th class="center">{l s='Customer' mod='orderinoneclick'}</th>
                                <th class="center">{l s='Date' mod='orderinoneclick'}</th>
                                <th class="center">{l s='Price' mod='orderinoneclick'}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach $ooc_orders as $order}
                                <tr>
                                    <td class="center">{$order->id|escape:'html':'UTF-8'}</td>
                                    <td class="center">{$order->getCustomerName()|escape:'html':'UTF-8'}</td>
                                    <td class="center">{$order->date|escape:'html':'UTF-8'}</td>
                                    <td class="center">{displayPrice price=$order->ooc_cart->order_price currency=$order->ooc_cart->id_currency|escape:'html':'UTF-8'}</td>
                                    <td class="text-right">
                                        <div class="btn-group pull-right">
                                            <a href="{$ooc_order_view_action|escape:'html':'UTF-8'}&id_sm_ooc_order={$order->id|escape:'html':'UTF-8'}" class="btn btn-default" title="{l s='View' mod='orderinoneclick'}" target="_blank">
                                                <i class="icon-search-plus"></i>{l s='View' mod='orderinoneclick'}
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            {/foreach}
                        </tbody>
                    </table>
                </div>
            </div>
	</div>
        {/if}
        {if isset($ooc_maybe_orders) && !empty($ooc_maybe_orders)}
	<div class="panel">
            <div class="panel-heading">{l s='Other orders with email' mod='orderinoneclick'}: {$emails|escape:'html':'UTF-8'}</div>
            <div class="form-group">
                <div class="table-responsive-row clearfix">
                    <table class="table sm_ooc_order">
                        <thead>
                            <tr class="nodrag nodrop">
                                <th class="center fixed-width-xs">{l s='ID Order' mod='orderinoneclick'}</th>
                                <th class="center">{l s='ID Guest' mod='orderinoneclick'}</th>
                                <th class="center">{l s='Customer' mod='orderinoneclick'}</th>
                                <th class="center">{l s='Date' mod='orderinoneclick'}</th>
                                <th class="center">{l s='Price' mod='orderinoneclick'}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach $ooc_maybe_orders as $order}
                                <tr>
                                    <td class="center">{$order->id|escape:'html':'UTF-8'}</td>
                                    <td class="center">{$order->id_guest|escape:'html':'UTF-8'}</td>
                                    <td class="center">{$order->getCustomerName()|escape:'html':'UTF-8'}</td>
                                    <td class="center">{$order->date|escape:'html':'UTF-8'}</td>
                                    <td class="center">{displayPrice price=$order->ooc_cart->order_price currency=$order->ooc_cart->id_currency|escape:'html':'UTF-8'}</td>
                                    <td class="text-right">
                                        <div class="btn-group pull-right">
                                            <a href="{$ooc_order_view_action|escape:'html':'UTF-8'}&id_sm_ooc_order={$order->id|escape:'html':'UTF-8'}" class="btn btn-default" title="{l s='View' mod='orderinoneclick'}" target="_blank">
                                                <i class="icon-search-plus"></i>{l s='View' mod='orderinoneclick'}
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            {/foreach}
                        </tbody>
                    </table>
                </div>
            </div>
	</div>
        {/if}
        {if empty($ooc_maybe_orders) && empty($ooc_orders)}
            <div class="panel">
                <div class="panel-heading">{l s='Customer history' mod='orderinoneclick'}</div>
                <div class="form-group">
                    {l s='No history for this customer / emails' mod='orderinoneclick'}
                </div>
            </div>
        {/if}
    </div>
</div>
