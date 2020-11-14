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

{if $blockfaqfaq_spm == 1}
        <div {if $blockfaqis_ps15 == 1}id="left_column"{/if}>
            <div id="blockfaq_block_left"  class="block {if $blockfaqis17 == 1}block-categories{/if}  {if $blockfaqis16 == 1}blockmanufacturer16{else}blockmanufacturer{/if}">
                <h4 class="title_block {if $blockfaqis17 == 1}text-uppercase{/if}">
                    <a href="{$blockfaqfaq_url|escape:'htmlall':'UTF-8'}" title="{l s='FAQ' mod='blockfaq'}">
                        {l s='FAQ' mod='blockfaq'}
                    </a>
                </h4>
                <div class="block_content block-items-data">
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

                    {else}
                        <div class="block-no-faq-items-home">
                            {l s='Questions not found.' mod='blockfaq'}
                        </div>
                    {/if}
                </div>
            </div>
        </div>
{/if}

