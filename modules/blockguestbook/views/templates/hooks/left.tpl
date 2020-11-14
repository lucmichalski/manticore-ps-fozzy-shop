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

{if $blockguestbookg_left == 1}

	<div  id="blockguestbook_block_left"  class="block blockmanufacturer {if $blockguestbookis17 == 1}block-categories hidden-sm-down{/if} ps15-color-background-g {if $blockguestbookis16 == 1}blockguestbook-block16{/if}">
		
		<h4 class="title_block {if $blockguestbookis16 == 1}guestbook-block-h4{/if} {if $blockguestbookis17 == 1}text-uppercase{/if}">
			<a href="{$blockguestbookguestbook_url|escape:'htmlall':'UTF-8'}">
				{l s='Guestbook' mod='blockguestbook'}&nbsp;(&nbsp;{$blockguestbookcount_all_reviews|escape:'htmlall':'UTF-8'}&nbsp;)
			</a>

            {if $blockguestbookrssong == 1}
                <a class="margin-left-5" href="{$base_dir_ssl|escape:'htmlall':'UTF-8'}modules/blockguestbook/rss_guestbook.php" title="{l s='RSS Feed' mod='blockguestbook'}" target="_blank">
                    <img src="{$base_dir_ssl|escape:'htmlall':'UTF-8'}modules/blockguestbook/views/img/feed.png" alt="{l s='RSS Feed' mod='blockguestbook'}" />
                </a>
            {/if}
		</h4>
		
		<div class="block_content  products-block">
		{if $blockguestbookcount_all_reviews > 0}
	    
	    
	    {foreach from=$blockguestbookreviews item=review name=myLoop}
	    <div class="rItem">
			<div class="ratingBox">
				<small>{l s='Post By' mod='blockguestbook'} <b>{$review.name|escape:'htmlall':'UTF-8' nofilter}</b></small>
			</div>
            <div class="clear"></div>
            <div class="margin-bottom-5">
                {if $blockguestbookis_avatarg == 1}
                <div class="float-left {if $blockguestbookis16 == 1}avatar-block{else}avatar-block15{/if}">
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

                <div class="font-size-11 float-left {if $blockguestbookis16 == 1}{if $blockguestbookis_avatarg == 1}guestbook-block-text{else}guestbook-block-text-100{/if}{else}{if $blockguestbookis_avatarg == 1}guestbook-block-text15{else}guestbook-block-text15-100{/if}{/if}">
                    {$review.message|substr:0:100|escape:'htmlall':'UTF-8' nofilter}
                    {if strlen($review.message)>100}...{/if}

                </div>
                <div class="clear"></div>
            </div>
            {if $blockguestbookis_webg == 1}
                {if strlen($review.web)>0}
                    <small class="float-right">
                        <a title="http://{$review.web|escape:'htmlall':'UTF-8'}" rel="nofollow" href="http://{$review.web|escape:'htmlall':'UTF-8'}"
                           target="_blank" class="guestbook-link-web"
                                >http://{$review.web|escape:'htmlall':'UTF-8'}</a>
                    </small>
                {/if}
            {/if}
            <small class="float-left">{$review.date_add|date_format:"%d-%m-%Y"|escape:'htmlall':'UTF-8'}</small>
            <div class="clear"></div>
		</div>
		{/foreach}
	    
	    <div class="submit_guestbook text-align-center">
	    	<a class="button_large {if $blockguestbookis17 == 1}button-small-blockguestbook{/if}" title="{l s='View all posts' mod='blockguestbook'}"
			   href="{$blockguestbookguestbook_url|escape:'htmlall':'UTF-8'}"
			>
	    		{l s='View all posts' mod='blockguestbook'}
			</a>
		</div>
	    
	    {else}
		<div class="no-items-shopreviews">
			{l s='There are not posts yet' mod='blockguestbook'}
		</div>
		{/if}
		</div>
	</div>
{/if}