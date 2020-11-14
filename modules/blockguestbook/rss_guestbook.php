<?php
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

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
include_once(dirname(__FILE__).'/classes/guestbook.class.php');
$shopreviews = new guestbook();

$_name = "blockguestbook";

if(version_compare(_PS_VERSION_, '1.6', '>')){
$_http_host = Tools::getShopDomainSsl(true, true).__PS_BASE_URI__; 
} else {
$_http_host = _PS_BASE_URL_.__PS_BASE_URI__;
}

if (version_compare(_PS_VERSION_, '1.5', '<')){
	require_once(_PS_MODULE_DIR_.$_name.'/backward_compatibility/backward.php');
} else{
	$cookie = Context::getContext()->cookie;
}

$id_lang = (int)$cookie->id_lang;
$data_language = $shopreviews->getfacebooklib($id_lang);
$rss_title =  '<![CDATA['.Configuration::get('PS_SHOP_NAME').']]>';
$rss_description =  '<![CDATA['.Configuration::get('PS_SHOP_NAME').' Guestbook]]>';

if (Configuration::get('PS_SSL_ENABLED') == 1)
	$url = "https://";
else
	$url = "http://";

$site = $_SERVER['HTTP_HOST'];
			
// Lets build the page
$rootURL = $url.$site."/feeds/";
$latestBuild = date("r");

// Lets define the the type of doc we're creating.
$createXML = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";


if(Configuration::get($_name.'rssong') == 1){

$createXML .= "<rss version=\"0.92\">\n";
$createXML .= "<channel>
	<title>".$rss_title."</title>
	<link>$url$site</link>
	<description>".$rss_description."</description>
	<lastBuildDate>$latestBuild</lastBuildDate>
	<docs>http://backend.userland.com/rss092</docs>
	<language>".$data_language['rss_language_iso']."</language>
	<image>
			<title>".$rss_title."</title>
			<url>".$_http_host."img/logo.jpg</url>
			<link>$url$site</link>
	</image>
";

$data_rss_items = $shopreviews->getItemsForRSS();

//echo "<pre>"; var_dump($data_rss_items); exit;

foreach($data_rss_items['items'] as $_item)
{
	$page = $_item['page']; 
	$description = $_item['seo_description'];
	$title = $_item['title'];
	$pubdate = $_item['pubdate'];
	$createXML .= $shopreviews->createRSSFile($title,$description,$page,$pubdate);
}
$createXML .= "</channel>\n </rss>";
// Finish it up
}

echo $createXML;















?>