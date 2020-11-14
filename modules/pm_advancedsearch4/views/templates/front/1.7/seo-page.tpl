{extends file='catalog/listing/category.tpl'}

{block name='product_list_header'}
    {if $as_seo_description}
        <div class="block-category card card-block hidden-sm-down">
            <h1 class="h1">{$as_seo_title}</h1>
            {if $as_seo_description}
                <div id="category-description" class="text-muted">{$as_seo_description nofilter}</div>
            {/if}
        </div>
        <div class="text-xs-center hidden-md-up">
            <h1 class="h1">{$as_seo_title}</h1>
        </div>
    {else}
        <h2 class="h2">{$listing.label}</h2>
    {/if}
{/block}

{block name='product_list_active_filters'}
    <div id="js-active-search-filters" class="hidden-sm-down">
        {$listing.rendered_active_filters nofilter}
    </div>
{/block}

{block name='content'}
    <div id="PM_ASearchResults" data-id-search="{$id_search|intval}">
        <div id="PM_ASearchResultsInner" class="PM_ASearchResultsInner_{$id_search|intval}">
            {$smarty.block.parent}
            {if isset($as_cross_links) && $as_cross_links && sizeof($as_cross_links)}
                <div id="PM_ASearchSeoCrossLinks" class="card-block">
                    <h4 class="h4">{$as_see_also_txt}</h4>
                    <ul class="bullet">
                    {foreach from=$as_cross_links item=as_cross_link}
                        <li>
                            <a href="{$as_cross_link.public_url nofilter}">{$as_cross_link.title}</a>
                        </li>
                    {/foreach}
                    </ul>
                </div>
            {/if}
        </div>
    </div>
{/block}