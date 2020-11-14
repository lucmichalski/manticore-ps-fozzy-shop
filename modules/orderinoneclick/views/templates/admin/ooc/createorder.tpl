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
    <div id="tab-pane-CreateOrder" class="tab-pane">
	<div class="panel">
            <h3 class="tab">{l s='Order details' mod='orderinoneclick'}</h3>
		<div class="form-group">
		    {if $ooc_order->id_customer == 0}
			{l s='You must' mod='orderinoneclick'} <a href="#" class="assign_c"> {l s='ASSIGN' mod='orderinoneclick'}</a> {l s='quick order to exist customer before create classic order' mod='orderinoneclick'}
		    {else}
			<a href="{$link->getAdminLink('AdminOOC')|escape:'htmlall':'UTF-8'}&createrealorder=1&updatesm_ooc_order=1&id_sm_ooc_order={$ooc_order->id_sm_ooc_order|escape:'htmlall':'UTF-8'}">{l s='Create order' mod='orderinoneclick'}</a>
			<p>
			    <div>{l s='You will redirect to "Add order" page...' mod='orderinoneclick'}</div>
			    <div>{l s='All posible fields will be filled.' mod='orderinoneclick'}</div>
			    <div>{l s='* Currently, do not support adding a customization fields.' mod='orderinoneclick'}</div>
			</p>
		    {/if}
		</div>
	</div>
    </div>
</div>
