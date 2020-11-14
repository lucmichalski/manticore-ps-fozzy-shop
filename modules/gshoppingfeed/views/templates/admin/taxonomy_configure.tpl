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
    {if isset($forbulk) && $forbulk == 1}
        <div class="control-label col-md-3">
            {l s='One Google category for all ours:' mod='gshoppingfeed'}
        </div>
        <div class="col-md-6 bulk_taxonomy_update_list">
            {if isset($taxonomyLists) && is_array($taxonomyLists) && count($taxonomyLists)}
                <select class="chosen" name="update_all_taxonomy_item" id="update_all_taxonomy_item">
                    {foreach from=$taxonomyLists item=taxonomy}
                        {if !isset($taxonomy['key']) || !isset($taxonomy['name'])}
                            {continue}
                        {/if}
                        <option value="{$taxonomy['key']|escape:'htmlall':'UTF-8'}___{$taxonomy['name']|escape:'htmlall':'UTF-8'}">
                            {$taxonomy['name']|escape:'htmlall':'UTF-8'}
                        </option>
                    {/foreach}
                </select>
            {/if}
        </div>
        <div class="col-md-3">
            <button name="update_all_taxonomy_list" value="1" class="btn btn-default">
                {l s='Update' mod='gshoppingfeed'}
            </button>

            <button name="taxonomy_trunctable" value="1" class="btn btn-default">
                {l s='Clear all' mod='gshoppingfeed'}
            </button>
        </div>
    {else}
        <select class="chosen taxonomy_option_list" name="">
        {foreach from=$taxonomyLists item=list}
            <option {if (isset($taxonomySelected) && isset($taxonomySelected.id_taxonomy) && $taxonomySelected.id_taxonomy==$list.key|intval)}selected="selected"{/if} value="{$list.key|escape:'htmlall':'UTF-8'}">
                {$list.name|escape:'htmlall':'UTF-8'}
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