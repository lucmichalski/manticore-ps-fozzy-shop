{*
* 2007-2014 PrestaShop
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
*  @copyright 2007-2014 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div id="novepopup">
<div class="novepopup-close">
<div class="nove-close-checkbox">
<div><input type="checkbox" class="checkbox" name="novepopup-checkbox" id="novepopup-checkbox" /></div>  <label for="novepopup-checkbox">{l s='Do not show again.' mod='novepopup'}</label></div>
<div class="nove-close-popup"><span class="cross" title="{l s='Close window' mod='novepopup'}"></span></div>
</div>


<div class="novepopup-content">{$txt|stripslashes}</div>
{if $newsletter}
<div class="novepopup-newsletter-form">
	<div class="row">
		<div class="col-xs-6">
		<span class="promo-text">{l s='Sign up to receive latest news and updates direct to your inbox' mod='novepopup'}</span>
	</div>
	<div class="col-xs-6">
	<form action="{$link->getPageLink('index', null, null, null, false, null, true)|escape:'html':'UTF-8'}" method="post" class="form-inline">
			<div >
				<input class="inputNew form-control grey newsletter-input" type="text" name="email" size="18" placeholder="{l s='Enter your e-mail' mod='novepopup'}" value="" />
                <button type="submit" name="submitNewsletter" class="btn btn-default button button-medium nove-btn-newsletter">
                    <span>{l s='Subscribe' mod='novepopup'}</span>
                </button>
				<input type="hidden" name="action" value="0" />
			</div>
		</form>
	</div>		</div></div>
	{/if}
</div> <!-- #layer_cart -->
<div id="novepopup-overlay"></div>
