<?php
/**
 * Media Rocks GbR
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA that is bundled with 
 * this package in the file MEDIAROCKS-LICENSE-COMMUNITY.txt.
 * It is also available through the world-wide-web at this URL:
 * http://solutions.mediarocks.de/MEDIAROCKS-LICENSE-COMMUNITY.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package is designed for Magento COMMUNITY edition. 
 * Media Rocks does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * Media Rocks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please send an email to support@mediarocks.de
 *
 */

class Mediarocks_SocialMetaTags_Model_Head extends Mage_Core_Model_Abstract
{
    /**
     * Default image
     *
     */
    protected $defaultImage;
        
    
    /**
     * Get current URL
     *
     */
    public function getUrl()
    {   
        if (empty($this->_data['url'])) {
            $this->_data['url'] = !in_array(Mage::app()->getFrontController()->getAction()->getFullActionName(), array('cms_index_noRoute', 'cms_index_defaultNoRoute')) ? Mage::helper('core/url')->getCurrentUrl() : '';
        }
        return $this->_data['url'];
    }
    
    /**
     * Get current sitename
     *
     */
    public function getSitename()
    {   
        if (empty($this->_data['sitename'])) {
            $this->_data['sitename'] = Mage::app()->getStore()->getGroup()->getName();
        }
        return $this->_data['sitename'];
    }
    
    /**
     * Get default description
     *
     */
    public function getDescription()
    {
        if (empty($this->_data['description'])) {
            $this->_data['description'] = Mage::getStoreConfig('design/head/default_description');
        }
        return $this->_data['description'];
    }

    /**
     * Get category title
     *
     */
    public function getCategoryTitle()
    {   
        if (empty($this->_data['category_title']) && $currentCategory = Mage::registry('current_category')) {
            $title = $currentCategory->getMetaTitle() ? $currentCategory->getMetaTitle() : $currentCategory->getName();
            $this->_data['category_title'] = strip_tags(htmlspecialchars($title));
        }
        
        return $this->_data['category_title'];
    }
    
    /**
     * Get category description
     *
     */
    public function getCategoryDescription()
    {   
        if (empty($this->_data['category_description']) && $currentCategory = Mage::registry('current_category')) {
            $desc = $currentCategory->getMetaDescription() ? $currentCategory->getMetaDescription() : ($currentCategory->getDescription() ? $currentCategory->getDescription() : $this->getDescription());
            $this->_data['category_description'] = strip_tags(htmlspecialchars($desc));
        }
        
        return $this->_data['category_description'];
    }

    /**
     * Get category image
     *
     * @param string $type ['facebook' or 'twitter']
     * @param boolean $_showImageThumbnail
     */
    public function getCategoryImage($type = 'facebook', $_showImageThumbnail = false)
    {   
        $id = 'category_image_'.$type;
        if (empty($this->_data[$id]) && $currentCategory = Mage::registry('current_category')) {
            
            // get facebook image
            if ($type == 'facebook' && $currentCategory->getFbShareImage() && $currentCategory->getFbShareImage() != 'no_selection') {
                $image = $currentCategory->getFbShareImage();
            }
            // get twitter image
            else if ($type == 'twitter' && $currentCategory->getTwitterShareImage() && $currentCategory->getTwitterShareImage() != 'no_selection') {
                $image = $currentCategory->getTwitterShareImage();
            }
            // get fallback image
            else {
                $image = ($_showImageThumbnail && $currentCategory->getThumbnail()) || !$currentCategory->getImage() ? $currentCategory->getThumbnail() : $currentCategory->getImage();
            }
            
            if ($image) {
                $this->_data[$id] = Mage::getBaseUrl('media').'catalog/category/'. $image;
            }
            else {
                $this->_data[$id] = $this->getFallbackImage($type, 'category');
            }
        }
        return $this->_data[$id];
    }
    
    /**
     * Get product title
     *
     */
    public function getProductTitle()
    {   
        if (empty($this->_data['product_title']) && $currentProduct = Mage::registry('current_product')) {
            $this->_data['product_title'] = strip_tags(htmlspecialchars($currentProduct->getMetaTitle() ? $currentProduct->getMetaTitle() : $currentProduct->getName()));
        }
        
        return $this->_data['product_title'];
    }
    
