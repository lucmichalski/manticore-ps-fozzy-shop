{*
* 2007-2017 PrestaShop
*
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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2017 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{$p_max = ceil($total/$npp)}
{$prev = $p - 1}
{$next = $p + 1}

<div class="list-pagination">
	<div class="npp-holder pull-left">
		<select class="npp">
			<option value="3"{if $npp == 3} selected{/if}>3</option>
			<option value="10"{if $npp == 10} selected{/if}>10</option>
			<option value="20"{if $npp == 20} selected{/if}>20</option>
			<option value="50"{if $npp == 50} selected{/if}>50</option>
			<option value="100"{if $npp == 100} selected{/if}>100</option>
			<option value="100000"{if $npp == '100000'} selected{/if}>{l s='All' mod='metatagsgenerator'}</option>
		</select> {l s='of' mod='metatagsgenerator'} {$total|intval}
	</div>
	{if $p_max > 1}	
	<div class="pages-holder pull-right">
		<a href="#" class="go-to-page" data-page="{if $prev}{$prev|intval}{else}1{/if}"><i class="icon-angle-left"></i></a>
		{if $prev}
			<a href="#" class="go-to-page first" data-page="1">1</a>
			{if $prev > 1}
				{if $prev > 2}...{/if}
				<a href="#" class="go-to-page" data-page="{$prev|intval}">{$prev|intval}</a>
			{/if}
		{/if}
		<span href="#" class="current-page" data-page="{$p|intval}">{$p|intval}</span>
		{if $next <= $p_max}
			{if $next < $p_max}
				<a href="#" class="go-to-page" data-page="{$next|intval}">{$next|intval}</a>
				{if $next < $p_max - 1}...{/if}
			{/if}
			<a href="#" class="go-to-page last" data-page="{$p_max|intval}">{$p_max|intval}</a>
		{else}
			{$next = $p_max}
		{/if}
		<a href="#" class="go-to-page" data-page="{$next|intval}"><i class="icon-angle-right"></i></a>
	</div>
	{/if}
</div>
