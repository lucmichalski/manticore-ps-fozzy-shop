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

{if $blockguestbookg_footer == 1}

{if $blockguestbookis17 == 1}
    <div class="clear"></div>
{/if}

        {if $blockguestbookis16 == 1}
            {if $blockguestbookis17 == 1}
                <div class="col-xs-12 col-sm-3 wrapper links ps15-color-background-g">
            {else}
                <section class="footer-block col-xs-12 col-sm-3 {if $blockguestbookis16 == 0}ps15-color-background-g{/if}">
            {/if}
        {else}

            <div class="clear"></div>
	        <div id="blockguestbook_block_footer"  class="block footer-block myaccount {if $blockguestbookis16 == 0}ps15-color-background-g{/if} margin-5 {if $blockguestbookis15 == 0}color-black{/if}"
	        >
        {/if}
		
		<h4 {if $blockguestbookis17 == 1}class="h3 hidden-sm-down"{/if}>
			<div class="float-left">
					<a {if $blockguestbookis15 == 0}class="color-black"{/if}
					href="{$blockguestbookguestbook_url|escape:'htmlall':'UTF-8'}"
					>{l s='Guestbook' mod='blockguestbook'}&nbsp;(&nbsp;{$blockguestbookcount_all_reviews|escape:'htmlall':'UTF-8'}&nbsp;)</a>

            </div>
            <div class="float-left margin-left-5">
            {if $blockguestbookrssong == 1}
                <a href="{$base_dir_ssl|escape:'htmlall':'UTF-8'}modules/blockguestbook/rss_guestbook.php" title="{l s='RSS Feed' mod='blockguestbook'}" target="_blank">
                    <img src="{$base_dir_ssl|escape:'htmlall':'UTF-8'}modules/blockguestbook/views/img/feed.png" alt="{l s='RSS Feed' mod='blockguestbook'}" />
                </a>
            {/if}
            </div>
            <div class="clear"></div>
		</h4>

    {if $blockguestbookis17 == 1}
        <div data-toggle="collapse" data-target="#blockfaq_block_footer17" class="title clearfix hidden-md-up">
            <span class="h3">{l s='Guestbook' mod='blockguestbook'}</span>
                        <span class="pull-xs-right">
                          <span class="navbar-toggler collapse-icons">
                            <i class="material-icons add">&#xE313;</i>

                          </span>
                        </span>
        </div>
    {/if}

		<div class="block_content block-items-data toggle-footer {if $blockguestbookis17 == 1}collapse{/if}" {if $blockguestbookis17 == 1}id="blockfaq_block_footer17"{/if}>
		{if $blockguestbookcount_all_reviews > 0}
	    
	    
	    {foreach from=$blockguestbookreviews_footer item=review name=myLoop}
	    <div class="rItem {if $blockguestbookis_ps15 == 1 && $blockguestbookis16 == 0}padding-0{/if}">
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
                    alt="{$review.name|escape:'htmlall':'UTF-8' nofilter}"
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
	    
	    <p align="center">
	             <a class="button_large {if $blockguestbookis17 == 1}button-small-blockguestbook{/if}" title="{l s='View all posts' mod='blockguestbook'}"
                       href="{$blockguestbookguestbook_url|escape:'htmlall':'UTF-8'}"
                            >
                        {l s='View all posts' mod='blockguestbook'}
                    </a>
		</p>
	    
	    {else}
		<div class="no-items-shopreviews">
			{l s='There are not posts yet.' mod='blockguestbook'}
		</div>
		{/if}
		</div>



	{if $blockguestbookis16 == 1}
        {if $blockguestbookis17 == 1}
            </div>
        {else}
            </section>
        {/if}
    {else}
	    </div>
    {/if}
{/if}