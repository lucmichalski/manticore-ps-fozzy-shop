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

use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;
use PrestaShop\PrestaShop\Adapter\Category\CategoryProductSearchProvider;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchResult;
if (!defined('_PS_VERSION_')) {
    exit;
}
class pm_advancedsearch4advancedsearch4ModuleFrontController extends AdvancedSearchProductListingFrontController
{
    private $idSeo = false;
    private $idSearch = false;
    private $searchInstance;
    protected $context;
    public $display_column_left = true;
    public $display_column_right = true;
    protected $display_header = true;
    protected $display_footer = true;
    private $criterions                = array();
    private $criterions_hidden        = array();
    private $next_id_criterion_group    = false;
    private $reset                    = false;
    public function __construct()
    {
        parent::__construct();
        if (Tools::getValue('ajaxMode')) {
            $this->ajax = true;
            $this->display_column_left = false;
            $this->display_column_right = false;
            $this->display_header = false;
            $this->display_footer = false;
        }
    }
    public function init()
    {
        if (!isset($this->module) || !is_object($this->module)) {
            $this->module = Module::getInstanceByName('pm_advancedsearch4');
        }
        $this->idSearch = (int)Tools::getValue('id_search');
        $this->searchInstance = new AdvancedSearchClass((int)$this->idSearch, (int)$this->context->language->id);
        parent::init();
        $this->setSEOTags();
        $this->setCriterions();
        $this->setProductFilterList();
        $this->processActions();
        $this->doProductSearch('');
    }
    private function setProductFilterList()
    {
        $productFilterListSource = Tools::getValue('productFilterListSource');
        if (in_array($productFilterListSource, As4SearchEngine::$validPageName)) {
            As4SearchEngine::$productFilterListSource = $productFilterListSource;
            if ($productFilterListSource == 'search' || $productFilterListSource == 'jolisearch' || $productFilterListSource == 'module-ambjolisearch-jolisearch') {
                $productFilterListData = AdvancedSearchCoreClass::getDataUnserialized(Tools::getValue('productFilterListData'));
                if ($productFilterListData !== false) {
                    As4SearchEngine::$productFilterListData = $productFilterListData;
                }
            }
            $this->module->setProductFilterContext();
        }
    }
    private function setSEOTags()
    {
        $this->idSeo = Tools::getValue('id_seo', false);
        if (Tools::getValue('ajaxMode')) {
            if (!headers_sent()) {
                header('X-Robots-Tag: noindex, nofollow', true);
            }
            $this->context->smarty->assign(array(
                'nofollow' => true,
                'nobots' => true,
            ));
        } elseif (Tools::getValue('only_products')) {
            if ($this->idSeo && (Tools::getValue('p') || Tools::getValue('n'))) {
                header('X-Robots-Tag: noindex, follow', true);
            } else {
                header('X-Robots-Tag: noindex, nofollow', true);
            }
            $this->context->smarty->assign(array(
                'nofollow' => true,
                'nobots' => true,
            ));
        }
    }
    private function setCriterions()
    {
        $this->idSearch = (int)Tools::getValue('id_search');
        $this->searchInstance = new AdvancedSearchClass((int)$this->idSearch, (int)$this->context->language->id);
        $this->criterions = Tools::getValue('as4c', array());
        if (is_array($this->criterions)) {
            $this->criterions = As4SearchEngine::cleanArrayCriterion($this->criterions);
        } else {
            $this->criterions = array();
        }
        $this->criterions_hidden = Tools::getValue('as4c_hidden', array());
        if (is_array($this->criterions_hidden)) {
            $this->criterions_hidden = As4SearchEngine::cleanArrayCriterion($this->criterions_hidden);
        } else {
            $this->criterions_hidden = array();
        }
        $this->reset = (int)Tools::getValue('reset', false);
        $this->reset_group = (int)Tools::getValue('reset_group', false);
        if ($this->reset) {
            $this->criterions = array();
        }
        if ($this->reset_group && isset($this->criterions[$this->reset_group])) {
            unset($this->criterions[$this->reset_group]);
            if ($this->searchInstance->step_search) {
                $criterionsGroups = AdvancedSearchCriterionGroupClass::getCriterionsGroupsFromIdSearch((int)$this->idSearch, (int)$this->context->language->id, false);
                if (AdvancedSearchCoreClass::_isFilledArray($criterionsGroups)) {
                    $deleteAfter = false;
                    foreach ($criterionsGroups as $criterionGroup) {
                        if ((int)$criterionGroup['id_criterion_group'] == $this->reset_group) {
                            $deleteAfter = true;
                        }
                        if ($deleteAfter && isset($this->criterions[(int)$criterionGroup['id_criterion_group']])) {
                            unset($this->criterions[(int)$criterionGroup['id_criterion_group']]);
                        }
                    }
                }
            }
        }
        if ($this->searchInstance->filter_by_emplacement) {
            $criterionsFromEmplacement = As4SearchEngine::getCriteriaFromEmplacement($this->searchInstance->id, $this->searchInstance->id_category_root);
            foreach ($criterionsFromEmplacement as $idCriterionGroup => $idCriterionList) {
                if (!isset($this->criterions[$idCriterionGroup])) {
                    $this->criterions[$idCriterionGroup] = $idCriterionList;
                } elseif (is_array($this->criterions[$idCriterionGroup]) && !sizeof($this->criterions[$idCriterionGroup])) {
                    $this->criterions[$idCriterionGroup] = $idCriterionList;
                }
            }
        }
        $this->next_id_criterion_group = (int)Tools::getValue('next_id_criterion_group', false);
        $this->context->cookie->{'next_id_criterion_group_'.(int)$this->idSearch} = $this->next_id_criterion_group;
    }
    public function processActions()
    {
        if (Tools::getValue('setHideCriterionStatus')) {
            ob_end_clean();
            $this->idSearch = (int)Tools::getValue('id_search');
            $state = (int)Tools::getValue('state') > 0;
            if (isset($this->context->cookie->hidden_criteria_state)) {
                $hidden_criteria_state = unserialize($this->context->cookie->hidden_criteria_state);
                if (is_array($hidden_criteria_state)) {
                    $hidden_criteria_state[$this->idSearch] = $state;
                } else {
                    $hidden_criteria_state = array();
                }
                $this->context->cookie->hidden_criteria_state = serialize($hidden_criteria_state);
            } else {
                $this->context->cookie->hidden_criteria_state = serialize(array($this->idSearch => $state));
            }
            die;
        }
    }
    public function getSearchEngine()
    {
        return $this->searchInstance;
    }
    public function getCriterionsList()
    {
        return $this->criterions;
    }
    public function getHiddenCriterionsList()
    {
        return $this->criterions_hidden;
    }
    public function getCanonicalURL()
    {
        return As4SearchEngine::generateURLFromCriterions($this->getSearchEngine()->id, $this->getCriterionsList());
    }
    public function getListingLabel()
    {
        return $this->getTranslator()->trans('Search results', array(), 'Shop.Theme.Catalog');
    }
    protected function updateQueryString(array $extraParams = null)
    {
        if ($extraParams === null) {
            $extraParams = array();
        }
        return As4SearchEngine::generateURLFromCriterions($this->getSearchEngine()->id, $this->getCriterionsList(), null, $extraParams);
    }
}
