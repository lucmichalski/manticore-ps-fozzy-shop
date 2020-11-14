{**
 * 2007-2019 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
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
 * @copyright 2007-2019 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}

{extends file="helpers/list/list_footer.tpl"}

{block name="endForm"}
    {if ($list_id == 'gshoppingfeed_taxonomy')}
        <div class="panel col-lg-12">
            <div class="panel-heading">
                {l s='Bulk updates:' mod='gshoppingfeed'}
            </div>
            <div class="row">
                <div class="col-md-12 bulk-taxonomy-upd-container">
                    <button class="load-bulk-taxonomy-js">{l s='View Google categories list' mod='gshoppingfeed'}</button>
                </div>
            </div>
        </div>
    {/if}
    </form>
{/block}
