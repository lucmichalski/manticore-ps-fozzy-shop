{*
 *
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
 *
*}

{extends file="helpers/form/form.tpl"}
{block name="field"}

    {if $input.type == 'checkbox_custom'}
        <div class="col-lg-9 {$input.name|escape:'htmlall':'UTF-8'}" style="padding: 7px">

            <input type="checkbox" name="{$input.name|escape:'htmlall':'UTF-8'}" id="{$input.name|escape:'htmlall':'UTF-8'}"
                   value="1" {if $input.values.value == 1} checked="checked"{/if} />



            {if isset($input.desc) && !empty($input.desc)}
                <p class="help-block">
                    {$input.desc|escape:'htmlall':'UTF-8'}
                </p>
            {/if}
        </div>

    {elseif $input.type == 'checkbox_custom_blocks'}
        <div class="col-lg-9 {$input.name|escape:'htmlall':'UTF-8'}">

            {foreach $input.values.query as $value}
                {assign var=id_checkbox value=$value[$input.values.id]}
                <div class="checkbox{if isset($input.expand) && strtolower($input.expand.default) == 'show'} hidden{/if}">

                    {strip}
                        <label for="{$id_checkbox|escape:'htmlall':'UTF-8'}">
                            <input type="checkbox" name="{$id_checkbox|escape:'htmlall':'UTF-8'}" id="{$id_checkbox|escape:'htmlall':'UTF-8'}" class="{if isset($input.class)}{$input.class|escape:'htmlall':'UTF-8'}{/if}"{if isset($value.val)} value="{$value.val|escape:'htmlall':'UTF-8'}"{/if}{if isset($fields_value[$id_checkbox]) && $fields_value[$id_checkbox]} checked="checked"{/if} />
                            {$value[$input.values.name]|escape:'htmlall':'UTF-8'}
                        </label>
                    {/strip}
                </div>
            {/foreach}

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





