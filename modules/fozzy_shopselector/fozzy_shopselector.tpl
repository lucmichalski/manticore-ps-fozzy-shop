<div id="shop-selector" class="d-inline-block">
    <div class="language-selector-wrapper d-inline-block">
        <div class="language-selector dropdown js-dropdown">
            <a class="expand-more" data-toggle="dropdown"> {$current_shop.name} <i class="fa fa-angle-down" aria-hidden="true"></i></a>
            <div class="dropdown-menu">
                <ul>
                    {foreach from=$shops item=shop}
                        <li {if $shop.id_shop == $current_shop.id_shop} class="current" {/if}>
                            <a href="{$shop.url}" rel="alternate" class="dropdown-item"> {$shop.name}</a>
                        </li>
                    {/foreach}
                </ul>
            </div>
        </div>
    </div>
</div>