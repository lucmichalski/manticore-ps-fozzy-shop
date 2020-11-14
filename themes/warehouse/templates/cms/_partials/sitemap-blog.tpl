{block name='sitemap_item'}
            <ul>
            {if isset($simpleblogmap)}
            <li>
                <a href="{$link->getModuleLink('ph_simpleblog', 'list')|escape:'html':'UTF-8'}" title="{l s='Blog' d='Shop.Theme.Global'}">{l s='Blog' d='Shop.Theme.Global'}</a>
            </li>
                {foreach from=$simpleblogmap key=myId item=cat name=foo}
                    <ul class="nested">
                    <li {if $smarty.foreach.foo.last} class="last" {/if}><a href="{$cat.category_url}" title="{$cat.name}">{$cat.name}</a>
                    {if isset($cat.blogs)}
                    <ul class="nested">
                    {foreach from=$cat.blogs key=myId2 item=blog name=foo2}
                    <li>
                    <a href="{$blog.url}" title="{$blog.title}">{$blog.title}</a>
                    </li>
                    {/foreach}
                    </ul>
                    {/if}
                    </li>
                    </ul>
                {/foreach}
            {/if}
            </ul>
{/block}