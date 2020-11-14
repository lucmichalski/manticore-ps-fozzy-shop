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

{extends file="helpers/form/form.tpl"}
{block name="field"}
    {if $input.type == 'language_item' || $input.type == 'id_item' || $input.type == 'shop_item'}


    <div class="col-lg-9 margin-form">

        <div class="form-group margin-item-form-top-left">
                <span class="badge">
                {$input.values|escape:'htmlall':'UTF-8'}
                    </span>
        </div>


        {if isset($input.desc) && !empty($input.desc)}
            <p class="help-block">
                {$input.desc|escape:'htmlall':'UTF-8'}
            </p>
        {/if}
    </div>

    {elseif $input.type == 'language_item_add'}


        <div class="col-lg-9 margin-form">


            <select id="id_lang" class=" fixed-width-xl" name="id_lang">
                {foreach $input.values as $language}

                    <option value="{$language.id_lang|escape:'htmlall':'UTF-8'}" {if $input.id_lang == $language.id_lang}selected="selected"{/if}>{$language.name|escape:'htmlall':'UTF-8'}</option>
                {/foreach}
            </select>


            {if isset($input.desc) && !empty($input.desc)}
                <p class="help-block">
                    {$input.desc|escape:'htmlall':'UTF-8'}
                </p>
            {/if}
        </div>

    {elseif $input.type == 'checkbox_custom'}
        <div class="col-lg-9 {$input.name|escape:'htmlall':'UTF-8'}">

            <input type="checkbox" name="{$input.name|escape:'htmlall':'UTF-8'}" id="{$input.name|escape:'htmlall':'UTF-8'}"
                   value="1" {if $input.values.value == 1} checked="checked"{/if} />



            {if isset($input.desc) && !empty($input.desc)}
                <p class="help-block">
                    {$input.desc|escape:'htmlall':'UTF-8'}
                </p>
            {/if}
        </div>


    {elseif $input.type == 'avatar_custom'}
    <div class="col-lg-9 margin-form">

        {if isset($input.is_demo) && !empty($input.is_demo)}
            {$input.is_demo|escape:'quotes':'UTF-8'}
        {/if}

        <span class="avatar-form">
        {if strlen($input.value)>0}
            <img src="{$input.base_dir_ssl|escape:'htmlall':'UTF-8'}{$input.path_img_cloud|escape:'htmlall':'UTF-8'}{$input.value|escape:'htmlall':'UTF-8'}" />
            <br/>
            <a class="delete_product_image btn btn-default" href="javascript:void(0)"
               onclick = "delete_avatar({$input.id_item|escape:'htmlall':'UTF-8'});"
               style="margin-top: 10px">
                <i class="icon-trash"></i> {l s='Delete avatar and use standart empty avatar' mod='blockguestbook'}
            </a>

        {else}
        <img src = "../modules/blockguestbook/views/img/avatar_m.gif" />
        {/if}
        </span>



     </div>

    {elseif $input.type == 'item_date'}

        <div class="row">
            <div class="input-group col-lg-4">
                <input id="{if isset($input.id)}{$input.id|escape:'htmlall':'UTF-8'}{else}{$input.name|escape:'htmlall':'UTF-8'}{/if}"
                       type="text" data-hex="true"
                       {if isset($input.class)}class="{$input.class}"
                       {else}class="item_datepicker"{/if} name="time_add" value="{$input.time_add|escape:'html':'UTF-8'}" />
                <span class="input-group-addon"><i class="icon-calendar-empty"></i></span>
            </div>
        </div>

    {literal}

        <script type="text/javascript">
            $('document').ready( function() {

                var dateObj = new Date();
                var hours = dateObj.getHours();
                var mins = dateObj.getMinutes();
                var secs = dateObj.getSeconds();
                if (hours < 10) { hours = "0" + hours; }
                if (mins < 10) { mins = "0" + mins; }
                if (secs < 10) { secs = "0" + secs; }
                var time = " "+hours+":"+mins+":"+secs;

                if ($(".item_datepicker").length > 0)
                    $(".item_datepicker").datepicker({prevText: '',nextText: '',dateFormat: 'yy-mm-dd'+time});

            });
        </script>
    {/literal}



    {elseif $input.type == 'text_custom'}



        <div class="col-lg-4">

            <div class="input-group">
                <input type="text" name="{$input.name|escape:'htmlall':'UTF-8'}" value="{$input.value|escape:'htmlall':'UTF-8'}" />
            </div>
            {if isset($input.desc) && !empty($input.desc)}
                <p class="help-block">
                    {$input.desc|escape:'htmlall':'UTF-8'}
                </p>
            {/if}
        </div>

    {else}
        {$smarty.block.parent}
    {/if}
{/block}
