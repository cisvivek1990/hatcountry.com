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

require_once(Mage::getModuleDir('controllers','ShopShark_Blog').DS.'Manage' .DS.'CatController.php');

class Mediarocks_SocialMetaTagsForShopSharkBlog_Manage_CatController extends ShopShark_Blog_Manage_CatController
{
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
			
            // upload social images
            foreach(array('fb_share_image', 'twitter_share_image') as $imageId) {
                if(isset($_FILES[$imageId]['name']) and (file_exists($_FILES[$imageId]['tmp_name']))) {
                    try {
                        $result['file'] = '';
                        
                        $uploader = new Varien_File_Uploader($imageId);
                        $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
                        $uploader->setAllowRenameFiles(true);
                        $uploader->setFilesDispersion(false);
                       
                        $path = Mage::getBaseDir('media').DS.'shopshark'.DS.'blog'.DS ;
                                   
                        $result = $uploader->save($path, $_FILES[$imageId]['name']);
                     
                        $data[$imageId] = 'shopshark'.DS.'blog'.DS.$result['file'];
                    }
                    catch(Exception $e) {
                        Mage::getSingleton('adminhtml/session')->addError($e->getMessage() . '  '. $path);
                        Mage::getSingleton('adminhtml/session')->setFormData($data);
                        $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                        return; 
                    }
                } 
                else if(isset($data[$imageId]['delete']) && $data[$imageId]['delete'] == 1) {
                    $data[$imageId] = '';
                }       
                else {
                    unset($data[$imageId]);
                }
                    
                // manipulate post data for the parent action
                $this->getRequest()->setPost($imageId, $data[$imageId]);
            }
        }
        
        // save default stuff
        return parent::saveAction();
    }
}
