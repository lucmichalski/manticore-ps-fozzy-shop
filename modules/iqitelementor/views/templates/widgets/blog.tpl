{*
* 2007-2016 PrestaShop
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
*  @copyright  2007-2016 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{if isset($posts) && count($posts)}


    {if $view == 'carousel'}
        <section class="elementor-blog-posts elementor-blog-posts-carousel elementor-slick-slider ph_simpleblog">
            <div class="elementor-blog-carousel simpleblog-posts {$classes}" data-slider_options='{$options|@json_encode nofilter}'>
                {foreach $posts as $post}
                    <div class="simpleblog-posts-column">{include file="module:ph_simpleblog/views/templates/front/1.7/_partials/post-miniature.tpl" is_category=0 post=$post blogLayout='grid' columns=0}</div>
                {/foreach}
            </div>
        </section>
    {elseif $view == 'grid'}
        <section class="elementor-blog-posts elementor-blog-posts-grid ph_simpleblog">
            <div class="row simpleblog-posts">
                {foreach $posts as $post}
                    <div class="simpleblog-posts-column {$options.gridClasses}">{include file="module:ph_simpleblog/views/templates/front/1.7/_partials/post-miniature.tpl" is_category=0 post=$post blogLayout='grid' columns=0}</div>
                {/foreach}
            </div>
        </section>
    {elseif $view == 'list'}
        <section class="elementor-blog-posts elementor-blog-posts-list">
                {foreach $posts as $post}
                    {include file="module:iqitelementor/views/templates/widgets/blog/post-small.tpl" post=$post}
                {/foreach}
        </section>
    {/if}


{/if}



