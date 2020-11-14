{*
* 2007-2018 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2018 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{extends file='page.tpl'}

{block name='page_header_container'}
<div class="page-heading">
    <h1 class="h1">
    	{if $is_category eq true}
			{$blogCategory->name}
		{else}
			{$blogMainTitle}
		{/if}
    </h2>
	</div><!--.page-heading-->
{/block}

{block name='head_seo_title'}{strip}{$meta_title}{/strip}{/block}
{block name='head_seo_description'}{strip}{$meta_description}{/strip}{/block}

{block name='page_content'}
{if $is_category eq true}
	{if Configuration::get('PH_BLOG_DISPLAY_CATEGORY_IMAGE') && isset($blogCategory->image)}
	<div class="simpleblog-category-image">
		<img src="{$blogCategory->image}" alt="{$blogCategory->name}" class="img-responsive" />
	</div>
	{/if}

	{if !empty($blogCategory->description) && Configuration::get('PH_BLOG_DISPLAY_CAT_DESC')}
	<div class="ph_cat_description rte">
		{$blogCategory->description nofilter}
	</div>
	{/if}
{/if}

<div class="simpleblog__listing" itemscope="itemscope" itemtype="http://schema.org/Blog">
	<div class="row">
		{if isset($posts) && count($posts)}
		{foreach from=$posts item=post}
        	{include file="module:ph_simpleblog/views/templates/front/1.7/_partials/post-miniature.tpl"}
	    {/foreach}
        {else}
			<div class="warning alert alert-warning">{l s='There are no posts' mod='ph_simpleblog'}</div>
        {/if}
	</div><!-- .row -->
</div><!-- .simpleblog__listing -->
{if isset($posts) && count($posts)}
    {if $is_category}
    	{include file="module:ph_simpleblog/views/templates/front/1.7/pagination.tpl" rewrite=$blogCategory->link_rewrite type='category'}
    {else}
    	{include file="module:ph_simpleblog/views/templates/front/1.7/pagination.tpl" rewrite=false type=false}
    {/if}
{/if}
{/block}