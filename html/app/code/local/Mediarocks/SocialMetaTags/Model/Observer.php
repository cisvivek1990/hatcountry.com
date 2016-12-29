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

class Mediarocks_SocialMetaTags_Model_Observer 
{
    /**
     * Add social meta attributes to new fieldset
     *
     * @param Varien_Event_Observer $observer
     *
     * @return void
     */
    public function prepareForm(Varien_Event_Observer $observer)
    {
        // hide formfields in backend if module output is disabled (doesn't work!)
        //$class = !Mage::helper('core')->isModuleEnabled('Mediarocks_SocialMetaTags') ? ' hidden' : '';
        
        $form = $observer->getEvent()->getForm();
        $fieldset = $form->addFieldset(
            'social_meta_information',
            array(
                 'legend' => 'Social Meta Information',
                 'class' => 'fieldset-wide'
            )
        );

        $fieldset->addField('fb_share_image', 'image', array(
            'name' => 'fb_share_image',
            'label' => 'Facebook Share Image',
            'title' => 'Facebook Share Image'
        ));

        $fieldset->addField('facebook_meta_title', 'text', array(
            'name' => 'facebook_meta_title',
            'label' => 'Facebook Meta Title',
            'title' => 'Facebook Meta Title',
        ));

        $fieldset->addField('facebook_meta_description', 'textarea', array(
            'name' => 'facebook_meta_description',
            'label' => 'Facebook Meta Description',
            'title' => 'Facebook Meta Description',
        ));

        $fieldset->addField('twitter_share_image', 'image', array(
            'name' => 'twitter_share_image',
            'label' => 'Twitter Card Image',
           	'title' => 'Twitter Card Image'
        ));

        $fieldset->addField('twitter_meta_title', 'text', array(
            'name' => 'twitter_meta_title',
            'label' => 'Twitter Meta Title',
            'title' => 'Twitter Meta Title',
        ));

        $fieldset->addField('twitter_meta_description', 'textarea', array(
            'name' => 'twitter_meta_description',
            'label' => 'Twitter Meta Description',
            'title' => 'Twitter Meta Description',
        ));
    }
    
    /**
     * Save social share images
     *
     * @param Varien_Event_Observer $observer
     *
     * @return void
     */
    public function savePage(Varien_Event_Observer $observer)
    {   
        $model = $observer->getEvent()->getPage();
        $request = $observer->getEvent()->getRequest();		
			
        if (is_array($_FILES['fb_share_image'])) {

            if (isset($_FILES['fb_share_image']['name']) && $_FILES['fb_share_image']['name'] != '') {
                $uploader = new Varien_File_Uploader('fb_share_image');
                $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
                $uploader->setAllowRenameFiles(false);
                $uploader->setFilesDispersion(false);
                
                $mediaFolderName = 'fb_share_image';
                $fileNamePrefix = 'cms_';
                $media_path  = Mage::getBaseDir('media') . DS . $mediaFolderName . DS;
                $uploadResult = $uploader->save($media_path, $fileNamePrefix . $_FILES['fb_share_image']['name']);
                if ($uploadResult) {
                  $model->setFbShareImage($mediaFolderName . DS . $uploadResult['file']);                  
                }
            }
            else {
                $data = $request->getPost();
                
                if (isset($data['fb_share_image']['delete']) && $data['fb_share_image']['delete'] == 1) {
                    $model->setFbShareImage('');
                }
                else {
                    $data = $model->getData();
                    if (isset($data['fb_share_image'])) {
                        $model->setFbShareImage(implode($data['fb_share_image']));
                    }
                }
            }
        }
            
        if (is_array($_FILES['twitter_share_image'])) {

            if (isset($_FILES['twitter_share_image']['name']) && $_FILES['twitter_share_image']['name'] != '') {
                $uploader = new Varien_File_Uploader('twitter_share_image');
                $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
                $uploader->setAllowRenameFiles(false);
                $uploader->setFilesDispersion(false);
                
                $mediaFolderName = 'twitter_share_image';
                $fileNamePrefix = 'cms_';
                $media_path  = Mage::getBaseDir('media') . DS . $mediaFolderName . DS;
                $uploadResult = $uploader->save($media_path, $fileNamePrefix . $_FILES['twitter_share_image']['name']);
                if ($uploadResult) {
                  $model->setTwitterShareImage($mediaFolderName . DS . $uploadResult['file']);                  
                }
            }
            else {
                $data = $request->getPost();
                
                if(isset($data['twitter_share_image']['delete']) && $data['twitter_share_image']['delete'] == 1) {
                    $model->setTwitterShareImage('');
                }
                else {
                    $data = $model->getData();
                    if (isset($data['twitter_share_image'])) {
                        $model->setTwitterShareImage(implode($data['twitter_share_image']));
                    }
                }
            }
        }
    }
    
