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

{if $blockfaqfaq_left == 1}

	<div id="blockfaq_block_left" class="block {if $blockfaqis17 == 1}block-categories hidden-sm-down{/if} {if $blockfaqis16 == 1}blockmanufacturer16{else}blockmanufacturer{/if}">
		<h4 class="title_block {if $blockfaqis17 == 1}text-uppercase{/if}">
		<a href="{$blockfaqfaq_url|escape:'htmlall':'UTF-8'}" title="{l s='FAQ' mod='blockfaq'}">
			{l s='FAQ' mod='blockfaq'}
		</a>
		</h4>
		<div class="block_content">
		{if count($blockfaqitemsblock) > 0}
	    <ul class="bullet">
	    	{foreach from=$blockfaqitemsblock item=items name=myLoop1}
	    	{foreach from=$items.data item=item name=myLoop}
	    	<li class="{if $smarty.foreach.myLoop.first}first_item{elseif $smarty.foreach.myLoop.last}last_item{else}item{/if}">
	    	<a href="{$blockfaqfaq_url|escape:'htmlall':'UTF-8'}#faq_{$item.id|escape:'UTF-8'}"
	    		   title="{$item.title nofilter}">
            	{$item.title nofilter}
	    	</a>
	    	</li>
	    	{/foreach}
	    	{/foreach}
	    </ul>
	     <p class="block-all-faq-items-button">
	     	 <a class="button_large {if $blockfaqis17 == 1}btn btn-default button button-small-blockshopreviews{/if}"  title="{l s='View all' mod='blockfaq'}"
				 href="{$blockfaqfaq_url|escape:'htmlall':'UTF-8'}">{l s='View all' mod='blockfaq'}</a>

		</p>
	    {else}
		<div class="block-no-faq-items" >
			{l s='Questions not found.' mod='blockfaq'}
		</div>
		{/if}
		</div>
	</div>
{/if}