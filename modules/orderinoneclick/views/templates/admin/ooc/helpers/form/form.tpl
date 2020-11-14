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

{extends file="helpers/form/form.tpl"}

{block name="defaultForm"}
    <div class="row">
        <div class="productTabs col-lg-2 col-md-3">
            <div class="list-group">
		{foreach $tabs key=id item=tab}
		    <a data-toggle="tab" class="list-group-item {if $tab.selected eq 1}active{/if}" id="link-{$tab.id|escape:'htmlall':'UTF-8'}" href="#tab-pane-{$tab.id|escape:'htmlall':'UTF-8'}">{$tab.name|escape:'htmlall':'UTF-8'}</a>
		{/foreach}
            </div>
        </div>
        <form id="sm_ooc_form" class="form-horizontal col-lg-10 col-md-9" action="{$form_action|escape:'html':'UTF-8'}" method="post" enctype="multipart/form-data" name="sm_ooc" novalidate>
            {foreach $tabs key=id item=tab}
                {$tab.form_content_html}
            {/foreach}
        </form>
    </div>	
{/block}

{block name="script"}
    {$smarty.block.parent}
    $('.list-group a.active').trigger('click');
    $('.list-group a').on('click', function(){
	$('.list-group a').removeClass('active');
	$('.tab-pane').removeClass('active');
	$(this).addClass('active');
    });
{/block}