    /**
     * Get product description
     *
     */
    public function getProductDescription()
    {   
        if (empty($this->_data['product_description']) && $currentProduct = Mage::registry('current_product')) {
            $desc = $currentProduct->getMetaDescription() ? $currentProduct->getMetaDescription() : ($currentProduct->getShortDescription() ? $currentProduct->getShortDescription() : $this->getDescription());
            $this->_data['product_description'] = strip_tags(htmlspecialchars($desc));
        }
        
        return $this->_data['product_description'];
    }
    
    /**
     * Get fallback image (in the order as defined in config)
     * 
     * @param string $type ['facebook' or 'twitter']
     * @param boolean $_showImageThumbnail
     */
    public function getProductFallbackImage($type = 'facebook', $_showImageThumbnail = false)
    {   
        $_imageAttribute = $type == 'facebook' ? 'fb_share_image' : 'twitter_share_image';
        
        $id = 'product_fallback_image_' . $type;
        
        if (!isset($this->_data[$id])) {
            
            $fallbacks = Mage::getStoreConfig('socialmetatags/general/product_img_fallback_order') != 'custom' ? Mage::getStoreConfig('socialmetatags/general/product_img_fallback_order') : Mage::getStoreConfig('socialmetatags/general/product_img_fallback_order_custom');
            
            // append thumbnail attribute if force fallback is enabled
            if ($_showImageThumbnail) {
                $fallbacks = $fallbacks ? 'thumbnail,' . $fallbacks : 'thumbnail';
            }
            
            // append attribute code
            $fallbacks = $fallbacks ? $_imageAttribute . ',' . $fallbacks : $_imageAttribute;
            
            // convert array to string
            $fallbacks = explode(',', $fallbacks);
            
            $currentProduct = Mage::registry('current_product');
            foreach ($fallbacks as $fallback_image_attribute) {
                
                /* bugfix for base image */
                if ($fallback_image_attribute == 'base_image') {
                    $fallback_image_attribute = 'image';
                }
                if ($currentProduct->getData($fallback_image_attribute) && $currentProduct->getData($fallback_image_attribute) != 'no_selection') {
                    
                    $this->_data[$id] = $currentProduct->getMediaConfig()->getMediaUrl($currentProduct->getData($fallback_image_attribute));
                    //$this->_data[$id] = Mage::helper('catalog/image')->init($currentProduct, $fallback_image_attribute); // this gets an image object ?!
                    break;
                }
            }
            // get global fallback image from
            if (!isset($this->_data[$id])) {
                $this->_data[$id] = $this->getFallbackImage($type, 'product');
            }
        }
        return $this->_data[$id];
    }
    
    /**
     * Get fallback social image
     *
     * @param string $type ['facebook' or 'twitter']
     */
    public function getFallbackImage($type = 'facebook', $pageType = 'product')
    {   
        if ($pageType !== 'product') {
          $pageType = '_'. $pageType;
        }
        else {
          $pageType = '';
        }
        $id = 'default_image_'.$type.$pageType;
        if (empty($this->_data[$id]) && $image = Mage::getStoreConfig('socialmetatags/'.$type.'/last_fallback_image'.$pageType)) {
            $this->_data[$id] = Mage::getBaseUrl('media') . 'mrsocialmetatags/' . $image;
        }
        return $this->_data[$id];
    }
    
    /**
     * Get product thumbnail (fallback to placeholder image if set in config)
     * 
     * @param string $type ['facebook' or 'twitter']
     */
    public function getProductThumbnail($type = 'facebook')
    {   
        $id = 'product_thumbnail_'.$type;
        if (empty($this->_data[$id]) && $currentProduct = Mage::registry('current_product')) {
            $this->_data[$id] = $currentProduct->getThumbnail() && $currentProduct->getThumbnail() != 'no_selection' ? Mage::helper('catalog/image')->init($currentProduct, 'thumbnail') : $this->getFallbackImage($type);
        }
        return $this->_data[$id];
    }
    
