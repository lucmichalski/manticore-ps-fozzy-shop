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

{if $blockfaqis15 == 0}
<link href="{$base_dir_ssl|escape:'htmlall':'UTF-8'}modules/blockfaq/views/css/blockfaq.css" rel="stylesheet" type="text/css" media="all" />
{/if}

{if $blockfaqis17 == 1}
{literal}
    <script type="text/javascript">
        //<![CDATA[
        var baseDir = '{/literal}{$base_dir_ssl|escape:'htmlall':'UTF-8'}{literal}';
        //]]>
    </script>
{/literal}
{/if}