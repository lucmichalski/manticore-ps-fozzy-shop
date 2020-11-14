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
 * @package blockfaq
 * @copyright Copyright StorePrestaModules SPM
 * @license   StorePrestaModules SPM
 */
*}

{if $blockfaqfaq_footer == 1}

{if $blockfaqis17 == 1}
    <div class="clear"></div>
{/if}

    {if $blockfaqis16 == 1}
        {if $blockfaqis17 == 1}
            <div class="col-xs-12 col-sm-3 wrapper links">
        {else}
            <section class="blockfaq_block_footer footer-block col-xs-12 col-sm-3">
        {/if}
    {else}
	    <div class="clear"></div>
	    <div id="blockfaq_block_footer"  class="block footer-block block-faq-footer {if $blockfaqis16 == 1}blockmanufacturer16-footer{else}blockmanufacturer{/if}">
	{/if}
		<h4 align="left" {if $blockfaqis17 == 1}class="h3 hidden-sm-down"{/if} {if $blockfaqis16 == 0}class="block-footer-h4"{/if}>
		<a href="{$blockfaqfaq_url|escape:'htmlall':'UTF-8'}" title="{l s='FAQ' mod='blockfaq'}">
			{l s='FAQ' mod='blockfaq'}
		</a>
		</h4>

    {if $blockfaqis17 == 1}
        <div data-toggle="collapse" data-target="#blockfaq_block_footer17" class="title clearfix hidden-md-up">
            <span class="h3">{l s='FAQ' mod='blockfaq'}</span>
                        <span class="pull-xs-right">
                          <span class="navbar-toggler collapse-icons">
                            <i class="material-icons add">&#xE313;</i>

                          </span>
                        </span>
        </div>
    {/if}

		<div class="block_content block-items-data toggle-footer {if $blockfaqis16 == 0}block-footer-content-faq{/if} {if $blockfaqis17 == 1}collapse{/if}" {if $blockfaqis17 == 1}id="blockfaq_block_footer17"{/if} >
		{if count($blockfaqitemsblock) > 0}
	    <ul class="bullet">
	    	{foreach from=$blockfaqitemsblock item=items name=myLoop1}
	    	{foreach from=$items.data item=item name=myLoop}
	    	<li class="{if $smarty.foreach.myLoop.first}first_item{elseif $smarty.foreach.myLoop.last}last_item{else}item{/if}">
                <a href="{$blockfaqfaq_url|escape:'htmlall':'UTF-8'}#faq_{$item.id|escape:'UTF-8'}"
                       title="{$item.title nofilter}" {if $blockfaqis16 == 0}class="block-footer-content-faq-a"{/if}>
                    {$item.title nofilter}
                </a>
	    	</li>
	    	{/foreach}
	    	{/foreach}
	    </ul>
	    
	    {else}
		<div class="block-no-faq-items-footer">
			{l s='Questions not found.' mod='blockfaq'}
		</div>
		{/if}
		</div>


     {if $blockfaqis16 == 1}
        {if $blockfaqis17 == 1}
            </div>
        {else}
            </section>
        {/if}
    {else}
	    </div>
    {/if}

{/if}