    /**
     * Get image root path
     *
     * @param string $url
     */
    public function getImageFromUrl($url)
    {   
        $baseUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
        $basePath = Mage::getBaseDir() . '/';
        $imagePath = str_replace($baseUrl, $basePath, $url);
        
        return is_file($imagePath) ? $imagePath : false;
    }


    public function getRequest()
    {
        return Mage::app()->getRequest();
    }

    
    /**
     * Retrieve og:meta Tags
     *
     * @param string <tagName>
     * @return string or array
     */
    public function getOgTags($tagName = '')
    {   
        $tags = array();
        $currentProduct = Mage::registry('current_product');
        $currentCategory = Mage::registry('current_category');
        $helper = Mage::helper('mediarocks_socialmetatags');
        
        // check if facebook is disabled
        if (Mage::getStoreConfig('socialmetatags/facebook/enabled')) {
        
            // Facebook API Key
            if ($app_id = Mage::getStoreConfig('socialmetatags/facebook/app_id')) {
                $tags['fb:app_id'] = $app_id;
            }
            
            // Facebook Admins
            if ($admins = Mage::getStoreConfig('socialmetatags/facebook/admins')) {
                $admins = explode(',', $admins);
                foreach($admins as $admin) {
                    $tags['fb:admins'][] = trim($admin);
                }
            }
            
            $tags['og:url'] = $this->getUrl();
            $tags['og:site_name'] = $this->getSitename();
            
            if ($currentProduct) {
                
                $tags['og:url'] = $currentProduct->getProductUrl();
                $tags['og:title'] = $currentProduct->getFacebookMetaTitle() ? $currentProduct->getFacebookMetaTitle() : $this->getProductTitle();
                $tags['og:description'] = $currentProduct->getFacebookMetaDescription() ? $currentProduct->getFacebookMetaDescription() : $this->getProductDescription();
                $tags['og:image'] = $this->getProductFallbackImage('facebook');
                
                if (Mage::getStoreConfig('socialmetatags/facebook/use_product_type')) {
                    
                    // type
                    $tags['og:type'] = 'product';
                    
                    // availability
                    $tags['product:availability'] = $currentProduct->getIsInStock() ? 'instock' : 'oos';
                    
                    // brand
                    if ($currentProduct->getManufacturer()) {
                        $tags['product:brand'] = $currentProduct->getAttributeText('manufacturer');
                    }
                    
                    // category
                    if ($currentProduct->getCategory()) {
                        $tags['product:category'] = $currentProduct->getCategory()->getName();
                    }
                    
                    // prices
                    $prices = $helper->getPriceIncExclTax($currentProduct);
                    
                    // check if product is bundled and if show price is enabled
                    if ($currentProduct->getTypeId() === Mage_Catalog_Model_Product_Type::TYPE_BUNDLE) {
                        
                        $productPriceType = (int)$helper->getBundledProductPriceType();
                        
                        // show min price
                        if ($productPriceType && $productPriceType === 1) {
                            $tags['product:price:amount'] = $prices['min_price'];
                            $tags['product:price:currency'] = $prices['currency_code'];
                        }
                        // show max price
                        else if ($productPriceType) {
                            $tags['product:price:amount'] = $prices['max_price'];
                            $tags['product:price:currency'] = $prices['currency_code'];
                        }
                    }
                    // normal product
                    else {
                      
                        // normal price
                        if ($prices['regular_price']) {
                            $tags['product:price:amount'] = $prices['regular_price'];
                            $tags['product:price:currency'] = $prices['currency_code'];
                        }
                        // special price
                        if ($prices['final_price']) {
                            
                            $tags['product:sale_price:amount'] = $prices['final_price'];
                            $tags['product:sale_price:currency'] = $prices['currency_code'];
                          
                            // get duration of special price
                            if ($specialpriceFromDate = $currentProduct->getSpecialFromDate()) {
                                $tags['product:sale_price_dates:start'] = date_create_from_format('Y-m-d H:i:s', $specialpriceFromDate)->getTimestamp();
                            }
                            if ($specialpriceToDate = $currentProduct->getSpecialToDate()) {
                                $tags['product:sale_price_dates:end'] = date_create_from_format('Y-m-d H:i:s', $specialpriceToDate)->getTimestamp();
                            }
                        }
                    }
                }
            }
            elseif ($currentCategory) {
                $tags['og:title'] = $currentCategory->getFacebookMetaTitle() ? $currentCategory->getFacebookMetaTitle() : $this->getCategoryTitle();
                $tags['og:description'] = $currentCategory->getFacebookMetaDescription() ? $currentCategory->getFacebookMetaDescription() : $this->getCategoryDescription();
                $tags['og:image'] = $this->getCategoryImage('facebook');
            }
            elseif ($this->getRequest()->getModuleName() == 'cms' && $cmsPage = Mage::getSingleton('cms/page')) {
                $tags['og:title'] = $cmsPage->getFacebookMetaTitle() ? $cmsPage->getFacebookMetaTitle() : $cmsPage->getTitle();
                $desc = $cmsPage->getFacebookMetaDescription() ? $cmsPage->getFacebookMetaDescription() : ($cmsPage->getMetaDescription() ? $cmsPage->getMetaDescription() : $this->getDescription());
                $tags['og:description'] = strip_tags(htmlspecialchars($desc));
                $tags['og:image'] = $cmsPage->getFbShareImage() ? Mage::getBaseUrl('media'). $cmsPage->getFbShareImage() : $this->getFallbackImage('facebook', 'cms');
            }
            else {
                $tags['og:title'] = $this->getTitle();
            }
            
            // add image dimensions so the crawler can render it immediately without having to asynchronously
            if ($tags['og:image'] && $image = $this->getImageFromUrl($tags['og:image'])) {
                
                try {
                    $imageSize = getimagesize($image);
                    if ($imageSize) {
                        $tags['og:image:width'] = $imageSize[0];
                        $tags['og:image:height'] = $imageSize[1];
                    }
                }
                catch (Exception $e) {
                    // ...
                }
            }
            
        }

        if ($currentProduct) {
            if ($properties = Mage::getStoreConfig('socialmetatags/additional/product_properties')) {
                $tags = $helper->parseKeyValuePairs($tags, $currentProduct, $properties);
            }
        }   
        else if ($currentCategory && $properties = Mage::getStoreConfig('socialmetatags/additional/category_properties')) {
            $tags = $helper->parseKeyValuePairs($tags, $currentCategory, $properties);
        }
        
        // return single tag
        if (strlen($tagName)) {
            return isset($tags[$tagName]) ? $tags[$tagName] : '';
        }
        
        // return all tags in array     
        return $tags;
    }


