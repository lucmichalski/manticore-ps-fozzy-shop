{**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
{extends file='page.tpl'}

{block name='page_title'}
  {l s='Our stores' d='Shop.Theme.Global'}
{/block}
  
{block name='page_content_container'}
  <section id="content" class="page-content page-stores">
    <div id="ShopsBefore">
    {hook h='displayShopsBefore'}
    </div>
    {foreach $stores as $store}
      <article id="store-{$store.id}" class="store-item">
        <div class="store-item-container clearfix">
          <div class="col-md-3 store-picture hidden-sm-down">
            <img src="{$store.image.bySize.stores_default.url}" alt="{$store.image.legend}" title="{$store.image.legend}">
          </div>
          <div class="col-md-5 col-sm-7 col-xs-12 store-description">
            <p class="h3 card-title">{$store.name}</p>
            <address>{$store.address.address1 nofilter}</address>
          </div>
          <div class="col-md-4 col-sm-5 col-xs-12 divide-left">
            <table>                              
              <tr>
                <th>{l s='График работы' d='Shop.Theme.Global'}:</th>
                <td>
                  <ul>
                    <li>{$store.business_hours.0.hours.0}</li>
                  </ul>
                </td>
              </tr>
              <tr>
                <th><i class="fa fa-phone" aria-hidden="true"></i></th>
                <td>
                  <ul>
                    <li><a href="tel:{$store.phone}">{$store.phone}</a></li>
                    <li><a href="tel:{$store.fax}">{$store.fax}</a></li>
                  </ul>
                </td>
              </tr>
            </table>
          </div>
        </div>
      </article>
    {/foreach}
  {hook h='displayShops'}
  </section>
{/block}
