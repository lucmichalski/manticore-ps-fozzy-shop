{*
 * 2016 Terranet
 *
 * NOTICE OF LICENSE
 *
 * @author    Terranet
 * @copyright 2016 Terranet
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*}

{if isset($taxonomyLists) && count($taxonomyLists) > 0}
    {if isset($taxonomyLists) && is_array($taxonomyLists) && count($taxonomyLists)}
        <select class="chosen" name="update_all_taxonomy_item" id="update_all_taxonomy_item">
            {foreach from=$taxonomyLists item=taxonomy}
                <option value="{$taxonomy['key']|escape:'htmlall':'UTF-8'}___{$taxonomy['name']|escape:'htmlall':'UTF-8'}">
                    {$taxonomy['name']|escape:'htmlall':'UTF-8'}
                </option>
            {/foreach}
        </select>
    {/if}
{else}
    <span class="label color_field" style="background-color:red;color:white;min-width: 120px; display: inline-block">
        <p class="help-block">
            {l s='No exist' mod='gshoppingfeed'}
        </p>
    </span>
{/if}