<?php
/**
* 2007-2017 PrestaShop
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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2017 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class MetaTagsGenerator extends Module
{
    public function __construct()
    {
        if (!defined('_PS_VERSION_')) {
            exit;
        }
        $this->name = 'metatagsgenerator';
        $this->tab = 'administration';
        $this->version = '1.6.3';
        $this->author = 'Amazzing';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->module_key = '007bc184babeabd2ea8ca6f45da0153a';

        parent::__construct();

        $this->displayName = $this->l('Meta tags generator');
        $this->description = $this->l('Meta tags generator');
        $this->db = Db::getInstance();
        $this->shop_ids = Shop::getContextListShopID();
        if ($this->is_17 = (Tools::substr(_PS_VERSION_, 0, 3) === '1.7')) {
            $this->l = $this->getTranslator();
        }
    }

    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }
        if ($this->is_17) {
            $this->registerHook('displayBackOfficeHeader');
        }
        return parent::install()
            && $this->registerHook('actionObjectAddAfter')
            && $this->registerHook('actionObjectUpdateAfter');
    }

    public function uninstall()
    {
        return parent::uninstall()
            && Configuration::deleteByName('MTG_AUTOFILL')
            && Configuration::deleteByName('MTG_SAVED_PATTERNS');
    }

    public function hookDisplayBackOfficeHeader($params)
    {
        // in 1.7 products are saved via ajax
        // this script is required for dynamically displaying generated meta fields
        if ($this->is_17 && Tools::getValue('controller') == 'AdminProducts') {
            $autofill_data = $this->getAutofillData();
            if (!empty($autofill_data['product'])) {
                $this->context->controller->addJquery();
                $js_path = $this->_path.'views/js/update-product-fields.js?v='.$this->version;
                $this->context->controller->js_files[] = $js_path;
                $ajax_path = 'index.php?controller=AdminModules&configure='.$this->name.
                '&token='.Tools::getAdminTokenLite('AdminModules').'&ajax=1';
                $js = '
                    <script type="text/javascript">
                        var mtg_ajax_action_path = \''.$ajax_path.'\';
                    </script>
                ';
                return $js;
            }
        }
    }

    public function getContent()
    {
        $this->list_img = $this->getListImg();
        if (Tools::isSubmit('ajax') && $action = Tools::getValue('action')) {
            $this->ajaxAction($action);
        }

        //  addCSS doesn't support versions, so we add js/css directly
        $this->context->controller->js_files[] = $this->_path.'views/js/back.js?v='.$this->version;
        $this->context->controller->css_files[$this->_path.'views/css/back.css?v='.$this->version] = 'all';

        $meta_types = $this->getFields('meta');
        $sorting_options = array(
            'main.id' => 'ID',
            'main.date_add' => $this->l('Date added'),
            'name' => $this->l('Name'),
        );
        foreach ($meta_types as $name => $t) {
            if ($name != 'img_legend') {
                $sorting_options[$name] = $t['name'];
            }
        }

        $this->context->smarty->assign(array(
            'shops_num' => count($this->shop_ids),
            'resources' => $this->getFields('resource'),
            'autofill_data' => $this->getAutofillData(),
            'meta_types' => $meta_types,
            'special_filters' => array(
                'product' => $this->getFields('product_filters'),
                'category' => $this->getFields('category_filters'),
            ),
            'sorting_options' => $sorting_options,
            'languages' => Language::getLanguages(false),
            'this' => $this,
            'documentation_link' => $this->_path.'readme_en.pdf',
        ));
        return $this->display(__FILE__, 'views/templates/admin/configure.tpl');
    }

    public function getListImg()
    {
        $type = $this->db->getRow('
            SELECT * FROM '._DB_PREFIX_.'image_type WHERE width < 150 AND width > 50 AND products = 1
        ');
        if (!$type) {
            $type = $this->db->getRow('SELECT * FROM '._DB_PREFIX_.'image_type WHERE products = 1');
        }
        return $type;
    }

    public function getAutofillData($decode = true)
    {
        $data = Configuration::get('MTG_AUTOFILL');
        if ($decode) {
            $data = Tools::jsonDecode($data, true);
        }
        return $data;
    }

    public function ajaxAction($action)
    {
        $ret = array();
        switch ($action) {
            case 'CallResourseList':
                $resource_type = Tools::getValue('resource_type');
                $id_lang = Tools::getValue('id_lang', $this->context->language->id);
                $patterns = $this->getSavedPatterns($resource_type, $id_lang, true);
                $ret['list'] = utf8_encode($this->ajaxCallResourceList($resource_type, $id_lang, $patterns));
                if (Tools::getValue('refresh_patterns') == 1) {
                    $ret['patterns'] = $patterns;
                }
                break;
            case 'GenerateMeta':
                $ret['updated_fields'] = $this->ajaxGenerateMeta();
                break;
            case 'SavePatterns':
                $id_lang = Tools::getValue('id_lang');
                $resource_type = Tools::getValue('resource_type');
                $patterns = Tools::getValue('patterns');
                $ret['saved'] = $this->savePatterns($id_lang, $resource_type, $patterns);
                if ($ret['saved']) {
                    $ret['savedTxt'] = utf8_encode($this->l('Patterns saved'));
                }
                break;
            case 'SaveAutoFillParams':
                $encoded_autofill_data = Tools::jsonEncode(Tools::getValue('autofill'));
                // update value for all shops
                $ret['saved'] = Configuration::updateGlobalValue('MTG_AUTOFILL', $encoded_autofill_data);
                break;
            case 'GetProductMetaFields':
                $id_product = Tools::getValue('id_product');
                $data = $this->db->executeS('
                    SELECT id_lang, meta_title, meta_description, meta_keywords
                    FROM '._DB_PREFIX_.'product_lang
                    WHERE id_product = '.(int)$id_product.'
                    AND id_shop = '.(int)$this->context->shop->id.'
                ');
                $meta_fields = array();
                foreach ($data as $d) {
                    $meta_fields[$d['id_lang']] = $d;
                }
                $ret['meta_fields'] = $meta_fields;
                break;
        }
        exit(Tools::jsonEncode($ret));
    }

    public function columnExists($table_name, $column_name, $prefix_included = true)
    {
        $table_name = $prefix_included ? $table_name : _DB_PREFIX_.$table_name;
        return (bool)$this->db->executeS('
            SHOW COLUMNS FROM '.pSQL($table_name).' LIKE \''.pSQL($column_name).'\'
        ');
    }

    public function tableExists($table_name, $prefix_included = true)
    {
        $table_name = $prefix_included ? $table_name : _DB_PREFIX_.$table_name;
        return (bool)$this->db->executeS('SHOW TABLES LIKE \''.pSQL($table_name).'\'');
    }

    public function ajaxCallResourceList($resource_type, $id_lang, $patterns)
    {
        $fields_list = array();
        if ($resource_type != 'cms') {
            $fields_list['name'] = $this->l('Name');
        }

        foreach ($patterns as $meta_name => $pattern) {
            if ($meta_name != 'img_legend' && !empty($pattern['active'])) {
                $fields_list[$meta_name] = $this->getFieldName('meta', $meta_name);
            }
        }

        if (in_array($resource_type, array('manufacturer', 'supplier'))) {
            unset($fields_list['link_rewrite']);
        }
        $order_by = Tools::getValue('order_by', current(array_keys($fields_list)));
        if ($order_by == 'main.id') {
            $order_by = 'main.id_'.$resource_type;
        }
        $order_way = Tools::getValue('order_way', 'ASC');
        $p = Tools::getValue('p', 1);
        $npp = $limit = Tools::getValue('npp', 10);
        $offset = ($p - 1) * $npp;
        $filters = array();
        foreach (array_keys($fields_list) as $f) {
            if ($filter = Tools::getValue($f)) {
                $filters[$f] = $filter;
            }
        }
        $identifier = bqSQL('id_'.$resource_type);
        $query = new DbQuery();
        // select will be defined later
        $query->from($resource_type, 'main');

        // retro-compatibility check
        if ($this->tableExists(_DB_PREFIX_.$resource_type.'_shop')) {
            $query->innerJoin(
                $resource_type.'_shop',
                'shop',
                'shop.`'.$identifier.'` = main.`'.$identifier.'`
                AND shop.`id_shop` IN ('.implode(', ', $this->shop_ids).')'
            );
        }

        $query->innerJoin(
            $resource_type.'_lang',
            'lang',
            'lang.`'.$identifier.'` = main.`'.$identifier.'`
            AND lang.`id_lang` = '.(int)$id_lang.'
            '.($this->columnExists(_DB_PREFIX_.$resource_type.'_lang', 'id_shop') ?
            'AND lang.`id_shop` = shop.`id_shop`' : '')
        );

        $additional_filters = array_keys($this->getFields($resource_type.'_filters'));
        $additional_filters[] = 'active';
        if ($resource_type == 'product') {
            $query->innerJoin(
                'category_product',
                'cp',
                'cp.`id_product` = main.`id_product`'
            );
        }

        // filter by manufacturer/supplier/category etc...
        foreach ($additional_filters as $name) {
            $value = Tools::getValue($name);
            if ($value != '-') {
                if ($this->columnExists(_DB_PREFIX_.$resource_type.'_shop', $name)) {
                    $name = 'shop.'.$name;
                }
                $query->where(pSQL($name).' = '.pSQL($value));
            }
        }

        if (is_array($filters) && $filters) {
            foreach ($filters as $name => $value) {
                $query->where('`'.bqSQL($name).'` LIKE \'%'.pSQL($value).'%\'');
            }
        }

        $total_query = $query;
        $total_query->select('COUNT(DISTINCT main.`'.$identifier.'`)');
        $total = $this->db->getValue($total_query);

        $query->select('main.`'.$identifier.'` AS id');
        foreach (array_keys($fields_list) as $field_name) {
            $query->select('`'.bqSQL($field_name).'`');
        }
        $query->orderBy(pSQL($order_by).' '.pSQL($order_way));
        $query->limit((int)$limit, (int)$offset);
        $query->groupBy('main.`'.$identifier.'`');
        $items = $this->db->executeS($query);

        if ($resource_type == 'product' && !empty($patterns['img_legend']['active'])) {
            $fields_list['img_legend'] = $this->l('Images');
            $images = $this->db->executeS('
                SELECT * FROM '._DB_PREFIX_.'image i
                INNER JOIN '._DB_PREFIX_.'image_lang il ON il.id_image = i.id_image
                WHERE il.id_lang = '.(int)$id_lang.'
            ');
            $sorted_images = array();
            foreach ($images as $img) {
                $sorted_images[$img['id_product']][$img['id_image']] = $img['legend'];
            }
            foreach ($items as &$i) {
                if (isset($sorted_images[$i['id']])) {
                    $i['img_legend'] = $sorted_images[$i['id']];
                }
            }
        }

        $this->context->smarty->assign(array(
            'fields_list' => $fields_list,
            'items' => $items,
            'total' => $total,
            'p' => $p,
            'npp' => $npp,
            'order_by' => $order_by,
            'order_way' => $order_way,
            'filters' => $filters,
            'this' => $this,
        ));
        $html = $this->display(__FILE__, 'views/templates/admin/resource-list.tpl');
        return $html;
    }

    public function savePatterns($id_lang, $resource_type, $patterns)
    {
        $saved_patterns = $this->getSavedPatterns();
        $saved_patterns[$id_lang][$resource_type] = $patterns;
        $saved_patterns = Tools::jsonEncode($saved_patterns);
        // update Value considering multishop
        return Configuration::updateValue('MTG_SAVED_PATTERNS', $saved_patterns);
    }

    public function getSavedPatterns($resource_type = false, $id_lang = false, $utf8_encode = false)
    {
        $saved_patterns = Configuration::get('MTG_SAVED_PATTERNS');
        $saved_patterns = $saved_patterns ? Tools::jsonDecode($saved_patterns, true) : array();
        if ($resource_type) {
            $languages = Language::getLanguages(false);
            foreach ($languages as $lang) {
                if ($id_lang && $id_lang != $lang['id_lang']) {
                    continue;
                }
                if (!empty($saved_patterns[$lang['id_lang']][$resource_type])) {
                    $saved_patterns[$lang['id_lang']] = $saved_patterns[$lang['id_lang']][$resource_type];
                } else {
                    $fields = $this->getFields('meta');
                    foreach ($fields as $meta_name => $f) {
                        $pattern = $f['patterns']['default'];
                        if (!empty($f['patterns'][$resource_type])) {
                            $pattern = $f['patterns'][$resource_type];
                        }
                        $saved_patterns[$lang['id_lang']][$meta_name]['active'] = 1;
                        $saved_patterns[$lang['id_lang']][$meta_name]['value'] = $pattern;
                        $saved_patterns[$lang['id_lang']][$meta_name]['length'] = $f['length'];
                    }
                }
                if ($utf8_encode) {
                    foreach ($saved_patterns[$lang['id_lang']] as &$p) {
                        $p['value'] = utf8_encode($p['value']);
                    }
                }
            }
            if ($id_lang && !empty($saved_patterns[$id_lang])) {
                $saved_patterns = $saved_patterns[$id_lang];
            }
        }
        return $saved_patterns;
    }

    public function hookActionObjectUpdateAfter($params)
    {
        $autofill_data = $this->getAutofillData();
        $obj = $params['object'];
        $resource_type = $this->getResourceType($obj);
        if (!empty($autofill_data[$resource_type]) && Validate::isLoadedObject($obj)) {
            $this->generateMeta($obj);
        }
    }

    public function hookActionObjectAddAfter($params)
    {
        $this->hookActionObjectUpdateAfter($params);
    }

    public function getResourceType($obj)
    {
        $resource_type = Tools::strtolower(get_class($obj));
        if ($resource_type == 'cmscategory') {
            $resource_type = 'cms_category';
        }
        return $resource_type;
    }

    public function generateMeta($obj, $params = array(), $throw_error = false)
    {
        $resource_type = $this->getResourceType($obj);
        $patterns = $this->getSavedPatterns($resource_type);
        $table_name = _DB_PREFIX_.$resource_type.'_lang';
        $identifier = 'id_'.$resource_type;
        $columns = array(
            'id_'.pSQL($resource_type),
            'id_lang',
            'meta_title',
            'meta_keywords',
            'meta_description',
        );
        if ($this->columnExists($table_name, 'link_rewrite')) {
            $columns[] = 'link_rewrite';
        }
        if ($this->columnExists($table_name, 'id_shop')) {
            $columns[] = 'id_shop';
        }
        $query = '
            SELECT '.pSQL(implode(', ', $columns)).'
            FROM '.pSQL($table_name).'
            WHERE '.pSQL($identifier).' = '.(int)$obj->id.'
        ';
        if (in_array('id_shop', $columns)) {
            $query .= ' AND id_shop IN ('.pSQL(implode(', ', $this->shop_ids)).')';
        }
        if (!empty($params['id_lang'])) {
            $query .= ' AND id_lang = '.(int)$params['id_lang'];
        }
        $current_meta_fields = $this->db->executeS($query);
        $update_rows = $keys_to_update = $updated_fields = $columns = array();
        foreach ($current_meta_fields as $f) {
            $id_lang = $f['id_lang'];
            $id_shop = !empty($f['id_shop']) ? $f['id_shop'] : 0;
            $row = array();
            foreach ($f as $meta_name => $meta_value) {
                $new_value = false;
                // add spaces, removed by AdminProductsController::_cleanMetaKeywords()
                if ($resource_type == 'product' && $meta_name == 'meta_keywords') {
                    $keywords = $this->formatMetaKeywords($meta_value);
                    if ($keywords != $meta_value) {
                        $new_value = $keywords;
                    }
                }
                if (!empty($params['forced_values'])) {
                    if (!empty($params['forced_values'][$meta_name])) {
                        $new_value = $params['forced_values'][$meta_name];
                    }
                } elseif ((!empty($params['overwrite_fields']) || !$meta_value)
                    && !empty($patterns[$id_lang][$meta_name]['active'])) {
                    $pattern = $patterns[$id_lang][$meta_name];
                    if (empty($pattern['length']) && $throw_error) {
                        $msg = sprintf(
                            $this->l('Please, specify max length for %s'),
                            $this->getFieldName('meta', $meta_name)
                        );
                        $this->throwError($msg);
                    }
                    $new_value = $this->processPattern(
                        $meta_name,
                        $pattern,
                        $resource_type,
                        $obj->id,
                        $id_lang,
                        $id_shop
                    );
                }

                if ($new_value) {
                    $validate_msg = $obj->validateField($meta_name, $new_value, $id_lang, array(), true);
                    if ($validate_msg === true) {
                        $meta_value = $new_value;
                        $keys_to_update[$meta_name] = pSQL($meta_name).' = VALUES('.pSQL($meta_name).')';
                        if ($id_shop == $this->context->shop->id || empty($updated_fields[$meta_name])) {
                            $updated_fields[$meta_name] = utf8_encode($new_value);
                        }
                    } elseif ($throw_error) {
                        $txt = $resource_type.' [id='.$obj->id.']: '.$validate_msg.': "'.$new_value.'"';
                        $this->throwError($txt);
                    }
                }
                $row[] = '\''.pSQL($meta_value).'\'';
                $columns[$meta_name] = pSQL($meta_name);
            }
            $update_rows[] = '('.implode(', ', $row).')';

            if ($resource_type == 'product' &&
                (empty($params['forced_values']) || !empty($params['forced_legend_id'])) &&
                (!empty($patterns[$id_lang]['img_legend']['active']) || !empty($params['forced_values']['legend']))) {
                // process image legends
                if (!empty($params['forced_values']['legend']) && !empty($params['forced_legend_id'])) {
                    $legend = $params['forced_values']['legend'];
                    $images = array(array('id_image' => (int)$params['forced_legend_id']));
                } else {
                    $pattern = $patterns[$id_lang]['img_legend'];
                    if (empty($pattern['length']) && $throw_error) {
                        $msg = sprintf($this->l('Please, specify max length for %s'), $this->l('Image legend'));
                        $this->throwError($msg);
                    }
                    $legend = $this->processPattern('legend', $pattern, $resource_type, $obj->id, $id_lang, $id_shop);
                    $images = $obj->getImages($id_lang);
                }
                foreach ($images as $img) {
                    if (!(int)$img['id_image']) {
                        continue;
                    }
                    $image = new Image((int)$img['id_image']);
                    if ((empty($params['overwrite_fields']) && $image->legend[$id_lang]) || !$legend) {
                        continue;
                    }
                    $image->legend[$id_lang] = $legend;
                    $this->saveObject($image, $id_lang);
                    $updated_fields['img_legend_'.$image->id] = utf8_encode($legend);
                }
            }
        }

        if ($keys_to_update && $columns && $update_rows) {
            $sql = '
                INSERT INTO '.pSQL($table_name).'
                ('.implode(', ', $columns).')
                VALUES '.implode(', ', $update_rows).'
                ON DUPLICATE KEY UPDATE '.implode(', ', $keys_to_update).'
            ';
            try {
                $this->db->execute($sql);
            } catch (Exception $e) {
                if ($throw_error) {
                    $this->throwError($e->getMessage());
                }
                unset($e);
            }
        }
        return $updated_fields;
    }

    public function formatMetaKeywords($string)
    {
        $keywords = array();
        $exploded_value = explode(',', $string);
        foreach ($exploded_value as $v) {
            if ($v = trim($v)) {
                $keywords[$v] = Tools::strtolower($v);
            }
        }
        return  implode(', ', $keywords);
    }

    public function ajaxGenerateMeta()
    {
        $resource_type = Tools::getValue('resource_type');
        $class_name = Tools::ucfirst($resource_type);
        foreach ($this->getFields('resource') as $k => $r) {
            if ($k == $resource_type) {
                $class_name = $r['class_name'];
            }
        }
        $this->verifyClass($class_name);
        $id = Tools::getValue('id');
        $obj = new $class_name($id);
        $params = array(
            'id_lang' => Tools::getValue('id_lang'),
            'overwrite_fields' => Tools::getValue('overwrite_fields'),
        );
        if (Tools::isSubmit('forced_meta')) {
            $params['overwrite_fields'] = 1;
            $params['forced_values'] = array(
                Tools::getValue('forced_meta') => Tools::getValue('forced_value'),
            );
            if ($forced_legend_id = Tools::getValue('forced_legend_id')) {
                $params['forced_legend_id'] = $forced_legend_id;
            }
        }
        return $this->generateMeta($obj, $params, true);
    }

    public function getClearDescription($description)
    {
        $description = str_replace('<', ' <', $description); // trick for keeping spaces after strip_tags
        if (method_exists('Tools', 'getDescriptionClean')) {
            $description = Tools::getDescriptionClean($description); // strip_tags + stripslashes
        } else {
            $description = strip_tags(stripslashes($description)); // retro-compatibility
        }

        $description = preg_replace("/\r\n|\r|\n/", '[temporary_new_line_marker]', $description); // mark new lines

        // remove spaces before punctuation
        $punctuation = array(' .' => '.', ' ,' => ',', ' !' => '!', ' ?' => '?', ' ;' => ';', ' :' => ':');
        $description = str_replace(array_keys($punctuation), $punctuation, $description);
        $description =  preg_replace('/,+/', ',', $description); // remove repeating commas

        $description = explode('[temporary_new_line_marker]', $description);
        $string = '';
        if (count($description) > 1) {
            foreach ($description as $fragment) {
                // trim all spaces including &nbsp;
                if ($fragment = trim(html_entity_decode(str_replace('&nbsp;', ' ', htmlentities($fragment))))) {
                    $string .= iconv('UTF-8', 'UTF-8//IGNORE', $fragment); // remove unrecognized characters
                    // add dot if last character is not puctuiation
                    if (!preg_match('/[^\w\s]/u', Tools::substr($fragment, -1))) {
                        $string .= '.';
                    }
                    $string .= ' ';
                }
            }
        } elseif (!empty($description[0])) {
            $string .= $description[0];
        }
        $string = trim(preg_replace('/\s+/', ' ', $string)); // remove whitespace
        $string = str_replace(array('<', '>', '=', '{', '}'), '', $string); // pass isGenericName validation
        return $string;
    }

    public function processPattern($meta_type, $pattern, $resource_type, $id_resource, $id_lang, $id_shop)
    {
        $replacements = $this->getPatternVariables($resource_type, $id_resource, $id_lang, $id_shop);

        if ($meta_type == 'meta_keywords') {
            foreach ($replacements as $from => $to) {
                if (!in_array($from, array('{auto_keywords}', '{taglist}', '{name}'))) {           //Britoff - name!!
                    // make sure plain text, like {description} is split in separate words
                    $keywords = array();
                    $to = preg_replace('/[^\w\s]/u', ' ', $to);
                    $to = explode(' ', Tools::strtolower($to));
                    foreach ($to as $k) {
                        if ($k = trim($k)) {
                            $keywords[] = $k;
                        }
                    }
                    $replacements[$from] = implode(', ', $keywords);
                }
            }
        }

        $text = str_replace(array_keys($replacements), array_values($replacements), $pattern['value']);
        $text = preg_replace('/\{[^)]+\}/', '', $text); // remove {variables} that were not converted
        $truncate_options = array('ellipsis' => '', 'exact' => false);
        $text = Tools::truncateString($text, $pattern['length'], $truncate_options);
        $text = trim($text, ','); // remove trailing commas

        if ($meta_type == 'link_rewrite') {
            $text = Tools::str2url($text);
            $text = Tools::substr($text, 0, $pattern['length']);
        } elseif ($meta_type == 'meta_keywords') {
            // remove possible keyword duplicates
            $text = $this->formatMetaKeywords($text);
        }

        return $text;
    }

    public function saveObject($obj, $id_lang)
    {
        $ret = true;
        // image object has to be processed differently
        if (get_class($obj) == 'Image') {
            $legend = $obj->legend[$id_lang];
            if (Validate::isGenericName($legend)) {
                $ret = $this->db->execute('
                    UPDATE '._DB_PREFIX_.'image_lang
                    SET legend = \''.pSQL($legend).'\'
                    WHERE id_image = '.(int)$obj->id.' AND id_lang = '.(int)$id_lang.'
                ');
            }
        } else {
            try {
                $obj->save();
            } catch (Exception $e) {
                $msg = $this->getObjectName($obj, $id_lang).': '.$e->getMessage();
                $this->throwError($msg);
            }
        }
        return $ret;
    }

    public function getObjectName($obj, $id_lang)
    {
        $name_field = (get_class($obj) == 'CMS') ? 'meta_title' : 'name';
        $name = $obj->$name_field ? $obj->$name_field : '[ID='.$obj->id.']';
        $name = is_array($name) ? $name[$id_lang] : $name;
        return $name;
    }

    public $saved_resource_data = array();

    public function getPatternVariables($resource_type, $id_resource, $id_lang, $id_shop)
    {
        $imploded_shop_ids = $id_shop ? $id_shop : implode(', ', $this->shop_ids);
        $saved_data_identifier = $resource_type.'-'.$id_resource.'-'.(int)$id_shop.'-'.(int)$id_lang;
        if (empty($this->saved_resource_data[$saved_data_identifier])) {
            $table_name = _DB_PREFIX_.$resource_type;
            $identifier = 'id_'.$resource_type;
            $query = 'SELECT * FROM '.pSQL($table_name).' main';
            if ($this->tableExists($table_name.'_shop')) {
                $query .= ' INNER JOIN '.pSQL($table_name).'_shop shop
                    ON main.'.pSQL($identifier).' = shop.'.pSQL($identifier).'
                    AND shop.id_shop IN ('.pSQL($imploded_shop_ids).')';
            }
            $query .= ' INNER JOIN '.pSQL($table_name).'_lang lang
                ON lang.'.pSQL($identifier).' = main.'.pSQL($identifier).'
                AND lang.id_lang = '.(int)$id_lang.'
                '.($this->columnExists($table_name.'_lang', 'id_shop') ? 'AND lang.id_shop = shop.id_shop' : '').'
                WHERE main.'.pSQL($identifier).' = '.(int)$id_resource;
            $data = $this->db->getRow($query);
            $this->saved_resource_data[$saved_data_identifier] = $data;
        }
        $available_variables = $processed_variables = array();
        foreach ($this->getFields('resource') as $type => $f) {
            if ($type == $resource_type) {
                $available_variables = $f['variables'];
                break;
            }
        }

        foreach ($available_variables as $v) {
            $processed_later = array('attributes_xx' => 1, 'feature_xx' => 1, 'colorlist' => 1);
            if (isset($processed_later[$v])) {
                continue;
            }
            $new_value = '';
            switch ($v) {
                case 'name':
                case 'reference':
                case 'ean13':
                case 'meta_title':
                case 'description':
                case 'description_short':
                case 'content':
                    $key = ($resource_type != 'manufacturer' || $v != 'description_short') ? $v : 'short_description';
                    if (!empty($this->saved_resource_data[$saved_data_identifier][$key])) {
                        $new_value = $this->saved_resource_data[$saved_data_identifier][$key];
                    }
                    break;
                case 'manufacturer':
                case 'supplier':
                    $c = Tools::ucfirst($v);
                    $key = 'id_'.$v;
                    if (!empty($this->saved_resource_data[$saved_data_identifier][$key])) {
                        $id = $this->saved_resource_data[$saved_data_identifier][$key];
                        $new_value = $c::getNameById($id);
                    }
                    break;
                case 'category':
                    $sql = '
                        SELECT cl.name
                        FROM '._DB_PREFIX_.'product_shop ps
                        INNER JOIN '._DB_PREFIX_.'category_lang cl
                            ON ps.id_category_default = cl.id_category
                            AND cl.id_shop = ps.id_shop
                            AND cl.id_lang = '.(int)$id_lang.'
                        WHERE ps.id_product = '.(int)$id_resource.'
                        AND ps.id_shop IN ('.pSQL($imploded_shop_ids).')
                    ';
                    $new_value = $this->db->getValue($sql);
                    break;
                case 'attributelist':
                case 'featurelist':
                    $var = str_replace('list', '', $v);
                    $method_name = 'getProduct'.Tools::ucfirst($var).'s';
                    $rows = $this->$method_name($id_resource, $id_lang, $imploded_shop_ids);
                    $sorted = array();
                    foreach ($rows as $r) {
                        $id_group = $r['id_group'];
                        if (!isset($sorted[$id_group])) {
                            $sorted[$id_group] = array(
                                'name' => $r['group_name'],
                                'is_color' => !empty($r['is_color_group']),
                                'values' => array(),
                            );
                        }
                        $sorted[$id_group]['values'][] = Tools::strtolower($r['name']);
                    }
                    $string = '';
                    foreach ($sorted as $id_group => $group) {
                        $imploded_values = implode(', ', $group['values']);
                        $string .= $group['name'].': '.$imploded_values.'; ';

                        // {attributes_xx}, {feature_xx}
                        $key = $var.($var == 'attribute' ? 's' : '').'_'.$id_group;
                        $processed_variables['{'.$key.'}'] = $imploded_values;

                        // {colorlist}
                        if ($group['is_color']) {
                            if (!isset($processed_variables['{colorlist}'])) {
                                $processed_variables['{colorlist}'] = $imploded_values;
                            } else {
                                $processed_variables['{colorlist}'] .= ', '.$imploded_values;
                            }
                        }
                    }
                    $new_value = trim($string, '; ');
                    break;
                case 'taglist':
                    $new_value = $this->getProductTags($id_resource, $id_lang, true);
                    break;
                case 'auto_keywords':
                    $table_name = _DB_PREFIX_.$resource_type;
                    $columns = ($resource_type != 'cms') ? 'name, description' : 'meta_title, content';
                    // manufactuer name is stored in main table and is not multilingual, so select from main and lang
                    $text = $this->db->getRow('
                        SELECT '.pSQL($columns).' FROM '.pSQL($table_name).' main
                        INNER JOIN '.pSQL($table_name).'_lang lang
                            ON lang.id_'.pSQL($resource_type).' = main.id_'.pSQL($resource_type).'
                        WHERE main.id_'.pSQL($resource_type).' = '.(int)$id_resource.'
                        AND lang.id_lang = '.(int)$id_lang.'
                        '.($this->columnExists($table_name.'_lang', 'id_shop') ?
                        'AND lang.id_shop IN ('.pSQL($imploded_shop_ids).')' :'').'
                    ');
                    $text = implode(' ', $text);
                    $text = $this->getClearDescription($text);
                    // remove punctuation
                    $text = preg_replace('/[^\w\s]/u', ' ', $text);
                    $text = explode(' ', Tools::strtolower($text));
                    $auto_keywords = array();
                    $exceptions = $this->getKeywordsExceptions($id_lang);
                    foreach ($text as $t) {
                        if (Tools::strlen($t) > 3
                            && !in_array($t, $exceptions)
                            && count($auto_keywords) < 20) {
                            $auto_keywords[$t] = isset($auto_keywords[$t]) ? $auto_keywords[$t] + 1 : 1;
                        }
                    }

                    if ($resource_type == 'product') {
                        $tags = $this->getProductTags($id_resource, $id_lang);
                        $max = max($auto_keywords) + 1;
                        foreach ($tags as $tag) {
                            $auto_keywords[$tag] = $max++;
                        }
                    }

                    // shuffle preserving keys
                    $shuffled = array();
                    $keys = array_keys($auto_keywords);
                    shuffle($keys);
                    foreach ($keys as $key) {
                        $shuffled[$key] = $auto_keywords[$key];
                    }
                    $auto_keywords = $shuffled;
                    arsort($auto_keywords);
                    $auto_keywords = implode(', ', array_keys($auto_keywords));
                    $new_value = trim($auto_keywords, ',');
                    break;
                case 'shop_name':
                    $id_shop = $id_shop ? $id_shop : $this->context->shop->id;
                    $new_value = $this->db->getValue('
                        SELECT name FROM '._DB_PREFIX_.'shop WHERE id_shop = '.(int)$id_shop.'
                    ');
                    break;
            }
            $processed_variables['{'.$v.'}'] = $this->getClearDescription($new_value);
        }
        if ($resource_type == 'product') {
            foreach ($this->getPriceVariables() as $var) {
                $iso_code = str_replace('price_', '', $var);
                $id_currency = Currency::getIdByIsoCode($iso_code);
                $currency = new Currency($id_currency);
                $price = Product::getPriceStatic($id_resource);
                $price = Tools::convertPrice($price, $currency);
                $c_decimals = (int)$currency->decimals * _PS_PRICE_DISPLAY_PRECISION_;
                $price = Tools::ps_round($price, $c_decimals);
                $processed_variables['{'.$var.'}'] = $price;
            }
        }
        // d($processed_variables);
        return $processed_variables;
    }

    public function getProductFeatures($id_product, $id_lang, $imploded_shop_ids)
    {
        $features = $this->db->executeS('
            SELECT DISTINCT fp.id_feature_value,
            fp.id_feature AS id_group,
            fvl.value AS name,
            fl.name AS group_name
            FROM '._DB_PREFIX_.'feature_product fp
            INNER JOIN '._DB_PREFIX_.'feature_shop fs
                ON fs.id_feature = fp.id_feature AND fs.id_shop IN ('.pSQL($imploded_shop_ids).')
            INNER JOIN '._DB_PREFIX_.'feature_lang fl
                ON fp.id_feature = fl.id_feature AND fl.id_lang = '.(int)$id_lang.'
            INNER JOIN '._DB_PREFIX_.'feature_value_lang fvl
                ON fp.id_feature_value = fvl.id_feature_value AND fvl.id_lang = '.(int)$id_lang.'
            WHERE fp.id_product = '.(int)$id_product.'
        ');
        return $features;
    }

    public function getProductAttributes($id_product, $id_lang, $imploded_shop_ids)
    {
        $attributes = $this->db->executeS('
            SELECT DISTINCT al.id_attribute,
            ag.id_attribute_group AS id_group,
            ag.is_color_group,
            al.name,
            agl.public_name AS group_name
            FROM '._DB_PREFIX_.'product_attribute pa
            INNER JOIN '._DB_PREFIX_.'product_attribute_shop pas
                ON pas.id_product_attribute = pa.id_product_attribute
                AND pas.id_shop IN ('.pSQL($imploded_shop_ids).')
            INNER JOIN '._DB_PREFIX_.'product_attribute_combination pac
                ON pac.id_product_attribute = pa.id_product_attribute
            INNER JOIN '._DB_PREFIX_.'attribute a
                ON a.id_attribute = pac.id_attribute
            INNER JOIN '._DB_PREFIX_.'attribute_shop a_shop
                ON a_shop.id_attribute = a.id_attribute
                AND a_shop.id_shop IN ('.pSQL($imploded_shop_ids).')
            INNER JOIN '._DB_PREFIX_.'attribute_lang al
                ON al.id_attribute = a.id_attribute AND al.id_lang = '.(int)$id_lang.'
            INNER JOIN '._DB_PREFIX_.'attribute_group ag
                ON ag.id_attribute_group = a.id_attribute_group
            INNER JOIN '._DB_PREFIX_.'attribute_group_lang agl
                ON agl.id_attribute_group = a.id_attribute_group AND agl.id_lang = '.(int)$id_lang.'
             WHERE pa.id_product = '.(int)$id_product.'
        ');
        return $attributes;
    }

    public function getProductTags($id_product, $id_lang, $implode = false)
    {
        $tags = $this->db->executeS('
            SELECT t.name
            FROM '._DB_PREFIX_.'tag t
            INNER JOIN '._DB_PREFIX_.'product_tag pt
                ON pt.id_tag = t.id_tag
                AND pt.id_product = '.(int)$id_product.'
                AND t.id_lang = '.(int)$id_lang.'
        ');
        foreach ($tags as &$t) {
            $t = $t['name'];
        }
        if ($implode) {
            $tags = implode(', ', $tags);
        }
        return $tags;
    }

    public function getSrcById($id_image, $img_type)
    {
        $src = _THEME_PROD_DIR_.Image::getImgFolderStatic($id_image);
        $src .= $id_image.'-'.$img_type.'.jpg';
        return $src;
    }

    public function implodePatternVariables($variables)
    {
        return '{'.implode('}, {', $variables).'}';
    }

    public function getObjectFieldValue($obj, $id_lang, $field_name)
    {
        $value = is_array($obj->$field_name) ? $obj->{$field_name}[$id_lang] : $obj->$field_name;
        return $value;
    }

    public function setObjectFieldValue(&$obj, $id_lang, $field_name, $field_value)
    {
        if (is_array($obj->$field_name)) {
            $obj->{$field_name}[$id_lang] = $field_value;
        } else {
            $obj->$field_name = $field_value;
        }
    }

    public function getPriceVariables()
    {
        $variables = array();
        $currencies = Currency::getCurrencies(false, false);
        foreach ($currencies as $c) {
            $variables[$c['iso_code']] = 'price_'.Tools::strtolower($c['iso_code']);
        }
        return $variables;
    }

    public function getFields($type)
    {
        $id_lang = $this->context->language->id;
        $fields = array();
        switch ($type) {
            case 'resource':
                $fields = array(
                    'product' => array(
                        'name' => $this->l('Products'),
                        'variables' => array('name', 'reference', 'ean13', 'category', 'manufacturer', 'supplier',
                        'description', 'description_short', 'attributelist', 'attributes_xx', 'featurelist',
                        'feature_xx',  'colorlist', 'taglist', 'auto_keywords', 'shop_name'),
                        'class_name' => 'Product',
                    ),
                    'category' => array(
                        'name' => $this->l('Categories'),
                        'variables' => array('name', 'description', 'auto_keywords', 'shop_name'),
                        'class_name' => 'Category',
                    ),
                    'cms' => array(
                        'name' => $this->l('CMS pages'),
                        'variables' => array('meta_title', 'content', 'auto_keywords', 'shop_name'),
                        'class_name' => 'CMS',
                    ),
                    'cms_category' => array(
                        'name' => $this->l('CMS categories'),
                        'variables' => array('name', 'description', 'auto_keywords', 'shop_name'),
                        'class_name' => 'CMSCategory',
                    ),
                    'manufacturer' => array(
                        'name' => $this->l('Manufacturers'),
                        'variables' => array('name', 'description', 'description_short', 'auto_keywords', 'shop_name'),
                        'class_name' => 'Manufacturer',
                    ),
                    'supplier' => array(
                        'name' => $this->l('Suppliers'),
                        'variables' => array('name', 'description', 'auto_keywords', 'shop_name'),
                        'class_name' => 'Supplier',
                    ),
                );
                foreach ($this->getPriceVariables() as $var) {
                    $fields['product']['variables'][] = $var;
                }
                break;
            case 'meta':
                $fields = array(
                    'meta_title' => array (
                        'name' => $this->l('Meta title'),
                        'class' => 'has-exclusions not-for-cms',
                        'patterns' => array(
                            'product' => $this->l('Buy {name}'),
                            'cms' => '{meta_title}',
                            'default' => '{name}'
                        ),
                        'length' => 50,
                    ),
                    'link_rewrite' => array (
                        'name' => $this->l('Friendly URL'),
                        'class' => 'has-exclusions not-for-manufacturer not-for-supplier',
                        'patterns' => array(
                            'cms' => '{meta_title}',
                            'default' => '{name}',
                        ),
                        'length' => 50,
                    ),
                    'meta_description' => array (
                        'name' => $this->l('Meta description'),
                        'patterns' => array(
                            'cms' => '{content}',
                            'default' => '{description}'
                        ),
                        'length' => 140,
                    ),
                    'meta_keywords' => array (
                        'name' => $this->l('Meta keywords'),
                        'patterns' => array(
                            'product' => $this->l('buy, {taglist}, {auto_keywords}'),
                            'default' => '{auto_keywords}'
                        ),
                        'length' => 128,
                    ),
                    'img_legend' => array (
                        'name' => $this->l('Image legends'),
                        'patterns' => array(
                            'default' => '{name}'
                        ),
                        'class' => 'special-option product-option',
                        'length' => 64,
                    ),
                );
                break;
            case 'product_filters':
                $fields = array(
                    'id_category' => array(
                        'name' => $this->l('category'),
                        'options' => $this->getCategoryOptions($id_lang),
                    ),
                    'id_manufacturer' => array(
                        'name' => $this->l('manufacturer'),
                        'options' => $this->getManufacturerOptions($id_lang),
                    ),
                );
                break;
            case 'category_filters':
                $fields = array(
                    'id_parent' => array(
                        'name' => $this->l('parent category'),
                        'options' => $this->getCategoryParentsOptions($id_lang),
                    ),
                );
                break;
        }
        return $fields;
    }

    public function getKeywordsExceptions($id_lang)
    {
        $lang_iso = Language::getIsoById($id_lang);
        $exceptions = array(
            'en' => array('with', 'which', 'since', 'into', 'much', 'each', 'what', 'your', 'when', 'only', 'that',
            'very', 'most'),
        );
        return !empty($exceptions[$lang_iso]) ? $exceptions[$lang_iso] : array();
    }

    public function getManufacturerOptions($id_lang)
    {
        $manufacturers = Manufacturer::getManufacturers(false, $id_lang, false, false, false, false, true);
        $manufacturer_options = array();
        foreach ($manufacturers as $m) {
            $manufacturer_options[$m['id_manufacturer']] = $m['name'];
        }
        return $manufacturer_options;
    }

    public function getCategoryOptions($id_lang, $id_parent = false, $include_last_children = true)
    {
        if (!$id_parent) {
            $id_parent = $this->db->getValue('SELECT id_category FROM '._DB_PREFIX_.'category WHERE id_parent = 0');
        }
        $result = $this->db->executeS('
            SELECT c.id_category, c.id_parent, cl.name, c.level_depth
            FROM '._DB_PREFIX_.'category c
            '.Shop::addSqlAssociation('category', 'c').'
            LEFT JOIN '._DB_PREFIX_.'category_lang cl
                ON c.id_category = cl.id_category AND cl.id_shop IN ('.implode(', ', $this->shop_ids).')
            WHERE id_lang = '.(int)$id_lang.'
            AND id_parent = '.(int)$id_parent.'
        ');
        $sorted = array();
        if ($result) {
            $prefix = ' ';
            for ($i = 1; $i < $result[0]['level_depth']; $i++) {
                $prefix .= '-';
            }
            foreach ($result as $r) {
                $id_category = $r['id_category'];
                $sorted[$id_category] = trim($prefix.$r['name']);
                if ($children = $this->getCategoryOptions($id_lang, $id_category, $include_last_children)) {
                    foreach ($children as $id_cat => $name) {
                        $sorted[$id_cat] = $name;
                    }
                } elseif (!$include_last_children) {
                    $sorted[$id_category] = 'remove';
                }
            }
        }
        return $sorted;
    }

    public function getCategoryParentsOptions($id_lang)
    {
        $sorted_categories = $this->getCategoryOptions($id_lang, false, false);
        foreach ($sorted_categories as $id_cat => $name) {
            if ($name == 'remove') {
                unset($sorted_categories[$id_cat]);
            }
        }
        return $sorted_categories;
    }

    public function getFieldName($type, $key)
    {
        $fields = $this->getFields($type);
        $result = !empty($fields[$key]) ? $fields[$key] : $key;
        if (is_array($result) && !empty($result['name'])) {
            $result = $result['name'];
        }
        return $result;
    }

    public function verifyClass($class_name, $throw_error = true)
    {
        $ret = class_exists($class_name);
        if (!$ret && $throw_error) {
            $this->throwError(sprintf($this->l('Class %s is not available'), $class_name));
        }
        return $ret;
    }

    public function throwError($errors, $render_html = true)
    {
        if (!is_array($errors)) {
            $errors = array($errors);
        }
        $html = '<div class="thrown-errors">'.$this->displayError(implode('<br>', $errors)).'</div>';
        if (!Tools::isSubmit('ajax')) {
            return $html;
        } elseif ($render_html) {
            $errors = utf8_encode($html);
        }
        die(Tools::jsonEncode(array('errors' => $errors)));
    }
}
