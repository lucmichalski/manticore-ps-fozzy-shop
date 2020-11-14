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

{if $blockguestbookis15 == 0}
<link href="{$base_dir_ssl|escape:'htmlall':'UTF-8'}modules/blockguestbook/views/css/blockguestbook.css" rel="stylesheet" type="text/css" media="all" />
{literal}
    <script type="text/javascript" src="{/literal}{$base_dir_ssl|escape:'htmlall':'UTF-8'}{literal}modules/blockguestbook/views/js/blockguestbook.js"></script>
{/literal}
{/if}

{if $blockguestbookis17 == 1}
{literal}
    <script type="text/javascript">
        //<![CDATA[
        var baseDir = '{/literal}{$base_dir_ssl|escape:'htmlall':'UTF-8'}{literal}';
        //]]>
    </script>
{/literal}
{/if}

{if $blockguestbookrssong == 1}
    <link rel="alternate" type="application/rss+xml" href="{$base_dir_ssl|escape:'htmlall':'UTF-8'}modules/blockguestbook/rss_guestbook.php" />
{/if}


{literal}
<style type="text/css">
    .ps15-color-background-g{background-color:{/literal}{$blockguestbookBGCOLOR_G|escape:'htmlall':'UTF-8'}{literal};}
</style>
{/literal}


