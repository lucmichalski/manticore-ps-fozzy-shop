{**
* 2010-2020 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2020 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

{if isset($manifestFile)}
    <link rel="manifest" href="{$manifestFile}">
{/if}

<meta name="mobile-web-app-capable" content="yes">
<meta name="application-name" content="{$applicationName}">
<meta name="theme-color" content="{$applicationThemeColor}">

<!-- Add to home screen for Safari on iOS -->
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="{$applicationThemeColor}">
<meta name="apple-mobile-web-app-title" content="{$applicationName}">
<link rel="apple-touch-icon" href="{$appleTouchIcon}">

<!-- Tile Icon for Windows -->
<meta name="msapplication-TileImage" content="{$applicationFavicon}">
<meta name="msapplication-TileColor" content="{$applicationThemeColor}">
<meta name="msapplication-starturl" content="{$startUrl}">
