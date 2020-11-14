{*
 * 2016 Terranet
 *
 * NOTICE OF LICENSE
 *
 * @author    Terranet
 * @copyright 2016 Terranet
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*}


<div class="panel col-lg-12">
    <h3>
        {l s='All feeds URL' mod='gshoppingfeed'}
    </h3>

    <div class="headerPanel pull-right">
        <a class="btn btn-primary pull-right" href="{$backUrl|escape:'htmlall':'UTF-8'}">
            {l s='Back to main panel' mod='gshoppingfeed'}
        </a>
    </div>

    {if isset($lists) && !empty($lists)}
        <ul class="gsh-accordion">
        {foreach from=$lists item='item'}
        <li>
            <strong>
                {$item['name']|escape:'htmlall':'UTF-8'} | {l s='Last Update: ' mod='gshoppingfeed'}{$item['date_update']|escape:'htmlall':'UTF-8'}
            </strong>
            <p>
                <a target="_blank" href="{$item['rebuild_url']|escape:'htmlall':'UTF-8'}">
                    {l s='Rebuild (for cronjob):' mod='gshoppingfeed'}
                </a>
                <br/>
                <strong>{$item['rebuild_url']|escape:'htmlall':'UTF-8'}</strong>
            </p>
            <p>
                <a target="_blank" href="{$item['download_url']|escape:'htmlall':'UTF-8'}">
                    {l s='Download (for Google Merchant Center):' mod='gshoppingfeed'}
                </a> <br/>
                <strong>{$item['download_url']|escape:'htmlall':'UTF-8'}</strong>
            </p>
        </li>
        {/foreach}
        </ul>
    {/if}

</div>

