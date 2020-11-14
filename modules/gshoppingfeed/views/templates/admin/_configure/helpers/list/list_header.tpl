{*
 * 2016 Terranet
 *
 * NOTICE OF LICENSE
 *
 * @author    Terranet
 * @copyright 2016 Terranet
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*}

{extends file="helpers/list/list_header.tpl"}

{block name="override_header"}
    {if ($list_id == 'gshoppingfeed_taxonomy')}
        <div class="panel text-center step_container_1">
            <span data-tab="gshoppingfeed_taxonomy" class="step-title tab">{l s='Step 1' mod='gshoppingfeed'}</span>
            <p class="help-block">
                {l s='Associate google-taxonomy categories' mod='gshoppingfeed'}
            </p>
        </div>
    {elseif ($list_id == 'gshoppingfeed_taxonomy_lang_list')}
        <div class="panel text-center step_container_1">
            <span data-tab="gshoppingfeed_taxonomy_lang_list"
                  class="step-title tab">{l s='Taxonomy Addons configurations' mod='gshoppingfeed'}</span>
            <p class="help-block">
                {l s='Google Taxonomy set' mod='gshoppingfeed'}
            </p>
        </div>
    {/if}
{/block}

{block name="preTable"}
    {if $list_id == 'gshoppingfeed'}
        <a class="pull-right" href="{$allFeedLink|escape:'htmlall':'UTF-8'}">
            {l s='All feeds URL' mod='gshoppingfeed'}
        </a>
    {/if}
    {if ($list_id == 'gshoppingfeed_taxonomy' && $languages|count >= 1)}
        <div class="form-group">
            <label class="control-label col-lg-2">
                &raquo; {l s='Select language' mod='gshoppingfeed'} :
            </label>
            <div class="col-lg-3">
                {if $languages|count > 1}
                <div class="form-group">
                    {/if}
                    <select class="lang_google_lists" id="change_google_lists" name="lang_google_lists">
                        {foreach $languages as $language}
                            <option {if isset($id_language) && $id_language == $language.id_lang}selected="selected"{/if}
                                    value="{$language.id_lang|escape:'htmlall':'UTF-8'}">{$language.name|escape:'htmlall':'UTF-8'}
                                - ({$language.language_code|escape:'htmlall':'UTF-8'})
                            </option>
                        {/foreach}
                    </select>
                    {if $languages|count > 1}
                </div>
                {/if}
            </div>
        </div>
        {strip}
            {addJsDef reloadTaxonomyUri=$update_path}
        {/strip}
    {/if}
{/block}