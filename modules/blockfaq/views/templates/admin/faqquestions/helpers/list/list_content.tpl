{*
/**
 * StorePrestaModules SPM LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 *
/*
 *
 * @author    StorePrestaModules SPM
 * @category content_management
 * @package blockfaq
 * @copyright Copyright StorePrestaModules SPM
 * @license   StorePrestaModules SPM
 */
*}

{extends file="helpers/list/list_content.tpl"}
{block name="td_content"}
    {if isset($params.type_custom) && $params.type_custom == 'title_category'}
        {if isset($tr[$key])}
            <span class="label-tooltip" data-original-title="{l s='Click here to see faq category on your site' mod='blockfaq'}" data-toggle="tooltip">

                    <a href="
                    {if $params.is_rewrite == 0}
                        {$link->getModuleLink('blockfaq', 'faq', [], true, {$tr.id_lang|escape:'htmlall':'UTF-8'}, {$tr.id_shop|escape:'htmlall':'UTF-8'})|escape:'htmlall':'UTF-8'}#faq_{$tr['id']|escape:'htmlall':'UTF-8'}
                    {else}
                        {$params.base_dir_ssl|escape:'htmlall':'UTF-8'}{$params.iso_code|escape:'htmlall':'UTF-8'}faq#faq_{$tr['id']|escape:'htmlall':'UTF-8'}
                    {/if}
                    "
                       style="text-decoration:underline" target="_blank">
                        {$tr[$key]|escape:'htmlall':'UTF-8'}
                    </a>
                </span>
        {/if}

    {elseif isset($params.type_custom) && $params.type_custom == 'is_active'}

        <span id="activeitem{$tr['id']|escape:'htmlall':'UTF-8'}">
                    <span class="label-tooltip" data-original-title="{l s='Click here to activate or deactivate category on your site' mod='blockfaq'}" data-toggle="tooltip">
                    <a href="javascript:void(0)" onclick="blockfaq_list({$tr['id']|escape:'htmlall':'UTF-8'},'active',{$tr[$key]|escape:'htmlall':'UTF-8'},'questionfaq');" style="text-decoration:none">
                        <img src="../img/admin/../../modules/blockfaq/views/img/{if $tr[$key] == 1}ok.gif{else}no_ok.gif{/if}"  />
                    </a>
                </span>
            </span>

    {elseif isset($params.type_custom) && $params.type_custom == 'is_by_customer'}

                        <img src="../img/admin/../../modules/blockfaq/views/img/{if $tr[$key] == 1}ok.gif{else}no_ok.gif{/if}"  />

    {elseif isset($params.type_custom) && $params.type_custom == 'order_by'}

        {if $index < $tr.count_all - 1 && isset($params.all_items_sort[$index+1])}
            <a class="btn btn-default"
               href="index.php?controller={$params.name_controller|escape:'htmlall':'UTF-8'}&token={$params.token|escape:'htmlall':'UTF-8'}&id={$tr.id|escape:'htmlall':'UTF-8'}&order_self={$tr.order_by|escape:'htmlall':'UTF-8'}&id_change={$params.all_items_sort[$index+1].id|escape:'htmlall':'UTF-8'}&order_change={$params.all_items_sort[$index+1].order_by|escape:'htmlall':'UTF-8'}"
                    >
                <img border="0" src="../img/admin/../../modules/blockfaq/views/img/down.gif" />
            </a>

        {/if}
        {if $index > 0 && isset($params.all_items_sort[$index-1])}
            <a class="btn btn-default" href="index.php?controller={$params.name_controller|escape:'htmlall':'UTF-8'}&token={$params.token|escape:'htmlall':'UTF-8'}&id={$tr.id|escape:'htmlall':'UTF-8'}&order_self={$tr.order_by|escape:'htmlall':'UTF-8'}&id_change={$params.all_items_sort[$index-1].id|escape:'htmlall':'UTF-8'}&order_change={$params.all_items_sort[$index-1].order_by|escape:'htmlall':'UTF-8'}">
                <img border="0" src="../img/admin/../../modules/blockfaq/views/img/up.gif" />
            </a>
        {/if}

    {else}
        {$smarty.block.parent}
    {/if}


{/block}