    /**
     * Retrieve twitter:meta Tags
     *
     * @param string <tagName>
     * @return string or array
     */
    public function getTwitterTags($tagName = '')
    {       
        $tags = array();
        $pageType = false;
        $show_image_thumbnail = false;
        $show_title = true;
        $show_description = true;
        $show_image = true;
        $helper = Mage::helper('mediarocks_socialmetatags');
        
        // get page type
        if ($currentProduct = Mage::registry('current_product')) {
           $pageType = "product";
        }
        elseif ($currentCategory = Mage::registry('current_category')) {
           $pageType = "category";
        }
        elseif ($this->getRequest()->getModuleName() == 'cms' && $cmsPage = Mage::getSingleton('cms/page')) {
           $pageType = "cms";
        }
        
        // check if twitter is disabled
        if (Mage::getStoreConfig('socialmetatags/twitter/enabled')) {
            
            
            # Default Tags
            // twitter:url
            $tags['twitter:url'] = $this->getUrl();
            
            // twitter:site:id
            if ($twitterSiteId = Mage::getStoreConfig('socialmetatags/twitter/card_site_id')) {
                $tags['twitter:site:id'] = $twitterSiteId;
            }
            
            // twitter:site
            else if ($twitterSite = Mage::getStoreConfig('socialmetatags/twitter/card_site')) {
                $tags['twitter:site'] = (substr($twitterSite, 0, 1) !== '@') ? '@' . $twitterSite : $twitterSite;
            }
            
            // twitter:creator:id
            if ($twitterCreatorId = Mage::getStoreConfig('socialmetatags/twitter/card_creator_id')) {
                $tags['twitter:creator:id'] = $twitterCreatorId;
            }
            
            // twitter:creator
            else if ($twitterCreator = Mage::getStoreConfig('socialmetatags/twitter/card_creator')) {
                $tags['twitter:creator'] = (substr($twitterCreator, 0, 1) !== '@') ? '@' . $twitterCreator : $twitterCreator;
            }
            
            # Card specific Tags
            switch (Mage::getStoreConfig('socialmetatags/twitter/card_type_' . $pageType)) {
                
                case "summary_card_image":
                case "photo_card":
                case "gallery_card":
                    
                    $tags['twitter:card'] = "summary_large_image";
                    break;
                    
                case "player_card":
                    
                    $tags['twitter:card'] = "player";
                    break;
                    
                case "product_card":
                    
                    $tags['twitter:card'] = "product";
                    
                    $show_image_thumbnail = Mage::getStoreConfig('socialmetatags/twitter/force_thumbnail_product');
                    $contentOptionDataModel = new Mediarocks_SocialMetaTags_Model_System_Config_Source_Twitter_Data();
                    if ($currentProduct) {
                        $entityTypeId = $currentProduct->getEntityTypeId();
                        for ($i = 1; $i <= 2; $i++) {
                            
                            if (!Mage::getStoreConfig('socialmetatags/twitter/card_data_'.$i.'_enabled')) {
                                continue;
                            }
                            
                            $content = Mage::getStoreConfig('socialmetatags/twitter/card_data_'.$i.'_content');
                            unset($attributeCode);
                            unset($value);
                            
                            // prices
                            if ($content == 'lowest_price') {
                                $prices = $helper->getPriceIncExclTax($currentProduct);
                                if ($prices['regular_price'] || $prices['final_price']) {
                                    $value = $prices['final_price'] ? $prices['final_price'] : $prices['regular_price'];
                                }
                            }
                            else if ($content == 'regular_price') {
                                $prices = $helper->getPriceIncExclTax($currentProduct);
                                if ($prices['regular_price']) {
                                    $value = $prices['regular_price'];
                                }
                            }
                            else if ($content == 'special_price') {
                                $prices = $helper->getPriceIncExclTax($currentProduct);
                                if ($prices['final_price']) {
                                    $value = $prices['final_price'];
                                }
                            }
                            else if ($content !== 'custom') {
                                $attributeCode = $content;
                            }
                            else {
                                $attributeCode = Mage::getStoreConfig('socialmetatags/twitter/card_data_'.$i);
                            }
                            
                            if (!$attributeCode) {
                                // normal attributes
                                if ($value && Mage::getStoreConfig('socialmetatags/twitter/card_data_'.$i.'_formatprice')) {
                                    $value = Mage::helper('core')->currency($value, true, false);
                                }
                            }
                            // normal attributes
                            else {
                
                                $attribute = Mage::getModel('eav/entity_attribute')->loadByCode($entityTypeId, $attributeCode);
                                $attributeType = $attribute->getFrontendInput();
                                
                                if (!$attributeType && $attributeCode == 'id') {
                                    $value = $currentProduct->getId();
                                }
                                else if ($attributeCode == 'final_price') {
                                    $value = $currentProduct->getFinalPrice();
                                }
                                else if ($attributeCode == 'is_in_stock') {
                                    $value = Mage::helper('mediarocks_socialmetatags')->__($currentProduct->getIsInStock() ? 'In stock' : 'Out of stock');
                                }
                                else if ($attributeType == 'select') {
                                    $value = $currentProduct->getAttributeText($attributeCode);
                                }
                                else {
                                    $value = $currentProduct->getData($attributeCode);
                                }
                                
                                // format price?
                                if (in_array($attributeCode, array("price","cost","special_price","final_price")) && Mage::getStoreConfig('socialmetatags/twitter/card_data_'.$i.'_formatprice')) {
                                    $value = Mage::helper('core')->currency($value, true, false);
                                }
                            }
                            
                            // add tags
                            if ($value) {
                                $tags['twitter:data'.$i] = is_array($value) ? implode(', ', $value) : $value;
                                $tags['twitter:label'.$i] = Mage::getStoreConfig('socialmetatags/twitter/card_data_'.$i.'_label');
                            }
                        }
                    }
                    break;
                
                default:
                case "summary_card":
                    
                    $tags['twitter:card'] = "summary";
                    $show_image_thumbnail = Mage::getStoreConfig('socialmetatags/twitter/force_thumbnail_summary');
                    break;
            }
            
            
            if ($pageType == 'product') {
                
                $show_title && $tags['twitter:title'] = $currentProduct->getTwitterMetaTitle() ? $currentProduct->getTwitterMetaTitle() : $this->getProductTitle();
                $show_description && $tags['twitter:description'] = $currentProduct->getTwitterMetaDescription() ? $currentProduct->getTwitterMetaDescription() : $this->getProductDescription();
                $show_image && $tags['twitter:image'] = $this->getProductFallbackImage('twitter', $show_image_thumbnail);
            }
            elseif ($pageType == 'category') {
                
                $show_title && $tags['twitter:title'] = $currentCategory->getTwitterMetaTitle() ? $currentCategory->getTwitterMetaTitle() : $this->getCategoryTitle();
                $show_description && $tags['twitter:description'] = $currentCategory->getTwitterMetaDescription() ? $currentCategory->getTwitterMetaDescription() : $this->getCategoryDescription();
                $show_image && $tags['twitter:image'] = $this->getCategoryImage('twitter', $show_image_thumbnail);
            }
            elseif ($pageType == 'cms') {
                
                $show_title && $tags['twitter:title'] = $cmsPage->getTwitterMetaTitle() ? $cmsPage->getTwitterMetaTitle() : $cmsPage->getTitle();
                $desc = $cmsPage->getTwitterMetaDescription() ? $cmsPage->getTwitterMetaDescription() : ($cmsPage->getMetaDescription() ? $cmsPage->getMetaDescription() : $this->getDescription());
                $show_description && $tags['twitter:description'] = strip_tags(htmlspecialchars($desc));
                $show_image && $tags['twitter:image'] = $cmsPage->getTwitterShareImage() ? Mage::getBaseUrl('media') . $cmsPage->getTwitterShareImage() : $this->getFallbackImage('twitter', 'cms');
            }
            
            // twitter allows a maximum of 200 characters for the description
            if ($tags['twitter:description']) {
                $desc = $tags['twitter:description'];
                if (strlen($desc) > 200) {
                    $desc = preg_replace('/^(.*?)\0(.*)$/is', '$1'.' ...', wordwrap($desc, 196, "\0"));
                }
                $tags['twitter:description'] = $desc;
            }
            // add image dimensions to improve twitters proportional resizing of images
            if ($tags['twitter:image'] && $image = $this->getImageFromUrl($tags['twitter:image'])) {
                
                try {
                    $imageSize = getimagesize($image);
                    if ($imageSize) {
                        $tags['twitter:image:width'] = $imageSize[0];
                        $tags['twitter:image:height'] = $imageSize[1];
                    }
                }
                catch (Exception $e) {
                    // ...
                }
            }
        }

        if ($currentProduct) {
            if ($properties = Mage::getStoreConfig('socialmetatags/additional/product_names')) {
                $tags = $helper->parseKeyValuePairs($tags, $currentProduct, $properties);
            }
        }   
        else if ($currentCategory && $properties = Mage::getStoreConfig('socialmetatags/additional/category_names')) {
            $tags = $helper->parseKeyValuePairs($tags, $currentCategory, $properties);
        }
        
        // return single tag
        if (strlen($tagName)) {         
            return isset($tags[$tagName]) ? $tags[$tagName] : '';
        }
        
        // return all tags in array     
        return $tags;
    }
}