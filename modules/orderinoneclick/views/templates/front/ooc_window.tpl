{*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @author    Yuri Denisov <contact@splashmart.ru>
*  @copyright 2014-2017 Yuri Denisov
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<script type="text/javascript">
    var product_button='{$product_button|escape:'htmlall':'UTF-8'}';
</script>

<div class="modal-header">
    {if (isset($titles->title))}
        {if $titles->title != ''}
            <h3 class="modal-title ooc_title">{$titles->title|escape:'htmlall':'UTF-8'}</h3>
        {/if}
    {/if}
    {if (isset($titles->description))}
        {if $titles->description != ''}
            <h5 class="modal-title ooc_description">{$titles->description|escape:'htmlall':'UTF-8'}</h5>
        {/if}
    {/if}              
</div>

<div class="modal-body">
    {if (isset($fields))}
        {if $fields != ''}

            <form action="" method="post" id="ooc_form" class="form-horizontal">
                {foreach from=$fields item=field}

                    {if !isset($field.is_valid)}
                        {assign var="is_valid" value="1"}
                    {else}
                        {assign var="is_valid" value=$field.is_valid}
                    {/if}

                    {if !isset($field.error_required)}
                        {assign var="error_required" value="0"}
                    {else}
                        {assign var="error_required" value=$field.error_required}
                    {/if}
                    
                    <div class="form-group row">
                        <label class="control-label col-sm-4" for="{$field.id|escape:'htmlall':'UTF-8'}" {if !empty($field.tip.$id_lang)}data-toggle="tooltip" title="{$field.tip.$id_lang|escape:'htmlall':'UTF-8'}"{/if}>
                            {if $field.required}<span class="ooc_required">*</span>{/if}
                            <span>
                                {$field.name.$id_lang|escape:'htmlall':'UTF-8'}:
                            </span>
                        </label>

                        <div class="col-sm-6">
                            {if ($error_required)}<div class="ooc_error_field">{l s='Field is required' mod='orderinoneclick'}</div>{/if}
                            {if (!$is_valid && !$error_required)}<div class="ooc_error_field">{l s='Error in fill field' mod='orderinoneclick'}</div>{/if}
                            <input class="form-control" type="text" id="{$field.id|escape:'htmlall':'UTF-8'}" name="{$field.id|escape:'htmlall':'UTF-8'}" {if (isset($field.value))}value="{$field.value|escape:'htmlall':'UTF-8'}"{/if} placeholder="{$field.description.$id_lang|escape:'htmlall':'UTF-8'}">
                        </div>
                    </div>
                {/foreach}

                {if isset($require_cms) && $require_cms != 0}
                    <div class="form-group row"> 
                        <div class="col-sm-offset-4 col-sm-6">
                            {if isset($cms_error_required) && $cms_error_required}
                                <div class="ooc_error_field">{l s='You must agree term and condition use.' mod='orderinoneclick'}</div>
                            {/if}
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" id="cms_agree" name="cms_agree" value="1" {if $agree}checked{/if}>&nbsp;
                                    {l s='I agree' mod='orderinoneclick'} <a href="{$href_agree|escape:'htmlall':'UTF-8'}" target="_blank">{l s='term and condition use' mod='orderinoneclick'}.</a>
                                </label>
                            </div>
                        </div>
                    </div>
                {/if}

                {if ($product_button)}
                    <input type="hidden" name="product_button" value="1">
                {/if}
                <div class="modal-footer ooc_footer">
                    <button id="hide-ooc-window" type="button" class="btn btn-default">{l s='Cancel' mod='orderinoneclick'}</button>
                    <input class="btn btn-primary" type="submit" value="{l s='Submit' mod='orderinoneclick'}">
                </div>
            </form>
        {/if}
    {/if}
</div>