    /**
     * Update facebook scrape information after prodct has been saved
     *
     * @param Varien_Event_Observer $observer
     *
     * @return void
     */
    public function afterSaveProduct(Varien_Event_Observer $observer)
    {
        $helper = Mage::helper('mediarocks_socialmetatags');
        if (!$helper->isFacebookAutoScrapeEnabled()) {
            return;
        }
        
        $product = $observer->getEvent()->getProduct();
        $productId = $product->getId();
        
        // get product urls from all store views
        $idPath = 'product/' . $productId;
        $productUrls = $helper->getUrlsByPathId($idPath);
        
        if (!count($productUrls)) {
            Mage::getSingleton('adminhtml/session')->addWarning($helper->__("Facebook Scrape Information could not be updated because no URL could be determined for this product"));
            return;
        }
        
        $hasScrapeLimitError = false;
        foreach($productUrls as $url) {
            $scrapeResult = $helper->updateFacebookScrapeInformation($url);
            
            if (!$scrapeResult->error) {
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    $helper->__("Facebook Scrape Information successfully updated for URL %s", $url)
                );
            }
            else {
                Mage::getSingleton('adminhtml/session')->addWarning(
                    $helper->__("An error occured while fetching new facebook scrape information for URL %s. (Error Code: %s, %s: %s)",
                        $url,
                        $scrapeResult->error->code,
                        $scrapeResult->error->type,
                        $scrapeResult->error->message
                    )
                );
                // check if scrape limit reached
                if ($scrapeResult->error->code == 4) {
                  $hasScrapeLimitError = true;
                }
            }
        }
        if ($hasScrapeLimitError && !Mage::getStoreConfig('socialmetatags/facebook/app_token')) {
            Mage::getSingleton('adminhtml/session')->addNotice(
                $helper->__("You can provide a Facebook App-Secret in the extension configuration, to get a higher Application request limit.")
            );
        }
    }
    
    /**
     * Update facebook scrape information after category has been saved
     *
     * @param Varien_Event_Observer $observer
     *
     * @return void
     */
    public function afterSaveCategory(Varien_Event_Observer $observer)
    {
        $helper = Mage::helper('mediarocks_socialmetatags');
        if (!$helper->isFacebookAutoScrapeEnabled()) {
            return;
        }
        
        $category = $observer->getEvent()->getDataObject();
        $categoryId = $category->getId();
        
        // get category urls from all store views
        $idPath = 'category/' . $categoryId;
        $categoryUrls = $helper->getUrlsByPathId($idPath);

        if (!count($categoryUrls)) {
            Mage::getSingleton('adminhtml/session')->addWarning($helper->__("Facebook Scrape Information could not be updated because no URL could be determined for this category"));
            return;
        }
        
        foreach($categoryUrls as $url) {
            $scrapeResult = $helper->updateFacebookScrapeInformation($url);
            
            if (!$scrapeResult->error) {
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    $helper->__("Facebook Scrape Information successfully updated for URL %s", $url)
                );
            }
            else {
                Mage::getSingleton('adminhtml/session')->addError(
                    $helper->__("An error occured while fetching new facebook scrape information for URL %s. (Error Code: %s, %s: %s)",
                        $url,
                        $scrapeResult->error->code,
                        $scrapeResult->error->type,
                        $scrapeResult->error->message
                    )
                );
            }
        }
    }
    
    /**
     * Update facebook scrape information after cms page has been saved
     *
     * @param Varien_Event_Observer $observer
     *
     * @return void
     */
    public function afterSaveCmsPage(Varien_Event_Observer $observer)
    {
        $helper = Mage::helper('mediarocks_socialmetatags');
        if (!$helper->isFacebookAutoScrapeEnabled()) {
            return;
        }
        
        $page = $observer->getObject();
        $pageId = $page->getPageId();
        $pageStores = $page->getStores();
        
        // if cms page is enabled for all stores, we fetch all store ids
        if (in_array(0, $pageStores)) {
            $pageStores = array_keys(Mage::app()->getStores());
        }
        
        // get all cms page urls for each store view
        $pageUrls = array();
        foreach ($pageStores as $storeId) {
            $storeBaseUrl = Mage::app()->getStore($storeId)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);
            $pageUrls[] = $storeBaseUrl . $page->getIdentifier();
        }
        
        if (!count($pageUrls)) {
            Mage::getSingleton('adminhtml/session')->addWarning($helper->__("Facebook Scrape Information could not be updated because no URL could be determined for this page"));
            return;
        }
        
        foreach($pageUrls as $url) {
          
            $scrapeResult = $helper->updateFacebookScrapeInformation($url);
            
            if (!$scrapeResult->error) {
                Mage::getSingleton('adminhtml/session')->addNotice(
                    $helper->__("Facebook Scrape Information successfully updated for URL %s", $url)
                );
            }
            else {
                Mage::getSingleton('adminhtml/session')->addWarning(
                    $helper->__("An error occured while fetching new facebook scrape information for URL %s. (Error Code: %s, %s: %s)",
                        $url,
                        $scrapeResult->error->code,
                        $scrapeResult->error->type,
                        $scrapeResult->error->message
                    )
                );
            }
        }
    }

    /**
     * Shortcut to getRequest
     *
     * @return Mage_Core_Controller_Request_Http
     */
    protected function _getRequest()
    {
        return Mage::app()->getRequest();
    }
}