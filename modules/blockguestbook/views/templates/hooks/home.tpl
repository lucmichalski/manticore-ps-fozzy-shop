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

{if $blockguestbookg_home == 1}
<div {if $blockguestbookis_ps15 == 1 && $blockguestbookis17 == 0}id="left_column"{/if}>
	<div id="blockguestbook_block_home" class="block {if $blockguestbookis17 == 1}block-categories{/if} ps15-color-background-g blockmanufacturer">
		
		<h4 class="title_block {if $blockguestbookis17 == 1}text-uppercase h6{/if} {if $blockguestbookis16 == 1}guestbook-block-h4{/if}">
            <a href="{$blockguestbookguestbook_url|escape:'htmlall':'UTF-8'}">
                {l s='Guestbook' mod='blockguestbook'}&nbsp;(&nbsp;{$blockguestbookcount_all_reviews|escape:'htmlall':'UTF-8'}&nbsp;)
            </a>

            {if $blockguestbookrssong == 1}
                <a class="margin-left-5" href="{$base_dir_ssl|escape:'htmlall':'UTF-8'}modules/blockguestbook/rss_guestbook.php" title="{l s='RSS Feed' mod='blockguestbook'}" target="_blank">
                    <img src="{$base_dir_ssl|escape:'htmlall':'UTF-8'}modules/blockguestbook/views/img/feed.png" alt="{l s='RSS Feed' mod='blockguestbook'}" />
                </a>
            {/if}
		</h4>
		
		<div class="block_content products-block block-items-data">
		{if $blockguestbookcount_all_reviews > 0}
	    
	    
	    {foreach from=$blockguestbookreviews item=review name=myLoop}
	    <div class="rItem guestbook-width-auto" >
            <div class="ratingBox text-align-right">
            <small>{l s='Post By' mod='blockguestbook'} <b>{$review.name|escape:'htmlall':'UTF-8' nofilter}</b></small>
            {if $blockguestbookis_countryg == 1}
                {if strlen($review.country)>0}
                    , <span class="fs-12">{$review.country|escape:'htmlall':'UTF-8' nofilter}</span>
                {/if}
            {/if}
            {if $blockguestbookis_cityg == 1}
                {if strlen($review.city)>0}
                   , <span class="fs-12">{$review.city|escape:'htmlall':'UTF-8' nofilter}</span>
                {/if}
            {/if}
            </div>
            <div class="clear"></div>
            <div>
                {if $blockguestbookis_avatarg == 1}
                <div class="float-left avatar-block-home">
                    <img
                            {if strlen($review.avatar)>0}
                                src="{$base_dir_ssl|escape:'htmlall':'UTF-8'}{$blockguestbookpic|escape:'htmlall':'UTF-8'}{$review.avatar|escape:'htmlall':'UTF-8'}"
                            {else}
                                src="{$base_dir_ssl|escape:'htmlall':'UTF-8'}modules/blockguestbook/views/img/avatar_m.gif"
                            {/if}
                            alt="{$review.name|escape:'htmlall':'UTF-8'}"
                            />
                </div>
                {/if}
                <div class="font-size-11 float-left {if $blockguestbookis_avatarg == 1}guestbook-block-text-home{else}guestbook-block-text-home-100{/if}">
                    {$review.message|substr:0:245|escape:'htmlall':'UTF-8' nofilter}
                    {if strlen($review.message)>245}...{/if}

                </div>
                <div class="clear"></div>
            </div>


            <small class="float-right">{$review.date_add|date_format:"%d-%m-%Y"|escape:'htmlall':'UTF-8'}</small>

            {if $blockguestbookis_webg == 1}
                {if strlen($review.web)>0}
                    <small class="float-right margin-right-10">
                        <a title="http://{$review.web|escape:'htmlall':'UTF-8'}" rel="nofollow" href="http://{$review.web|escape:'htmlall':'UTF-8'}"
                           target="_blank" class="guestbook-link-web"
                                >http://{$review.web|escape:'htmlall':'UTF-8'}</a>
                    </small>
                {/if}
            {/if}
            <div class="clear"></div>
		</div>
		{/foreach}

            <div class="text-align-right">
                <a class="button_large {if $blockguestbookis17 == 1}button-small-blockguestbook{/if}" title="{l s='View all posts' mod='blockguestbook'}"
                   href="{$blockguestbookguestbook_url|escape:'htmlall':'UTF-8'}"
                        >
                    {l s='View all posts' mod='blockguestbook'}
                </a>
            </div>
	    
	    {else}
		<div class="no-items-shopreviews guestbook-width-auto">
			{l s='There are not posts yet.' mod='blockguestbook'}
		</div>
		{/if}
		</div>
	</div>
</div>
{/if}