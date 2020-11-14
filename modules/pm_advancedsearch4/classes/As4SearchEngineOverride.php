<?php
/**
 *
 * @author Presta-Module.com <support@presta-module.com>
 * @copyright Presta-Module
 * @license   Commercial
 *
 *           ____     __  __
 *          |  _ \   |  \/  |
 *          | |_) |  | |\/| |
 *          |  __/   | |  | |
 *          |_|      |_|  |_|
 *
 ****/

if (!defined('_PS_VERSION_')) {
    exit;
}

abstract class As4SearchEngineOverride extends As4SearchEngine
{
    public static function getProductsByNativeSearch($expr)
    {
        $context = Context::getContext();
        $id_lang = (int)$context->language->id;
        $id_shop = (int)$context->shop->id;

        $search_text = mb_strtolower(isset($_REQUEST['s'])?$_REQUEST['s']:(isset($_REQUEST['search_query'])?$_REQUEST['search_query']:$expr), 'UTF-8');
        $cache_id = 'sphinxSearchCategory_'.$id_lang.'_'.$id_shop.'_'.md5($search_text);
        if (Cache::isStored($cache_id)) {
            //echo '<fieldset><legend>'.$cache_id.'='.var_export(Cache::isStored($cache_id),1).'</legend>'.json_encode(Cache::retrieve($cache_id)).'</fieldset>'.PHP_EOL;
            return Cache::retrieve($cache_id);
        }

        //$db = Db::getInstance(_PS_USE_SQL_SLAVE_);
        $expr = Tools::replaceAccentedChars(urldecode($expr));

        //if (false!==stripos($search_text,'і') || false!==stripos($search_text,'ї') || false!==stripos($search_text,'є')) $id_lang = 2;

        // Sphinx search, get ids of found category
        $sphinx_results = Search::getSphinxResults($id_lang, $search_text);
        if (false === $sphinx_results) return false;

        if (empty($sphinx_results['total']) && preg_match('![a-z]!i', $search_text)) {
            $sphinx_results = Search::getSphinxResults($id_lang, strtr($search_text, Search::getKeybords()));
        }
        if (empty($sphinx_results['total'])) {
            $sphinx_results = Search::getSphinxResults($id_lang, $expr, $context);
        }
        if (empty($sphinx_results['total']) && preg_match('![а-яїіє]!i', $search_text)) {
            $sphinx_results = Search::getSphinxResults($id_lang, strtr($search_text, Search::getKeybordsRu()));
        }

        if ($sphinx_results['total']<=0) {
            self::$productFilterListQuery = '-1';
        } else {
            self::$productFilterListQuery = implode(',',$sphinx_results['results']);
        }

        Cache::store($cache_id, self::$productFilterListQuery);
    }
}
