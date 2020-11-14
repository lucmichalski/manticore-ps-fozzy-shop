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
 * @package blockguestbook
 * @copyright Copyright StorePrestaModules SPM
 * @license   StorePrestaModules SPM
 */
*}

{extends file="helpers/list/list_content.tpl"}
{block name="td_content"}
    {if isset($params.type_custom) && $params.type_custom == 'title_category'}
        {if isset($tr[$key])}
            <span class="label-tooltip" data-original-title="{l s='Click here to see faq category on your site' mod='blockguestbook'}" data-toggle="tooltip">

                    <a href="
                    {if $params.is_rewrite == 0}
                        {$link->getModuleLink('blockguestbook', 'faq', [], true, {$tr.id_lang|escape:'htmlall':'UTF-8'}, {$tr.id_shop|escape:'htmlall':'UTF-8'})|escape:'htmlall':'UTF-8'}#faq_{$tr['id']|escape:'htmlall':'UTF-8'}
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
                    <span class="label-tooltip" data-original-title="{l s='Click here to activate or deactivate testimonial on your site' mod='blockguestbook'}" data-toggle="tooltip">
                    <a href="javascript:void(0)" onclick="blockguestbook_list({$tr['id']|escape:'htmlall':'UTF-8'},'active',{$tr[$key]|escape:'htmlall':'UTF-8'},'testimonial');" style="text-decoration:none">
                        <img src="../modules/blockguestbook/views/img/{if $tr[$key] == 1}ok.gif{else}no_ok.gif{/if}"  />
                    </a>
                </span>
            </span>
    {elseif isset($params.type_custom) && $params.type_custom == 'avatar'}
        <span class="avatar-list">
        {if strlen($tr['avatar'])>0}
        <img src="{$params.base_dir_ssl|escape:'htmlall':'UTF-8'}{$params.path_img_cloud|escape:'htmlall':'UTF-8'}{$tr['avatar']|escape:'htmlall':'UTF-8'}" />
        {else}
        <img src = "../modules/blockguestbook/views/img/avatar_m.gif" />
        {/if}
        </span>


    {elseif isset($params.type_custom) && $params.type_custom == 'web'}


                    <span class="label-tooltip" data-original-title="{$tr['web']|escape:'htmlall':'UTF-8'}" data-toggle="tooltip">
                    {*<a href="{$tr['web']|escape:'htmlall':'UTF-8'}" style="text-decoration:underline">*}
                        {$tr['web']|escape:'htmlall':'UTF-8'}
                    {*</a>*}
                </span>

    {else}
        {$smarty.block.parent}
    {/if}


{/block}