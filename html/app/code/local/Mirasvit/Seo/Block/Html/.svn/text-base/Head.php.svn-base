<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Advanced SEO Suite
 * @version   1.1.1
 * @build     899
 * @copyright Copyright (C) 2015 Mirasvit (http://mirasvit.com/)
 */


if (Mage::helper('mstcore')->isModuleInstalled('CorlleteLab_Imagezoom') && class_exists('CorlleteLab_Imagezoom_Block_Html_Head')) {
    abstract class Mirasvit_Seo_Block_Html_Head_Abstract extends CorlleteLab_Imagezoom_Block_Html_Head {

    }
} elseif (Mage::helper('core')->isModuleEnabled('Fooman_Speedster') && class_exists('Fooman_Speedster_Block_Page_Html_Head')) {
    abstract class Mirasvit_Seo_Block_Html_Head_Abstract extends Fooman_Speedster_Block_Page_Html_Head {

    }
} elseif (Mage::helper('core')->isModuleEnabled('Fooman_SpeedsterAdvanced') && class_exists('Fooman_SpeedsterAdvanced_Block_Page_Html_Head')) {
    abstract class Mirasvit_Seo_Block_Html_Head_Abstract extends Fooman_SpeedsterAdvanced_Block_Page_Html_Head {

    }
} elseif (Mage::helper('core')->isModuleEnabled('Conekta_Card') && class_exists('Conekta_Card_Block_Page_Html_Head')) {
    abstract class Mirasvit_Seo_Block_Html_Head_Abstract extends Conekta_Card_Block_Page_Html_Head {

    }
} elseif (Mage::helper('core')->isModuleEnabled('Aoe_JsCssTstamp') && class_exists('Aoe_JsCssTstamp_Block_Head')) {
    abstract class Mirasvit_Seo_Block_Html_Head_Abstract extends Aoe_JsCssTstamp_Block_Head {

    }
} else {
    abstract class Mirasvit_Seo_Block_Html_Head_Abstract extends Mage_Page_Block_Html_Head {

    }
}

class Mirasvit_Seo_Block_Html_Head extends Mirasvit_Seo_Block_Html_Head_Abstract
{
    protected function _construct()
    {
        parent::_construct();
        $this->setupCanonicalUrl();
        $this->setupAlternateTag();
    }

    public function getConfig()
    {
    	return Mage::getSingleton('seo/config');
    }

    public function getRobots()
    {
        if (!$this->getAction()) {
            return;
        }
        if ($product = Mage::registry('current_product')) {
            if ($robots = Mage::helper('seo')->getMetaRobotsByCode($product->getSeoMetaRobots())) {
                return $robots;
            }
        }
    	$fullAction = $this->getAction()->getFullActionName();
        foreach ($this->getConfig()->getNoindexPages() as $record) {
            //for patterns like filterattribute_(arttribte_code) and filterattribute_(Nlevel)
            if (strpos($record['pattern'], 'filterattribute_(') !== false
                && $fullAction == 'catalog_category_view') {
                    if ($this->_checkFilterPattern($record['pattern'])) {
                         return Mage::helper('seo')->getMetaRobotsByCode($record->getOption());
                    }
            }

            if (Mage::helper('seo')->checkPattern($fullAction, $record->getPattern())
                || Mage::helper('seo')->checkPattern(Mage::helper('seo')->getBaseUri(), $record['pattern'])) {
                return Mage::helper('seo')->getMetaRobotsByCode($record->getOption());
            }
        }

        return parent::getRobots();
    }

    protected function _checkFilterPattern($pattern)
    {
        $urlParams = Mage::app()->getFrontController()->getRequest()->getQuery();
        if (!Mage::getSingleton('catalog/layer')->getFilterableAttributes()) {
            return false;
        }
        $currentFilters = Mage::getSingleton('catalog/layer')->getFilterableAttributes()->getData();
        $filterArr = array();
        foreach ($currentFilters as $filterAttr) {
            if (isset($filterAttr['attribute_code'])) {
                $filterArr[] = $filterAttr['attribute_code'];
            }
        }

        $usedFilters = array();
        if (!empty($filterArr)) {
            foreach ($urlParams as $keyParam => $valParam) {
                if (in_array($keyParam, $filterArr)) {
                    $usedFilters[] = $keyParam;
                }
            }
        }

        if (!empty($usedFilters)) {
            $usedFiltersCount = count($usedFilters);
            if (strpos($pattern, 'level)') !== false) {
                preg_match('/filterattribute_\\((\d{1})level/', trim($pattern), $levelNumber);
                if (isset($levelNumber[1])) {
                    if ($levelNumber[1] == $usedFiltersCount) {
                        return true;
                    }
                }
            }

            foreach($usedFilters as $useFilterVal) {
                if (strpos($pattern, '(' . $useFilterVal . ')') !== false) {
                    return true;
                }
            }
        }

        return false;
    }

    public function setupCanonicalUrl()
    {
    	if (!$this->getConfig()->isAddCanonicalUrl()) {
    		return;
    	}

        if (!$this->getAction()) {
            return;
        }

    	$fullAction = $this->getAction()->getFullActionName();
        foreach ($this->getConfig()->getCanonicalUrlIgnorePages() as $page) {
            if (Mage::helper('seo')->checkPattern($fullAction, $page)
                || Mage::helper('seo')->checkPattern(Mage::helper('seo')->getBaseUri(), $page)) {
                return;
            }
        }

        $productActions = array(
            'catalog_product_view',
            'review_product_list',
            'review_product_view',
            'productquestions_show_index',
        );

        $productCanonicalStoreId = false;
        $useCrossDomain = true;
        if (in_array($fullAction, $productActions)) {
            $product = Mage::registry('current_product');
            if (!$product) {
                return;
            }
            $productCanonicalStoreId = $product->getSeoCanonicalStoreId(); //canonical store id for current product
            $canonicalUrlForCurrentProduct = trim($product->getSeoCanonicalUrl());

            $collection = Mage::getModel('catalog/product')->getCollection()
                ->addFieldToFilter('entity_id', $product->getId())
                ->addStoreFilter()
                ->addUrlRewrite();

            $product      = $collection->getFirstItem();
            $canonicalUrl = $product->getProductUrl();

            if ($canonicalUrlForCurrentProduct) {
                if (strpos($canonicalUrlForCurrentProduct, 'http://') !== false
                    || strpos($canonicalUrlForCurrentProduct, 'https://') !== false) {
                        $canonicalUrl = $canonicalUrlForCurrentProduct;
                        $useCrossDomain = false;
                } else {
                    $canonicalUrlForCurrentProduct = (substr($canonicalUrlForCurrentProduct, 0, 1) == '/') ? substr($canonicalUrlForCurrentProduct, 1) : $canonicalUrlForCurrentProduct;
                    $canonicalUrl = Mage::getBaseUrl() . $canonicalUrlForCurrentProduct;
                }
            }
        } elseif ($fullAction == 'catalog_category_view') {
            $category     = Mage::registry('current_category');
            if (!$category) {
                return;
            }
            $canonicalUrl = $category->getUrl();
        } else {
			$canonicalUrl = Mage::helper('seo')->getBaseUri();
			$canonicalUrl = Mage::getUrl('', array('_direct' => ltrim($canonicalUrl, '/')));
            $canonicalUrl = strtok($canonicalUrl, '?');
        }

        //setup crossdomian URL if this option is enabled
        if ((($crossDomainStore = $this->getConfig()->getCrossDomainStore()) || $productCanonicalStoreId) && $useCrossDomain) {
            if ($productCanonicalStoreId) {
                $crossDomainStore = $productCanonicalStoreId;
            }
            $mainBaseUrl = Mage::app()->getStore($crossDomainStore)->getBaseUrl();
            $currentBaseUrl = Mage::app()->getStore()->getBaseUrl();
            $canonicalUrl = str_replace($currentBaseUrl, $mainBaseUrl, $canonicalUrl);

            if (Mage::app()->getStore()->isCurrentlySecure()) {
                $canonicalUrl = str_replace('http://', 'https://', $canonicalUrl);
            }
        }

        if (false && isset($product)) { //возможно в перспективе вывести это в конфигурацию. т.к. это нужно только в некоторых случаях.
            // если среди категорий продукта есть корневая категория, то устанавливаем ее для каноникал
            $categoryIds = $product->getCategoryIds();

            if (Mage::helper('catalog/category_flat')->isEnabled()) {
                $currentStore = Mage::app()->getStore()->getId();
                foreach (Mage::app()->getStores() as $store) {
                    Mage::app()->setCurrentStore($store->getId());
                    $collection = Mage::getModel('catalog/category')->getCollection()
                        ->addFieldToFilter('entity_id', $categoryIds)
                        ->addFieldToFilter('level', 1);
                    if ($collection->count()) {
                        $mainBaseUrl = $store->getBaseUrl();
                        break;
                    }
                }
                Mage::app()->setCurrentStore($currentStore);
                if (isset($mainBaseUrl)) {
                    $currentBaseUrl = Mage::app()->getStore()->getBaseUrl();
                    $canonicalUrl = str_replace($currentBaseUrl, $mainBaseUrl, $canonicalUrl);
                }
            } else {
                $collection = Mage::getModel('catalog/category')->getCollection()
                        ->addFieldToFilter('entity_id', $categoryIds)
                        ->addFieldToFilter('level', 1);
                if ($collection->count()) {
                    $rootCategory = $collection->getFirstItem();
                    foreach (Mage::app()->getStores() as $store) {
                          if ($store->getRootCategoryId() == $rootCategory->getId()) {
                            $mainBaseUrl = $store->getBaseUrl();
                            $currentBaseUrl = Mage::app()->getStore()->getBaseUrl();
                            $canonicalUrl = str_replace($currentBaseUrl, $mainBaseUrl, $canonicalUrl);
                          }
                    }
                }
            }
        }


        $page = (int)Mage::app()->getRequest()->getParam('p');
        if ($page > 1) {
            $canonicalUrl .= "?p=$page";
        }

        $this->addLinkRel('canonical', $canonicalUrl);
    }

    public function setupAlternateTag()
    {
        if (!$this->getConfig()->isAlternateHreflangEnabled(Mage::app()->getStore()->getStoreId())) {
            return;
        }

        $currentStoreGroup = Mage::app()->getStore()->getGroupId();
        if (Mage::app()->getRequest()->getControllerName() == 'product'
            || Mage::app()->getRequest()->getControllerName() == 'category'
            || Mage::app()->getRequest()->getModuleName() == 'cms') {
                $storesNumberInGroup = 0;
                $storesArray = array();
                foreach (Mage::app()->getStores() as $store)
                {
                   if ($store->getIsActive() && $store->getGroupId() == $currentStoreGroup) {
                        $storesArray[] = $store;
                        $storesNumberInGroup++;
                   }
                }

                if ($storesNumberInGroup > 1 ) { //if a current store is multilanguage
                    $isAlternateAdded = false;
                    if (($cmsPageId = Mage::getSingleton('cms/page')->getPageId())
                        && Mage::app()->getRequest()->getActionName() != 'noRoute') {
                        $cmsStoresIds = Mage::getSingleton('cms/page')->getStoreId();
                        $cmsCollection = Mage::getModel('cms/page')->getCollection()
                                        ->addFieldToSelect('alternate_group')
                                        ->addFieldToFilter('page_id', array('eq' => $cmsPageId))
                                        ->getFirstItem();
                        if(($alternateGroup = $cmsCollection->getAlternateGroup()) && $cmsStoresIds[0] != 0) {
                            $cmsCollection = Mage::getModel('cms/page')->getCollection()
                                        ->addFieldToSelect(array('alternate_group', 'identifier'))
                                        ->addFieldToFilter('alternate_group', array('eq' => $alternateGroup))
                                        ->addFieldToFilter('is_active', true);

                            $table = Mage::getSingleton('core/resource')->getTableName('cms/page_store');
                            $cmsCollection->getSelect()
                                         ->join(array('storeTable' => $table), 'main_table.page_id = storeTable.page_id', array('store_id' => 'storeTable.store_id'));
                            $cmsPages = $cmsCollection->getData();
                            if(count($cmsPages) > 0) {
                                $alternateLinks = array();
                                foreach ($cmsPages as $page) {
                                     $url = Mage::app()->getStore($page['store_id'])->getBaseUrl() . $page['identifier'];
                                     $alternateLinks[$page['store_id']] = $url;
                                }
                                if (count($alternateLinks) > 0) {
                                    //we add something like ?___store=frenchurl if we have the same urls for group
                                    $alternateLinksUnique = array_unique($alternateLinks);
                                    $alternateLinksDifferent = array_diff_key($alternateLinks, $alternateLinksUnique);
                                    $alternateLinksWithTheSameUrl = array_intersect($alternateLinks, $alternateLinksDifferent);
                                    foreach ($alternateLinksWithTheSameUrl as $storeId => $storeUrl) {
                                        if (isset($storesArray[$storeId])) {
                                            $urlAddition = strstr($storesArray[$storeId]->getCurrentUrl(false),"?");
                                            $alternateLinks[$storeId] = $alternateLinks[$storeId] . $urlAddition;
                                        }
                                    }

                                    foreach ($alternateLinks as $storeId => $storeUrl) {
                                        $storeCodeCms = substr(Mage::getStoreConfig('general/locale/code', $storeId),0,2);
                                        $this->addLinkRel('alternate"' . ' hreflang="' . $storeCodeCms, $storeUrl);
                                    }

                                    $isAlternateAdded = true;

                                }
                            }
                        }
                    }

                    if (!$isAlternateAdded) {
                        foreach ($storesArray as $store)
                        {
                           $url =  htmlspecialchars_decode($store->getCurrentUrl(false));
                           $storeCode = substr(Mage::getStoreConfig('general/locale/code', $store->getId()),0,2);
                           $addLinkRel = false;
                           if (Mage::app()->getRequest()->getModuleName() == 'cms'
                               && Mage::app()->getRequest()->getActionName() != 'noRoute') {
                                    $cmsStoresIds = Mage::getSingleton('cms/page')->getStoreId();
                                    if (in_array($store->getId(), Mage::getSingleton('cms/page')->getStoreId())
                                        || (isset($cmsStoresIds[0]) && $cmsStoresIds[0] == 0)) {
                                            $addLinkRel = true;
                                    }
                           }
                           if (Mage::app()->getRequest()->getControllerName() == 'product') {
                                $urlAddition = strstr($url,"?"); //need if we have the same product url for every store, will add something like ?___store=frenchurl
                                $product = Mage::registry('current_product');
                                if (!$product) {
                                    return;
                                }
                                $category = Mage::registry('current_category');
                                $category ? $categoryId = $category->getId() : $categoryId = null;
                                $url = $store->getBaseUrl() . $this->getAlternateProductUrl($product->getId(), $categoryId, $store->getId()) . $urlAddition;
                                $addLinkRel = true;
                           }
                           if (Mage::app()->getRequest()->getControllerName() == 'category') {
                                $collection = Mage::getModel('catalog/category')->getCollection()
                                            ->setStoreId($store->getId())
                                            ->addFieldToFilter('is_active', array('eq'=>'1'))
                                            ->addFieldToFilter('entity_id', array('eq'=>Mage::registry('current_category')->getId()))
                                            ->getFirstItem();
                                if($collection->hasData()) {
                                    $addLinkRel = true;
                                }
                           }
                           if ($addLinkRel) {
                                $this->addLinkRel('alternate"' . ' hreflang="' . $storeCode, $url);
                           }
                        }
                    }
                }
        }
    }

    public function getAlternateProductUrl($productId, $categoryId, $storeId)
    {
        $idPath = sprintf('product/%d', $productId);
        if ($categoryId && $this->getConfig()->getProductUrlFormat() != Mirasvit_Seo_Model_Config::URL_FORMAT_SHORT) {
            $idPath = sprintf('%s/%d', $idPath, $categoryId);
        }
        $urlRewriteObject = Mage::getModel('core/url_rewrite')->setStoreId($storeId)->loadByIdPath($idPath);

        return $urlRewriteObject->getRequestPath();
    }
}
