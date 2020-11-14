{*
 * 2016 Terranet
 *
 * NOTICE OF LICENSE
 *
 * @author    Terranet
 * @copyright 2016 Terranet
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*}

{extends file="helpers/list/list_content.tpl"}

{block name="td_content"}
    {if isset($params.type) && $params.type == 'taxonomy_text'}
        {if is_array($tr.$key)}
            {foreach from=$tr.$key item=itm}
                <b>{$itm.iso|escape:'html':'UTF-8'}</b>
                : {$itm.item|escape:'html':'UTF-8'}
                <br/>
            {/foreach}
        {else}
            {$tr.$key|escape:'html':'UTF-8'}
        {/if}
    {elseif isset($params.type) && $params.type =='taxonomy_exist'}
        {if (bool)$tr.$key == true}
            <span class="label color_field"
                  style="background-color:#CCCC99;color:#383838;min-width: 120px; display: inline-block">
                {l s='Available' mod='gshoppingfeed'}
            </span>
        {else}
            <span class="label color_field"
                  style="background-color:red;color:white;min-width: 120px; display: inline-block">
                {l s='No exist' mod='gshoppingfeed'}
            </span>
        {/if}
    {elseif (isset($params.type) && $params.type == 'taxonomy_lists')}
        <span class="btn btn-default change_taxonomy">
            {l s='Edit' mod='gshoppingfeed'}
        </span>
        <span class="btn btn-default change_taxonomy_save hidden">
            {l s='Save' mod='gshoppingfeed'}
        </span>
    {else}
        {$smarty.block.parent}
    {/if}
{/block}

{block name="open_td"}
    {if isset($params.type) && $params.type == 'taxonomy_lists'}
    <td class="pointer text-right">
        <input type="hidden" value="{$tr.$identifier|intval}" name="ind" class="ind">
    {elseif isset($params.type) && $params.type == 'fast_generation'}
    <td class="pointer center">
        <a class="btn btn-primary" href="{$current_index|escape:'html':'UTF-8'}&amp;{$identifier|escape:'html':'UTF-8'}={$tr.$identifier|escape:'html':'UTF-8'}{if $view}&amp;view{else}&amp;update{/if}{$table|escape:'html':'UTF-8'}{if $page > 1}&amp;page={$page|intval}{/if}&amp;token={$token|escape:'html':'UTF-8'}&amp;generationlink=1">
            {l s='Show link' mod='gshoppingfeed'}
        </a>
    {else}
        {$smarty.block.parent}
    {/if}
{/block}

